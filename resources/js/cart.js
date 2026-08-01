export function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export function jsonHeaders() {
    return {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
    };
}

export async function request(url, options) {
    const response = await fetch(url, options);

    if (! response.ok) {
        throw new Error(`Request to ${url} failed with status ${response.status}`);
    }

    return response.json();
}

export function addToCart(productId, quantity = 1, variationId = null) {
    return request('/cart/items', {
        method: 'POST',
        headers: jsonHeaders(),
        body: JSON.stringify({ product_id: productId, variation_id: variationId, quantity }),
    });
}

export function updateCartItem(itemId, quantity) {
    return request(`/cart/items/${itemId}`, {
        method: 'PATCH',
        headers: jsonHeaders(),
        body: JSON.stringify({ quantity }),
    });
}

export function removeCartItem(itemId) {
    return request(`/cart/items/${itemId}`, {
        method: 'DELETE',
        headers: jsonHeaders(),
    });
}

function dispatchCartChanged(detail) {
    document.dispatchEvent(new CustomEvent('cart:changed', { detail }));
}

function dispatchQuantityPreview(delta) {
    document.dispatchEvent(new CustomEvent('cart:quantity-preview', { detail: { delta } }));
}

export function registerCartComponents(Alpine) {
    Alpine.store('cart', {
        count: Number(document.documentElement.dataset.cartCount ?? 0),
    });

    Alpine.store('errorModal', {
        open: false,
        image: null,
        title: '',
        message: '',
        items: [],
        show({ image = null, title = '', message = '', items = [] }) {
            this.image = image;
            this.title = title;
            this.message = message;
            this.items = items;
            this.open = true;
        },
        close() {
            this.open = false;
        },
    });

    document.addEventListener('cart:changed', (event) => {
        Alpine.store('cart').count = event.detail.itemCount;
    });

    Alpine.data('addToCartButton', (productId) => ({
        loading: false,
        added: false,
        async add(quantity = 1, variationId = null) {
            if (this.loading) {
                return;
            }
            this.loading = true;
            try {
                const data = await addToCart(productId, quantity, variationId);
                dispatchCartChanged(data);
                this.added = true;
                setTimeout(() => (this.added = false), 1500);
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('cartRow', (itemId, quantity, unitPrice, maxAllowed, inStock, productName = '', productImage = null, realStockLimit = null) => ({
        itemId,
        quantity,
        unitPrice,
        maxAllowed,
        inStock,
        productName,
        productImage,
        realStockLimit,
        busy: false,
        notice: '',
        syncTimer: null,
        get lineTotal() {
            return Math.round(this.quantity * this.unitPrice).toLocaleString();
        },
        get atMax() {
            return this.quantity >= this.maxAllowed;
        },
        get lowStock() {
            return this.realStockLimit !== null && this.realStockLimit < 4;
        },
        increment() {
            if (this.atMax) {
                this.showNotice(`Only ${this.maxAllowed} of this item is available.`);
                return;
            }
            this.bump(1);
        },
        decrement() {
            if (this.quantity <= 1) {
                return;
            }
            this.bump(-1);
        },
        // Optimistic: bump the visible quantity/total instantly, then debounce the
        // actual network sync so rapid clicking collapses into one request instead
        // of one per click (that round-trip latency is what felt slow before).
        bump(delta) {
            this.quantity += delta;
            dispatchQuantityPreview(delta * this.unitPrice);
            clearTimeout(this.syncTimer);
            this.syncTimer = setTimeout(() => this.setQuantity(this.quantity), 400);
        },
        async setQuantity(qty) {
            this.busy = true;
            try {
                const data = await updateCartItem(this.itemId, qty);
                const updated = data.items.find((item) => item.id === this.itemId);
                this.quantity = updated ? updated.quantity : qty;
                if (updated) {
                    this.maxAllowed = updated.maxAllowed;
                }
                if (data.notice) {
                    this.showNotice(data.notice);
                }
                dispatchCartChanged(data);
            } finally {
                this.busy = false;
            }
        },
        showNotice(message) {
            this.notice = message;
            setTimeout(() => (this.notice = ''), 3000);
        },
        async remove() {
            if (this.busy) {
                return;
            }
            this.busy = true;
            const data = await removeCartItem(this.itemId);
            dispatchCartChanged(data);
            this.$el.remove();
        },
    }));

    Alpine.data('cartSummary', (initialSubtotal, shippingFee = 0, stockIssues = [], checkoutUrl = '/checkout') => ({
        subtotal: initialSubtotal,
        shippingFee,
        stockIssues,
        checkoutUrl,
        itemCount: Number(document.documentElement.dataset.cartCount ?? 0),
        init() {
            if (this.stockIssues.length > 0) {
                Alpine.store('errorModal').show({
                    title: 'Some items need your attention',
                    message: 'Please resolve these before checking out:',
                    items: this.stockIssues,
                });
            }
        },
        get total() {
            return this.subtotal + (this.itemCount > 0 ? this.shippingFee : 0);
        },
        get isEmpty() {
            return this.itemCount === 0;
        },
        update(detail) {
            this.subtotal = detail.subtotal;
            this.itemCount = detail.itemCount;
            if (detail.shippingCost !== undefined) {
                this.shippingFee = detail.shippingCost;
            }
            if (detail.stockIssues !== undefined) {
                this.stockIssues = detail.stockIssues;
            }
        },
        previewQuantityChange(delta) {
            this.subtotal += delta;
        },
        proceedToCheckout() {
            if (this.stockIssues.length > 0) {
                Alpine.store('errorModal').show({
                    title: 'Some items need your attention',
                    message: 'Please resolve these before checking out:',
                    items: this.stockIssues,
                });
                return;
            }
            window.location.href = this.checkoutUrl;
        },
    }));
}

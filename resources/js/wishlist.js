import { jsonHeaders, request } from './cart';

export function toggleWishlist(productId) {
    return request('/wishlist/toggle', {
        method: 'POST',
        headers: jsonHeaders(),
        body: JSON.stringify({ product_id: productId }),
    });
}

export function registerWishlistComponents(Alpine) {
    Alpine.store('wishlist', {
        count: Number(document.documentElement.dataset.wishlistCount ?? 0),
    });

    Alpine.data('wishlistButton', (productId, initialInWishlist = false) => ({
        inWishlist: initialInWishlist,
        loading: false,
        async toggle() {
            if (this.loading) {
                return;
            }
            this.loading = true;
            try {
                const data = await toggleWishlist(productId);
                this.inWishlist = data.inWishlist;
                Alpine.store('wishlist').count = data.count;
            } finally {
                this.loading = false;
            }
        },
    }));
}

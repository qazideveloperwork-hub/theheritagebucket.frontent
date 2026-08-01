# Heritage Basket — Frontend

Customer-facing storefront for Heritage Basket, an artisanal e-commerce site for Pakistani handcrafted goods. Built from Stitch-generated mockups in `../UI/stitch_responsive_multi_device_interface/`.

This is one of **two separate Laravel apps** sharing one MySQL database:
- `thehertiagebasket/frontend` (this project) — the public storefront.
- `thehertiagebasket/admin` — Filament admin panel for managing products/orders/blog.

## Stack

- Laravel 13, Blade views, Tailwind CSS 3 (`tailwind.config.js` — classic JS config, not v4's CSS `@theme`) + Alpine.js for interactivity.
- **Toolchain note:** downgraded from Laravel's Laravel 13 default (Vite 8 / Tailwind 4) to **Vite 4 + Tailwind 3** because the dev machine runs Node v16.14.0 (EOL), and Vite 8/Tailwind 4 require Node 20+. If Node is ever upgraded to 20+, the toolchain *could* be moved back to v4/Vite 8, but there's no need to — everything works fine on the current setup.
- Cart/checkout use plain `fetch()` AJAX (see `resources/js/cart.js`) + Alpine — no Vue, per explicit decision.
- Design tokens (colors, Playfair Display/Inter fonts, spacing scale) ported 1:1 from the mockups' inline `tailwind.config` into `tailwind.config.js`.

## Local URLs

- **http://theheritagebasket.local** (nginx vhost via Laragon — see `H:\laragon\etc\nginx\sites-enabled\theheritagebasket.local.conf`)
- Or `php artisan serve --port=8000` → http://127.0.0.1:8000

## Database

Shared MySQL database `heritagebasket` (root / no password / 127.0.0.1:3306). **Migrations live in and are only ever run from the `admin` project** — this app has its own copies of the Eloquent models (`app/Models/*`) pointing at the same tables, but never runs `php artisan migrate` itself. If the schema changes, update migrations in `admin`, run them there, then mirror any model changes here.

Cart identity is a long-lived encrypted cookie (`heritage_cart_token`, see `App\Services\CartService`) — no login required to use the cart/checkout.

## What's built

- **Home** (`/`) — hero, shop-by-category bento grid, new arrivals, artisan story, featured products, newsletter.
- **Shop** (`/shop`) — category + max-price filters, sort, pagination.
- **Product detail** (`/products/{slug}`) — gallery, story, artisan bio, care instructions, related products, AJAX add-to-basket.
- **Blog** (`/blog`, `/blog/{slug}`) — featured article + grid, individual article page.
- **Login / Register / Forgot password** — static UI matching the mockups; forms POST and flash a "coming soon" message. **No real authentication** — this was an explicit scope decision, not a bug.
- **Cart** (`/cart`) — real, DB-backed, AJAX quantity update/remove via Alpine (`resources/js/cart.js`).
- **Checkout** (`/checkout`) — real order creation (`orders`/`order_items` tables), COD or bank transfer, no payment gateway.
- **Newsletter** — `POST /newsletter` stores subscriber emails.

## Known limitations / things to revisit

- **Product images are hotlinked** from the Stitch mockup tool's CDN (`lh3.googleusercontent.com/aida-public/...`). These are third-party URLs outside our control — if Google ever revokes/expires them, images will break site-wide. Swap for real product photography (uploaded via the admin Product resource, which stores plain URL strings) when available.
- **Regions were dropped** per an explicit decision — only Category exists as a taxonomy. If a real regional structure is wanted later, it'll need new migrations (run from `admin`) plus frontend UI for a regional bento grid / filter, matching what the original mockups showed for Punjab/Sindh/Balochistan/KPK/Multan.
- No real customer accounts — if real auth is wanted later, build it against a new `users` table (careful: `admin_users` is already taken by the admin app's Filament guard on this shared DB, so don't reuse that table/name).
- Frontend has default Laravel `User` model/migrations from scaffolding — unused, harmless, left in place as framework boilerplate.

## Commands

```bash
npm run dev      # Vite dev server with hot reload
npm run build    # production build (run after any CSS/JS change before shipping)
php artisan serve --port=8000
```
# theheritagebucket.frontent

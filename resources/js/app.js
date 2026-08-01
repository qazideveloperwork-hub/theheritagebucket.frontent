import Alpine from 'alpinejs';
import { registerCartComponents } from './cart';
import { registerFormValidator } from './validation';
import { registerWishlistComponents } from './wishlist';

window.Alpine = Alpine;

registerCartComponents(Alpine);
registerFormValidator(Alpine);
registerWishlistComponents(Alpine);

Alpine.start();

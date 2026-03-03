/**
 * resources/js/menu-print-page.js
 *
 * Vite entry point for the Quick Menu Print admin page.
 * Imports both generators and exposes them on window.MenuPrint so the
 * Alpine.js handler in the Blade view can instantiate them.
 */
import { FoodMenuGenerator, PdfImage } from './menu-generator/FoodMenuGenerator.js';
import { DrinkMenuGenerator } from './menu-generator/DrinkMenuGenerator.js';

window.MenuPrint = {
    FoodMenuGenerator,
    DrinkMenuGenerator,
    PdfImage,
};

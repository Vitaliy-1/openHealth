import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './node_modules/flowbite/**/*.js', // Add this line
    ],
    darkMode: 'class',
    theme: {
        // Your theme settings
    },
    plugins: [
        forms,
        typography,
        require('flowbite/plugin') // Add Flowbite plugin
    ],
};

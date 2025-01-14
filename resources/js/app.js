import 'flowbite';
import './bootstrap';
import './common';
import './index';

// import { initFlowbite } from 'flowbite';

import Datepicker from 'flowbite-datepicker/Datepicker';

(function () {
    Datepicker.locales.uk = {
        days: ["Неділя", "Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота"],
        daysShort: ["Нед", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
        daysMin: ["Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
        months: ["Cічень", "Лютий", "Березень", "Квітень", "Травень", "Червень", "Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень"],
        monthsShort: ["Січ", "Лют", "Бер", "Кві", "Тра", "Чер", "Лип", "Сер", "Вер", "Жов", "Лис", "Гру"],
        today: "Сьогодні",
        clear: "Очистити",
        format: "dd.mm.yyyy",
        weekStart: 1
    };
}());
const initializeDatepickers = () => {
    const datepickerElements = document.querySelectorAll('.default-datepicker');

    datepickerElements.forEach(element => {
        if (!element.classList.contains('datepicker-initialized')) {
            const minDate = element.getAttribute('data-min') ? new Date(element.getAttribute('data-min')) : null;
            const maxDate = element.getAttribute('data-max') ? new Date(element.getAttribute('data-max')) : null;

            const datepicker = new Datepicker(element, {
                format: 'yyyy-mm-dd',
                language: 'uk',
                minDate: minDate,
                maxDate: maxDate,
            });

            element.classList.add('datepicker-initialized');

            element.addEventListener('changeDate', function(event) {
                const selectedDate = event.target.value;
                const wireModel = element.getAttribute('wire:model');
                const componentId = element.closest('[wire\\:id]').getAttribute('wire:id');
                if (Livewire.find(componentId)) {
                    Livewire.find(componentId).set(wireModel, selectedDate);
                }
            });
        }
    });
};

document.addEventListener('livewire:load', () => {
    initializeDatepickers();

    Livewire.hook('message.sent', (message) => {
        if (message.actionQueue[0].payload.method === 'update') {
            document.getElementById('preloader').style.display = 'block';
        }
    });
    Livewire.hook('message.processed', (message) => {
        if (message.actionQueue[0].payload.method === 'update') {
            document.getElementById('preloader').style.display = 'none';
        }
    });
});


document.addEventListener("livewire:initialized", () => {
    initializeDatepickers();

    Livewire.hook('element.init', ({ component, el }) => {
        initializeDatepickers();
    })
});

// See Flowbite instruction on the dark mode switcher: https://flowbite.com/docs/customize/dark-mode/
(function () {
    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    // Change the icons inside the button based on previous settings
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        themeToggleLightIcon.classList.remove('hidden');
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
    }

    var themeToggleBtn = document.getElementById('theme-toggle');

    themeToggleBtn.addEventListener('click', function() {

        // toggle icons inside button
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // if set via local storage previously
        if (localStorage.getItem('color-theme')) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }

            // if NOT set via local storage previously
        } else {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }
    });
})()

import.meta.glob([
    '../images/**',
]);

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
            const datepicker = new Datepicker(element, {
                format: 'yyyy-mm-dd',
                language: 'uk'
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

document.addEventListener('livewire:initialRender', function() {
    initializeDatepickers();
    console.log('livewire:render');

});

document.addEventListener('livewire:render', function() {
    initializeDatepickers();
    console.log('livewire:update');
});
import.meta.glob([
    '../images/**',
]);

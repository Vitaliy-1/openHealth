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
// document.addEventListener('livewire:navigated', () => {
//     initFlowbite();
// })
document.addEventListener('DOMContentLoaded', () => {
    const datepickerElements = document.querySelectorAll('.default-datepicker');

    datepickerElements.forEach(element => {
        const datepicker = new Datepicker(element, {
            format: 'yyyy-mm-dd',
            language: 'uk'
        });

        element.addEventListener('changeDate', function(event) {
            const selectedDate = event.target.value;

            const wireModel = element.getAttribute('wire:model.live');

            const componentId = element.closest('[wire\\:id]').getAttribute('wire:id');

            if (Livewire.find(componentId)) {
                Livewire.find(componentId).set(wireModel, selectedDate);
            }
        });
    });
});


import.meta.glob([
    '../images/**',
]);

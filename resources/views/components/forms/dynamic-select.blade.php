@props(['options', 'property' => ''])

<div x-data="{
        open: false,
        search: '',
        selectedId: @entangle($property).live,
        options: @js($options),
        get filteredOptions() {
            if (this.search === '') return this.options;
            return Object.values(this.options).filter(option =>
                option.toLowerCase().includes(this.search.toLowerCase())
            );
        },
         get selectedOption() {
            return this.options[this.selectedId] || null;
        },
        selectOption(option, index) {
            this.selectedId = index;
            this.open = false;
        }

    }"
>
    <div class="relative mt-2">
        <button @click="open = !open" type="button"
                class="default-input text-left">
            <span class="block truncate" x-text="selectedOption ? selectedOption : 'Обрати '"></span>
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="h-5 w-5 text-gray-800" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
                          clip-rule="evenodd"/>
                </svg>
            </span>
        </button>
        <div x-show="open" @click.away="open = false" x-cloak
             class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg">
            <input type="text" x-model="search" placeholder="Пошук..." class="default-input">
            <ul class="max-h-60 overflow-auto">
                <template x-for="(option, index) in filteredOptions">
                    <li @click="selectOption(option, index)"
                        class="cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-gray-100 hover:text-gray-800"
                        :class="{'bg-sky-600 text-white': selectedId === index}">
                        <span x-text="option" class="font-normal block truncate"></span>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</div>

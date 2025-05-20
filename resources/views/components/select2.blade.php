@props(['modelPath', 'dictionaryName'])

<div x-data="selectComponent('{{ $dictionaryName }}')"
     x-modelable="selected"
     x-model="{{ $modelPath }}"
     @click.away="hideOptions"
>
    <input class="input-modal"
           type="search"
           placeholder="{{ __('forms.select') }}"
           x-model="search"
           @input="showOptions"
           id="{{ $attributes['id'] ?? '' }}"
           autocomplete="off"
           role="combobox"
    />

    <div class="relative">
        <div x-show="optionsVisible"
             class="absolute z-50 border p-2 overflow-y-scroll bg-white dark:bg-gray-800 dark:text-white max-h-60 grid"
        >
            <template x-for="(option, index) in filteredOptions" :key="index + option.value">
                <a @click="selectOption(option)"
                   x-html="highlight('[' + option.value + '] - ' + option.label)"
                   class="cursor-pointer px-2"
                ></a>
            </template>

            <div x-show="filteredOptions.length === 0" class="px-2 py-1 text-gray-500">
                {{ __('forms.nothing_found') }}
            </div>
        </div>
    </div>
</div>

<script>
    function selectComponent(dictionaryKey) {
        return {
            search: '',
            selected: '',
            optionsVisible: false,
            options: [],

            init() {
                const rawData = this.$wire.dictionaries?.[dictionaryKey] ?? {};

                if (dictionaryKey === 'eHealth/LOINC/observation_codes') {
                    const codeMap = this.$wire.observationCodeMap;
                    const allowedCodes = codeMap.laboratory ?? [];

                    this.options = Object.entries(rawData)
                        .filter(([value]) => allowedCodes.includes(value))
                        .map(([value, label]) => ({value, label}));
                } else if (dictionaryKey === 'eHealth/ICF/classifiers') {
                    // Initialize the options according to the initial value
                    this.updateIcfOptions(rawData);

                    this.$watch('modalObservation.categories.coding[0].code', () => {
                        this.updateIcfOptions(rawData);
                    });
                } else {
                    this.options = Object.entries(rawData).map(([value, label]) => ({value, label}));
                }

                this.watchSelected();
            },

            updateIcfOptions(rawData) {
                const categoryCode = this.modalObservation?.categories.coding[0]?.code;

                // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/17483628678/D020.+ISO+9999#1.1.3-%D0%A1%D1%82%D1%80%D1%83%D0%BA%D1%82%D1%83%D1%80%D0%B0-%D0%BA%D0%BE%D0%B4%D1%96%D0%B2-%D0%9C%D0%9A%D0%A4
                const prefixMap = {
                    functions: 'b',
                    structures: 's',
                    activities: 'd',
                    environmental: 'e'
                };

                const prefix = prefixMap[categoryCode] ?? null;

                this.options = Object.entries(rawData)
                    .filter(([key]) => {
                        // filter by prefix
                        return key.startsWith(prefix);
                    })
                    .map(([value, label]) => ({value, label}));
            },

            showOptions() {
                this.optionsVisible = true;
            },

            hideOptions() {
                this.optionsVisible = false;
            },

            selectOption(option) {
                this.selected = option.value;
                this.search = '[' + option.value + '] – ' + option.label;
                this.hideOptions();
            },

            highlight(text) {
                const escaped = this.search.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
                const re = new RegExp(escaped, 'gi');

                return text.replace(re, match => `<span class='bg-purple-300'>${match}</span>`);
            },

            get filteredOptions() {
                if (!this.search) return this.options;

                return this.options.filter(option =>
                    (option.label + option.value).toLowerCase().includes(this.search.toLowerCase())
                );
            },

            watchSelected() {
                this.$watch('selected', (value) => {
                    if (value === undefined || value === null || value === '') {
                        this.search = '';
                    } else {
                        const opt = this.options.find(option => option.value === value);
                        if (opt) {
                            this.search = '[' + opt.value + '] – ' + opt.label;
                        }
                    }
                });
            }
        }
    }
</script>
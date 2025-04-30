@props(['modelPath', 'dictionary' => []])

@php
    $options = collect($dictionary)->map(function($option, $key) {
        return ['label' => $option, 'value' => $key];
    })->values();
@endphp

<div x-data="selectComponent(@js($options))"
     x-init="init()"
     x-modelable="selected"
     x-model="{{ $modelPath }}"
     @click.away="hideOptions"
     :x-model-init="{{ $modelPath }}"
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
            <template x-for="option in filteredOptions" :key="option.value">
                <a @click="selectOption(option)"
                   x-html="highlight(option.value + ' - ' + option.label)"
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
    function selectComponent(options) {
        return {
            search: '',
            selected: '',
            optionsVisible: false,
            options,

            init() {
                this.watchSelected();
                this.selected = this.$el.getAttribute('x-model-init') || '';
            },

            showOptions() {
                this.optionsVisible = true;
            },

            hideOptions() {
                this.optionsVisible = false;
            },

            selectOption(option) {
                this.selected = option.value;
                this.search = option.value + ' – ' + option.label;
                this.hideOptions();
            },

            highlight(text) {
                const escaped = this.search.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
                const re = new RegExp(escaped, 'gi');

                return text.replace(re, match => `<span class='bg-yellow-200'>${match}</span>`);
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
                            this.search = opt.value + ' – ' + opt.label;
                        }
                    }
                });
            }
        }
    }
</script>

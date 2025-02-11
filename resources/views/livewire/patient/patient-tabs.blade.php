@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
@endphp

<div>
    <div aria-hidden="true" class="hidden">
        {!! $svgSprite !!}
    </div>

    <section>
        <x-section-navigation x-data="{ showFilter: true }" class="breadcrumb-form">
            <x-slot name="title">
                {{ $patient['last_name'] }} {{ $patient['first_name'] }} {{ $patient['second_name'] ?? '' }}
            </x-slot>
            <x-slot name="navigation">

                <div class="sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700 mb-8">
                    <button type="button"
                            class="flex items-center gap-2 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        <svg width="16" height="16">
                            <use xlink:href="#svg-plus"></use>
                        </svg>
                        {{ __('Розпочати взаємодію') }}
                    </button>
                </div>

                <div>
                    <!-- Navigation -->
                    <nav class="bg-[#EEEEEE]">
                        <div class="mx-auto overflow-hidden">
                            <ul class="flex items-baseline font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                                @foreach($navTabs as $key => $tab)
                                    <li>
                                        <button wire:click="switchTab('{{ $key }}')"
                                                class="m-0 {{ $activeTab === $key ? 'default-button' : 'text-gray-500 pl-3' }}"
                                        >
                                            {{ $tab['title'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </nav>

                    <!-- Show active livewire component -->
                    <div class="mt-6">
                        @isset($navTabs[$activeTab])
                            @livewire($navTabs[$activeTab]['component'], ['patient' => $patient, 'key' => $activeTab])
                        @endisset
                    </div>
                </div>
            </x-slot>
        </x-section-navigation>
    </section>
</div>

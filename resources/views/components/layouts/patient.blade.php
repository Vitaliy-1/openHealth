<section>
    <x-section-navigation x-data="{ showFilter: true }" class="breadcrumb-form">
        <x-slot name="title">
            {{ $lastName }} {{ $firstName }} {{ $secondName ?? '' }}
        </x-slot>

        <x-slot name="navigation">
            <div class="sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700 mb-8">
                <button type="button"
                        class="flex items-center gap-2 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    <svg width="16" height="16">
                        <use xlink:href="#svg-plus"></use>
                    </svg>
                    {{ __('patients.start_interacting') }}
                </button>
            </div>

            <nav x-data="{ currentPath: window.location.pathname }">
                {{-- Mobile version --}}
                <div class="sm:hidden">
                    <label for="tabs" class="sr-only"></label>
                    <select id="tabs"
                            x-model="currentPath"
                            @change="window.location.href = $event.target.value"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        @php
                            $navItems = [
                                'patient-data' => 'patients.patient_data',
                                'summary' => 'patients.summary',
                                'episodes' => 'patients.episodes'
                            ];
                        @endphp

                        @foreach($navItems as $route => $translation)
                            <option value="{{ route('patient.' . $route, ['id' => $id]) }}"
                                    :selected="currentPath.includes('{{ $route }}')"
                            >
                                {{ __($translation) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Desktop version --}}
                <ul class="hidden text-sm font-medium text-center text-gray-500 rounded-lg shadow-sm sm:flex dark:divide-gray-700 dark:text-gray-400">
                    @foreach($navItems as $route => $translation)
                        <li class="w-full focus-within:z-10">
                            <a href="{{ route('patient.' . $route, ['id' => $id]) }}"
                               x-on:click="currentPath = '{{ route('patient.' . $route, ['id' => $id]) }}'"
                               class="inline-block w-full p-4 border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                               :class="currentPath.includes('{{ $route }}')
                                   ? 'text-gray-900 bg-gray-100 dark:bg-gray-700 dark:text-white'
                                   : 'bg-white hover:text-gray-700 hover:bg-gray-50 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700'"
                            >
                                {{ __($translation) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </x-slot>
    </x-section-navigation>

    {{ $slot }}
</section>

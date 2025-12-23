<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Робочий простір') }}
            </h2>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500 hidden sm:inline">{{ __('Заклад:') }}</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-gray-700 px-3 py-1 rounded-full border border-indigo-100 dark:border-gray-600">
                    {{ $legalEntity->name }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="w-full space-y-6">

            <div class="relative rounded-2xl shadow-md overflow-hidden bg-indigo-600 text-white">
                {{-- Background scenery (Totally isolated) --}}
                <div class="absolute inset-0 z-0">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-blue-600"></div>
                    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-white opacity-5"></div>
                    <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-blue-300 opacity-10"></div>
                </div>

                <div class="relative z-10 p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-white">
                            Вітаємо, {{ Auth::user()->person->first_name ?? Auth::user()->party->first_name ?? 'Колего' }}!
                        </h1>
                        <div class="mt-2 text-indigo-100 text-base max-w-3xl">
                            @if($viewAs)
                                <span class="bg-yellow-400 text-black text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide mr-2 shadow-sm align-middle">Debug: {{ $viewAs }}</span>
                            @endif

                            @if($this->hasAccess('hr') && (!empty($hrStats['pending_count']) || !empty($hrStats['unverified_parties'])))
                                Є завдання:
                                @if(!empty($hrStats['pending_count']))
                                    <span class="font-bold text-white underline decoration-yellow-400 underline-offset-4">{{ $hrStats['pending_count'] }} запитів</span>
                                @endif
                                @if(!empty($hrStats['pending_count']) && !empty($hrStats['unverified_parties'])) та @endif
                                @if(!empty($hrStats['unverified_parties']))
                                    <span class="font-bold text-white underline decoration-red-400 underline-offset-4">{{ $hrStats['unverified_parties'] }} верифікацій</span>
                                @endif
                            @elseif($this->hasAccess('doctor'))
                                Гарного робочого дня. Наступний пацієнт о <span class="font-bold text-white">{{ $doctorStats['next_patient_time'] }}</span>.
                            @else
                                Панель керування медичним закладом.
                            @endif
                        </div>
                    </div>
                    <div class="hidden md:block text-right">
                        <div class="text-3xl font-light text-white">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
                        <div class="text-xs font-medium text-indigo-200 uppercase tracking-wider">{{ \Carbon\Carbon::now()->translatedFormat('d F, l') }}</div>
                    </div>
                </div>
            </div>

            {{-- 2. DOCTOR'S SECTION --}}
            @if($this->hasAccess('doctor'))
                <div>
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ __('Робота з пацієнтами') }}
                    </h3>

                    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">

                        {{-- Left Column (1/4 width on big screens) --}}
                        <div class="space-y-6 xl:col-span-1">
                            {{-- Next Patient --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">Наступний візит</p>
                                <div class="flex justify-between items-center">
                                    <div class="min-w-0">
                                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $doctorStats['next_patient_time'] }}</h4>
                                        <p class="text-gray-600 dark:text-gray-400 truncate text-sm">{{ $doctorStats['next_patient_name'] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 space-y-3">
                                <a href="{{ route('patient.create', ['legalEntity' => $legalEntity->id]) }}" class="w-full py-2.5 px-4 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    <span>Новий пацієнт</span>
                                </a>
                                <a href="{{ route('patient.index', ['legalEntity' => $legalEntity->id]) }}" class="w-full py-2.5 px-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <span>Знайти пацієнта</span>
                                </a>
                            </div>
                        </div>

                        {{-- Right Column (3/4 width on big screens) --}}
                        <div class="xl:col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800 dark:text-white">Останні пацієнти</h3>
                                <a href="{{ route('patient.index', ['legalEntity' => $legalEntity->id]) }}" class="text-xs font-medium text-indigo-600 hover:underline">Всі пацієнти &rarr;</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-3">Час</th>
                                        <th class="px-6 py-3">ПІБ</th>
                                        <th class="px-6 py-3 hidden md:table-cell">Причина</th>
                                        <th class="px-6 py-3 text-right">Дія</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($doctorStats['recent_patients'] ?? [] as $patient)
                                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <td class="px-6 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ $patient['time'] }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-700 dark:text-gray-300">
                                                {{ $patient['name'] }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $patient['reason'] }}</span>
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase">Картка</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. HR SECTION (Human Resources) --}}
            @if($this->hasAccess('hr'))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Card 1: Incoming Requests --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Запити на створення
                            </h3>
                            @if(($hrStats['pending_count'] ?? 0) > 0)
                                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-bold">
                                {{ $hrStats['pending_count'] }}
                            </span>
                            @endif
                        </div>

                        <div class="flex-grow divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentRequests as $req)
                                <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex justify-between items-center">
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $req->party?->fullName ?? 'Без імені' }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $req->position }}</p>
                                    </div>
                                    <a href="{{ route('employee-request.edit', ['employee_request' => $req->id, 'legalEntity' => $legalEntity->id]) }}"
                                       class="px-3 py-1 text-xs font-bold text-white bg-indigo-600 rounded hover:bg-indigo-700 transition">
                                        Дія
                                    </a>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400 text-sm flex flex-col items-center justify-center h-full">
                                    Немає нових запитів
                                </div>
                            @endforelse
                        </div>

                        <div class="p-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 text-center">
                            <a href="{{ route('employee.index', ['legalEntity' => $legalEntity->id, 'status' => ['NEW']]) }}"
                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 uppercase tracking-wide">
                                Всі запити &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Data verification --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Верифікація (Parties)
                            </h3>
                            @if(($hrStats['unverified_parties'] ?? 0) > 0)
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">
                                {{ $hrStats['unverified_parties'] }}!
                            </span>
                            @endif
                        </div>

                        <div class="p-6 flex-grow flex flex-col items-center justify-center text-center">
                            @if(($hrStats['unverified_parties'] ?? 0) > 0)
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Необхідна верифікація</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 max-w-xs">
                                    {{ $hrStats['unverified_parties'] }} осіб мають статус "Не верифіковано".
                                </p>
                                <a href="{{ route('party.verification.index', ['legalEntity' => $legalEntity->id]) }}"
                                   class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-md transition">
                                    Перейти до списку
                                </a>
                            @else
                                <div class="text-green-500 mb-2">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Всі дані верифіковано</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- 4. OWNER'S SECTION --}}
            @if($this->hasAccess('owner'))
                <div>
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        {{ __('Аналітика закладу') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-emerald-500 flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Активні декларації</p>
                                <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($ownerStats['total_declarations'] ?? 0) }}</p>
                            </div>
                            <div class="mt-2 text-[10px] text-gray-400 flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Дані з ЕСОЗ
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-blue-500 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Штат лікарів</p>
                                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $ownerStats['active_doctors'] ?? 0 }}</p>
                                </div>
                                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded text-blue-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                            </div>
                            <a href="{{ route('employee.index', ['legalEntity' => $legalEntity->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline mt-2">
                                Управління персоналом &rarr;
                            </a>
                        </div>

                        <a href="{{ route('legal-entity.details', ['legalEntity' => $legalEntity->id]) }}" class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:border-indigo-500 hover:shadow-md transition flex flex-col items-center justify-center text-center cursor-pointer group">
                            <div class="mb-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-full group-hover:bg-indigo-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <span class="font-bold text-gray-700 dark:text-gray-200 group-hover:text-indigo-600">Налаштування</span>
                            <span class="text-[10px] text-gray-500">Ліцензії, контакти</span>
                        </a>
                    </div>
                </div>
            @endif

        </div>

        {{-- DEBUG PANEL --}}
        <div class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-2 text-[10px] z-50 opacity-90 hover:opacity-100 transition shadow-lg border-t border-gray-700">
            <div class="w-full px-4 flex justify-between items-center">
                <span class="font-mono text-gray-400">DEV MODE</span>
                <div class="flex space-x-2">
                    <a href="{{ request()->url() }}" class="px-2 py-1 bg-gray-800 hover:bg-gray-700 rounded {{ !$viewAs ? 'bg-indigo-600 text-white' : '' }}">REAL</a>
                    <a href="{{ request()->fullUrlWithQuery(['view_as' => 'owner']) }}" class="px-2 py-1 bg-gray-800 hover:bg-gray-700 rounded {{ $viewAs === 'owner' ? 'bg-indigo-600 text-white' : '' }}">OWNER</a>
                    <a href="{{ request()->fullUrlWithQuery(['view_as' => 'hr']) }}" class="px-2 py-1 bg-gray-800 hover:bg-gray-700 rounded {{ $viewAs === 'hr' ? 'bg-indigo-600 text-white' : '' }}">HR</a>
                    <a href="{{ request()->fullUrlWithQuery(['view_as' => 'doctor']) }}" class="px-2 py-1 bg-gray-800 hover:bg-gray-700 rounded {{ $viewAs === 'doctor' ? 'bg-indigo-600 text-white' : '' }}">DOCTOR</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Пошук пацієнта') }}
    </h2>
</x-slot>

<form wire:submit="search">
    <div class="space-y-12 mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-6 lg:px-8 mt-8 mb-8">
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Інформація про пацієнта</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Будь ласка, внесіть детальні дані по пацієнту для пошуку в базі даних ЕСОЗ.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="first-name" class="block text-sm font-medium leading-6 text-gray-900">{{__('patients.firstName')}}</label>
                    <div class="mt-2">
                        <input wire:model="firstName" type="text" name="first-name" id="first-name" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="last-name" class="block text-sm font-medium leading-6 text-gray-900">{{__('patients.lastName')}}</label>
                    <div class="mt-2">
                        <input wire:model="lastName" type="text" name="last-name" id="last-name" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <label for="second-name" class="block text-sm font-medium leading-6 text-gray-900">{{__('patients.secondName')}}</label>
                    <div class="mt-2">
                        <input wire:model="secondName" type="text" name="second-name" id="second-name" autocomplete="second-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Імейл</label>
                    <div class="mt-2">
                        <input wire:model="email" id="email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="birth-date" class="block text-sm font-medium leading-6 text-gray-900">Дата народження</label>
                    <div class="mt-2">
                        <input wire:model="birthDate" type="date" name="birth-date" id="birth-date" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-2 sm:col-start-1">
                    <label for="ipn" class="block text-sm font-medium leading-6 text-gray-900">ІПН</label>
                    <div class="mt-2">
                        <input wire:model="ipn" type="number" name="ipn" id="ipn" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="tel" class="block text-sm font-medium leading-6 text-gray-900">Номер телефону</label>
                    <div class="mt-2">
                        <input wire:model="phone" type="tel" name="tel" id="tel" autocomplete="tel" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="birth-certificate" class="block text-sm font-medium leading-6 text-gray-900">Номер свідотства про народження</label>
                    <div class="mt-2">
                        <input wire:model="birthCertificate" type="number" name="birth-certificate" id="birth-certificate" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
            </div>
        </div>

    <div class="mt-6 flex items-center justify-end gap-x-6">
        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Пошук</button>
    </div>

    <div wire:loading wire:target="search" class="bg-white rounded-md border shadow px-6 py-3">
        <span class="text-gray-700 font-semibold">Шукаємо пацієнта в ЕСОЗ ...</span>
    </div>

    @if(!empty($patients))
            <div class="overflow-y-scroll shadow sm:rounded-lg">
                <table class="table-auto w-full shadow border border-gray-200 dark:border-gray-700 sm:rounded-lg bg-white">
                    <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">Дія</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">{{__('patients.firstName')}}</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">Прізвищe</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">По батькові</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">Дата народження</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">Стать</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">ІПН</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">Номер телефону</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">Місто народження</th>
                        <th class="px-6 py-3 text-left text-xs font-bold whitespace-nowrap text-gray-600 uppercase">Країна</th>
                    </tr>
                    </thead>
                    @foreach($patients as $patient)
                        <tr class="border-solid border-t">
                            <td class="px-6 py-4">
                                <a class="font-medium text-sm text-indigo-600 cursor-pointer" wire:click="import" id="import-patient">Додати</a>
                            </td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['first_name'] }}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['last_name'] }}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium"> {{ $patient['second_name'] }}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['birth_date'] }}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['gender'] }}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['tax_id'] }}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['phones'][0]['number']}}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['birth_settlement'] }}</td>
                            <td class="px-6 py-4 opacity-50 whitespace-nowrap text-sm font-medium">{{ $patient['birth_country'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
    @elseif(is_array($patients))
        <div class="bg-white border shadow">
            Результатів не знайдено
        </div>
    @endif
</form>

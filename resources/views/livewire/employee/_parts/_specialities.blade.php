<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              x-data="{
                  specialities: $wire.entangle('form.specialities'),
                  openModal: false,
                  modalSpeciality: new Speciality(),
                  newSpeciality: false,
                  item: 0,
                  specDict: $wire.dictionaries['SPECIALITY_TYPE'],
                  levelDict: $wire.dictionaries['SPECIALITY_LEVEL']
              }"
    >

            <h5 class="mb-5 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ __('forms.specialities') }}
            </h5>

            <table class="table-input w-inherit">
                <thead class="thead-input">
                <tr>
                    <th class="th-input">{{ __('Спеціальність') }}</th>
                    <th class="th-input">{{ __('Орган що видав') }}</th>
                    <th class="th-input">{{ __('Рівень спеціальності') }}</th>
                    <th class="th-input">{{ __('Номер свідоцтва') }}</th>
                    <th class="th-input">{{ __('forms.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <template x-for="(speciality, index) in specialities" :key="index">
                    <tr>
                        <td class="td-input" x-text="specDict[speciality.speciality] || speciality.speciality"></td>
                        <td class="td-input" x-text="speciality.attestation_name"></td>
                        <td class="td-input" x-text="levelDict[speciality.level] || speciality.level"></td>
                        <td class="td-input" x-text="speciality.certificate_number"></td>
                        <td class="td-input">
                            <div class="flex space-x-2">
                                <button @click.prevent="openModal = true; item = index; modalSpeciality = new Speciality(speciality); newSpeciality = false"
                                        class="text-blue-600 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </button>
                                <button @click.prevent="specialities.splice(index, 1)"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>

            <div class="mt-6">
                <button @click.prevent="openModal = true; newSpeciality = true; modalSpeciality = new Speciality()"
                        class="item-add my-5">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                    </svg>
                    {{__('forms.addSpeciality')}}
                </button>
            </div>

    </fieldset>
</div>

<script>
    class Speciality {
        speciality = '';
        attestation_name = '';
        level = '';
        certificate_number = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

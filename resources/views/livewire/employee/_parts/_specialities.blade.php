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

        <table class="table-input w-full">
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
                                ✎
                            </button>
                            <button @click.prevent="specialities.splice(index, 1)"
                                    class="text-red-600 hover:text-red-800">
                                ✕
                            </button>
                        </div>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>

        <!-- Кнопка додавання -->

        <button @click="
                        openModal = true;
                        newDocument = true;
                        modalDocument = new Speciality()
                    "
                @click.prevent
                class="item-add my-5"
        >
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
            </svg>

            {{__('forms.addSpeciality')}}
        </button>

        <!-- Modal -->
        <template x-teleport="body">
            <div x-show="openModal"
                 style="display: none"
                 @keydown.escape.prevent.stop="openModal = false"
                 role="dialog"
                 aria-modal="true"
                 x-id="['modal-title']"
                 :aria-labelledby="$id('modal-title')"
                 class="modal"
            >
                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/40 z-40"></div>

                <div x-show="openModal"
                     x-transition
                     @click="openModal = false"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div @click.stop
                         x-trap.noscroll.inert="openModal"
                         class="w-full max-w-lg bg-gray-800 text-white rounded-lg shadow-lg p-6"
                    >
                        <h2 class="text-xl font-semibold mb-6" :id="$id('modal-title')">
                            <span x-text="newSpeciality ? '{{ __('Додати спеціальність') }}' : '{{ __('Редагувати спеціальність') }}'"></span>
                        </h2>

                        <form>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="specSpeciality" class="block mb-1 text-sm font-medium">{{ __('Спеціальність') }}</label>
                                    <input type="text" id="specSpeciality" x-model="modalSpeciality.speciality"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalSpeciality.speciality">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="specAttestation" class="block mb-1 text-sm font-medium">{{ __('Орган що видав') }}</label>
                                    <input type="text" id="specAttestation" x-model="modalSpeciality.attestation_name"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalSpeciality.attestation_name">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="specLevel" class="block mb-1 text-sm font-medium">{{ __('Рівень спеціальності') }}</label>
                                    <input type="text" id="specLevel" x-model="modalSpeciality.level"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalSpeciality.level">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="specCertificate" class="block mb-1 text-sm font-medium">{{ __('Номер свідоцтва') }}</label>
                                    <input type="text" id="specCertificate" x-model="modalSpeciality.certificate_number"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-4">
                                <button type="button"
                                        @click="openModal = false"
                                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                    {{ __('forms.cancel') }}
                                </button>
                                <button type="submit"
                                        @click.prevent="newSpeciality ? specialities.push(modalSpeciality) : specialities[item] = modalSpeciality; openModal = false"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        :disabled="!(modalSpeciality.speciality && modalSpeciality.attestation_name && modalSpeciality.level)">
                                    {{ __('forms.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

    </fieldset>
</div>

<script>
    class Speciality {
        speciality = '';
        attestation_name = '';
        level = '';
        certificate_number = '';

        constructor(obj = null) {
            if (obj) Object.assign(this, obj);
        }
    }
</script>

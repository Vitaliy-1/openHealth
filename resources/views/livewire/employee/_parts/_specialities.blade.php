<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              {{-- Binding documents to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
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
        <legend class="legend">
            <h2>{{ __('forms.specialities') }}</h2>
        </legend>

        <table class="table-input w-full">
            <thead class="thead-input">
            <tr>
                <th class="th-input">{{ __('forms.speciality') }}</th>
                <th class="th-input">{{ __('forms.document_issued_by') }}</th>
                <th class="th-input">{{ __('forms.speciality_level') }}</th>
                <th class="th-input">{{ __('forms.speciality_officio') }}</th>
                <th class="th-input">{{ __('forms.certificate_number') }}</th>
                <th class="th-input">{{ __('forms.attestationDate') }}</th>
                <th class="th-input">{{ __('forms.actions') }}</th>
            </tr>
            </thead>
            <tbody>


            <template x-for="(speciality, index) in specialities" :key="index">
                <tr>
                    <td class="td-input" x-text="specDict[speciality.speciality] || speciality.speciality"></td>
                    <td class="td-input" x-text="speciality.attestation_name"></td>
                    <td class="td-input" x-text="levelDict[speciality.level] || speciality.level"></td>
                    <td class="td-input">
                        <div class="flex justify-center">
                            <svg x-show="speciality.speciality_officio" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <svg x-show="!speciality.speciality_officio" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </td>
                    <td class="td-input" x-text="speciality.certificate_number"></td>
                    <td class="td-input" x-text="speciality.attestation_date"></td>
                    <td class="td-input">
                        <!-- Кнопки редагування та видалення -->
                        <x-dropdown-button
                            :editAction="'openModal = true; item = index; modalSpeciality = new Speciality(speciality); newSpeciality = false; close($refs.button)'"
                            :deleteAction="'specialities.splice(index, 1); close($refs.button)'"
                        />
                    </td>
                </tr>
            </template>

            </tbody>
        </table>

        <!-- Кнопка додавання -->
        <button @click="
                        openModal = true;
                        newSpeciality = true;
                        modalSpeciality = new Speciality()
                    "
                @click.prevent
                class="item-add my-5"
        >
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
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
                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/25"></div>

                <div x-show="openModal"
                     x-transition
                     @click="openModal = false"
                     class="relative flex min-h-screen items-center justify-center p-4"
                >
                    <div @click.stop
                         x-trap.noscroll.inert="openModal"
                         class="modal-content h-fit"
                    >
                        <h3 class="modal-header" :id="$id('modal-title')">
                            <span x-text="newSpeciality ? '{{ __('forms.addSpeciality') }}' : '{{ __('forms.edit') . ' ' . __('forms.speciality') }}'"></span>
                        </h3>

                        <form>
                            <div class="form-row-modal grid grid-cols-2 gap-4">
                                <div>
                                    <label for="speciality" class="label-modal">{{ __('forms.speciality') }}</label>
                                    <select id="speciality"
                                            x-model="modalSpeciality.speciality"
                                            class="input-modal"
                                            required>
                                        <template x-for="(description, value) in specDict">
                                            <option :value="value" x-text="description"></option>
                                        </template>
                                    </select>
                                    <p class="text-error text-xs"
                                       x-show="!modalSpeciality.speciality || !Object.keys(specDict).includes(modalSpeciality.speciality)">{{ __('forms.field_empty') }}</p>
                                </div>
                                <div class="flex flex-col justify-end">
                                    <label class="inline-flex items-center mt-6">
                                        <input type="checkbox" x-model="modalSpeciality.speciality_officio"
                                               class="h-4 w-4 text-blue-600 dark:text-blue-500 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('forms.speciality_officio') }}</span>
                                    </label>
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1"
                                       x-show="modalSpeciality.speciality_officio === null || modalSpeciality.speciality_officio === undefined">
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>
                                <div>
                                    <label for="attestationName"
                                           class="label-modal">{{ __('forms.document_issued_by') }}</label>
                                    <input x-model="modalSpeciality.attestation_name" type="text" id="attestationName"
                                           class="input-modal" required>
                                    <p class="text-error text-xs"
                                       x-show="!modalSpeciality.attestation_name || modalSpeciality.attestation_name.trim().length === 0">{{ __('forms.field_empty') }}</p>
                                </div>
                                <div>
                                    <label for="attestationDate"
                                           class="label-modal">{{ __('forms.attestationDate') }}</label>
                                    <input id="attestationDate" x-model="modalSpeciality.attestation_date"
                                           class="input-modal datepicker-input" autocomplete="off" required>
                                    <p class="text-error text-xs"
                                       x-show="!modalSpeciality.attestation_date || modalSpeciality.attestation_date.trim().length === 0">{{ __('forms.field_empty') }}</p>
                                </div>
                                <div>
                                    <label for="specialityLevel"
                                           class="label-modal">{{ __('forms.speciality_level') }}</label>
                                    <select id="specialityLevel"
                                            x-model="modalSpeciality.level"
                                            class="input-modal"
                                            required>
                                        <template x-for="(description, value) in levelDict">
                                            <option :value="value" x-text="description"></option>
                                        </template>
                                    </select>
                                    <p class="text-error text-xs"
                                       x-show="!modalSpeciality.level || !Object.keys(levelDict).includes(modalSpeciality.level)">{{ __('forms.field_empty') }}</p>
                                </div>
                                <div>
                                    <label for="certificateNumber"
                                           class="label-modal">{{ __('forms.certificate_number') }}</label>
                                    <input x-model="modalSpeciality.certificate_number" type="text"
                                           id="certificateNumber" class="input-modal">
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between space-x-2">
                                <button type="button"
                                        @click="openModal = false"
                                        class="button-minor">
                                    {{ __('forms.cancel') }}
                                </button>
                                <button type="submit"
                                        @click.prevent="newSpeciality ? specialities.push(modalSpeciality) : specialities[item] = modalSpeciality; openModal = false"
                                        class="button-primary"
                                        :disabled="!(modalSpeciality.speciality && modalSpeciality.speciality.trim().length > 0 &&
                                            modalSpeciality.attestation_name && modalSpeciality.attestation_name.trim().length > 0 &&
                                            modalSpeciality.level && modalSpeciality.level.trim().length > 0 &&
                                            modalSpeciality.attestation_date && modalSpeciality.attestation_date.trim().length > 0)"
                                >
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
        speciality_officio = '';
        attestation_name = '';
        attestation_date = '';
        level = '';
        certificate_number = '';

        constructor(obj = null) {
            if (obj) Object.assign(this, obj);
        }
    }
</script>

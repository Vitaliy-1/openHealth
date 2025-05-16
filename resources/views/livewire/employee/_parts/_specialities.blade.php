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
                <th class="th-input">{{ __('forms.specialityAttestationDate') }}</th>
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
        <template x-teleport="body"> {{-- This moves the modal at the end of the body tag --}}
            <div x-show="openModal"
                 style="display: none"
                 @keydown.escape.prevent.stop="openModal = false"
                 role="dialog"
                 aria-modal="true"
                 x-id="['modal-title']"
                 :aria-labelledby="$id('modal-title')" {{-- This associates the modal with unique ID --}}
                 class="modal"
            >

                {{-- Overlay --}}
                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/40 z-40"></div>

                {{-- Panel --}}
                <div x-show="openModal"
                     x-transition
                     @click="openModal = false"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div @click.stop
                         x-trap.noscroll.inert="openModal"
                         class="modal-content h-fit" {{-- class="w-full max-w-lg bg-gray-800 text-white rounded-lg shadow-lg p-6" --}}
                    >
                        {{-- Title --}}
                        <h2 class="modal-header" :id="$id('modal-title')">
                            <span
                                x-text="newSpeciality ? '{{ __('forms.addSpeciality') }}' : '{{ __('forms.edit') . ' ' . __('forms.speciality') }}'"></span>
                        </h2>

                        {{-- Content --}}
                        <form>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="specialityType" class="label-modal">{{ __('Спеціальність') }}
                                    </label>

                                    <select id="specialityType"
                                            x-model="modalSpeciality.speciality"
                                            class="input-modal"
                                            required>
                                        <option value="">{{ __('forms.speciality') }}</option>
                                        @foreach($this->dictionaries['SPECIALITY_TYPE'] as $typeValue => $typeDescription)
                                            <option value="{{ $typeValue }}">{{ $typeDescription }}</option>
                                        @endforeach
                                    </select>

                                    <p class="text-red-500 text-xs mt-1"
                                       x-show="modalSpeciality.speciality && !Object.keys(dictionary).includes(modalSpeciality.speciality)">
                                        {{ __('forms.invalid_selection') }}
                                    </p>

                                    <p class="text-red-500 text-xs mt-1"
                                       x-show="!modalSpeciality.speciality">
                                        {{ __('forms.field_empty') }}
                                    </p>
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
                                    <label for="specAttestation"
                                           class="label-modal">{{ __('Орган що видав') }}</label>
                                    <input type="text" id="specAttestation" x-model="modalSpeciality.attestation_name"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-red-500 text-xs mt-1"
                                       x-show="!modalSpeciality.attestation_name">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div class="mt-4">
                                    <label for="specLevel" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('forms.speciality_level') }}
                                    </label>

                                    <select id="specLevel"
                                            x-model="modalSpeciality.level"
                                            class="input-modal w-full px-3 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                        <option value="">{{ __('forms.speciality_level') }}</option>
                                        @foreach($this->dictionaries['SPECIALITY_LEVEL'] as $typeValue => $typeDescription)
                                            <option value="{{ $typeValue }}">{{ $typeDescription }}</option>
                                        @endforeach
                                    </select>

                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1"
                                       x-show="modalSpeciality.level && !Object.keys(dictionary).includes(modalSpeciality.level)">
                                        {{ __('forms.invalid_selection') }}
                                    </p>

                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1"
                                       x-show="!modalSpeciality.level">
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div>
                                    <label for="specCertificate"
                                           class="label-modal">{{ __('forms.certificate_number') }}</label>
                                    <input type="text" id="specCertificate" x-model="modalSpeciality.certificate_number"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="specAttestationDate" class="label-modal">{{__('forms.speciality_attestation_date')}}</label>
                                    <input id="specAttestationDate" x-model="modalSpeciality.attestation_date"  class="input-modal datepicker-input"
                                           autocomplete="off" required>
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

<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              {{-- Binding documents to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  qualifications: $wire.entangle('form.qualifications'),
                  openModal: false,
                  modalQualification: new Qualification(),
                  newQualification: false,
                  item: 0,
                  qualTypeDict: $wire.dictionaries['QUALIFICATION_TYPE'],
                  qualSpecDict: $wire.dictionaries['SPEC_QUALIFICATION_TYPE'],
                  countryDict: @js($this->dictionaries['COUNTRY']),
              }"
    >
        <legend class="legend">
            <h2>{{ __('forms.qualifications') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.document_type') }}</th>
                <th scope="col" class="th-input">{{ __('forms.country') }}</th>
                <th scope="col" class="th-input">{{ __('forms.institutionName') }}</th>
                <th scope="col" class="th-input">{{ __('forms.speciality') }}</th>
                <th scope="col" class="th-input">{{ __('forms.certificateNumber') }}</th>
                <th scope="col" class="th-input">{{ __('forms.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(qualification, index) in qualifications" :key="index">
                <tr>
                    <td class="td-input" x-text="qualTypeDict[qualification.type] || qualification.type"></td>
                    <td class="td-input" x-text="countryDict[qualification.country] || qualification.country"></td>
                    <td class="td-input" x-text="qualification.institution_name"></td>
                    <td class="td-input" x-text="qualification.speciality"></td>
                    <td class="td-input" x-text="qualification.certificate_number"></td>
                    <td class="td-input relative">
                        <!-- Кнопки редагування та видалення -->
                        <x-dropdown-button
                            :editAction="'openModal = true; item = index; modalQualification = new Qualification(qualification); newQualification = false; close($refs.button)'"
                            :deleteAction="'qualifications.splice(index, 1); close($refs.button)'"
                        />
                    </td>
                </tr>
            </template>

            </tbody>
        </table>

        <div>

            <button @click="
                    openModal = true;
                    newQualification = true;
                    modalQualification = new Qualification({ country: 'UA' });
                    "
                    @click.prevent
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                     viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14m-7 7V5"/>
                </svg>

                {{__('forms.addQualification')}}
            </button>


            {{-- Modal --}}
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
                                <span x-text="newQualification ? '{{ __('forms.addQualification') }}' : '{{ __('forms.edit') . ' ' . __('forms.qualification') }}'"></span>
                            </h3>

                            <form>
                                <div class="form-row-modal grid grid-cols-2 gap-4">

                                    <div>
                                        <label for="qualificationType"
                                               class="label-modal">{{ __('forms.qualification_type') }}</label>
                                        <select id="qualificationType"
                                                x-model="modalQualification.type"
                                                class="input-modal"
                                                required>
                                            <template x-for="(description, value) in qualTypeDict">
                                                <option :value="value" x-text="description"></option>
                                            </template>
                                        </select>
                                        <p class="text-error text-xs"
                                           x-show="!modalQualification.type || !Object.keys(qualTypeDict).includes(modalQualification.type)">{{ __('forms.field_empty') }}</p>
                                    </div>

                                    <div>
                                        <label for="qualificationCountry"
                                               class="label-modal">{{ __('forms.country') }}</label>
                                        <select x-model="modalQualification.country" id="qualificationCountry"
                                                class="input-modal" required>
                                            <option value="">{{ __('forms.select_country') }}</option>
                                            <template x-for="(description, value) in countryDict">
                                                <option :value="value" x-text="description"></option>
                                            </template>
                                        </select>
                                        <p class="text-error text-xs"
                                           x-show="!modalQualification.country || !Object.keys(countryDict).includes(modalQualification.country)">{{ __('forms.field_empty') }}</p>
                                        <p class="text-error text-xs"
                                           x-show="!modalQualification.country || modalQualification.country.trim().length === 0">{{ __('forms.field_empty') }}</p>
                                    </div>
                                    {{-- ... Решта полів без змін, якщо вони не викликають помилок ... --}}

                                    <div>
                                        <label for="qualificationInstitution"
                                               class="label-modal">{{ __('forms.institutionName') }}</label>
                                        <input x-model="modalQualification.institution_name" type="text"
                                               id="qualificationInstitution" class="input-modal" required>
                                        <p class="text-error text-xs"
                                           x-show="!modalQualification.institution_name || modalQualification.institution_name.trim().length === 0">{{ __('forms.field_empty') }}</p>
                                    </div>

                                    <div>
                                        <label for="qualificationSpeciality"
                                               class="label-modal">{{ __('forms.speciality') }}</label>
                                        <select id="qualificationSpeciality"
                                                x-model="modalQualification.speciality"
                                                class="input-modal"
                                                required>
                                            <template x-for="(description, value) in qualSpecDict">
                                                <option :value="value" x-text="description"></option>
                                            </template>
                                        </select>
                                        <p class="text-error text-xs"
                                           x-show="!modalQualification.speciality || !Object.keys(qualSpecDict).includes(modalQualification.speciality)">{{ __('forms.field_empty') }}</p>
                                    </div>

                                    <div>
                                        <label for="qualificationCertificateNumber"
                                               class="label-modal">{{ __('forms.certificateNumber') }}</label>
                                        <input x-model="modalQualification.certificate_number" type="text"
                                               id="qualificationCertificateNumber" class="input-modal">
                                    </div>

                                </div>

                                <div class="mt-6 flex justify-between space-x-2">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{ __('forms.cancel') }}
                                    </button>

                                    <button type="submit"
                                            @click.prevent="newQualification ? qualifications.push(modalQualification) : qualifications[item] = modalQualification; openModal = false"
                                            class="button-primary"
                                            :disabled="!(modalQualification.type && modalQualification.type.trim().length > 0 &&
                                            modalQualification.country && modalQualification.country.trim().length > 0 &&
                                            modalQualification.institution_name && modalQualification.institution_name.trim().length > 0 &&
                                            modalQualification.speciality && modalQualification.speciality.trim().length > 0)"
                                    >
                                        {{ __('forms.save') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </fieldset>
</div>

<script>
    class Qualification {
        type = '';
        country = '';
        institution_name = '';
        speciality = '';
        certificate_number = '';

        constructor(obj = null) {
            if (obj) Object.assign(this, obj);
        }
    }
</script>

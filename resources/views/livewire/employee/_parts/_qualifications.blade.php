<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              x-data="{
                  qualifications: $wire.entangle('form.qualifications'),
                  openModal: false,
                  modalQualification: new Qualification(),
                  newQualification: false,
                  item: 0,
                  qualTypeDict: $wire.dictionaries['QUALIFICATION_TYPE']
              }"
    >
        <legend class="legend">
            <h2>{{ __('forms.qualifications') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.document_type') }}</th>
                <th scope="col" class="th-input">{{ __('forms.institutionName') }}</th>
                <th scope="col" class="th-input">{{ __('forms.speciality') }}</th>
                <th scope="col" class="th-input">{{ __('forms.certificateNumber') }}</th>
                <th scope="col" class="th-input">{{ __('forms.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(qualification, index) in qualifications">
                <tr>
                    <td class="td-input" x-text="qualTypeDict[qualification.type] || qualification.type"></td>
                    <td class="td-input" x-text="qualification.institution_name"></td>
                    <td class="td-input" x-text="qualification.speciality"></td>
                    <td class="td-input" x-text="qualification.certificate_number"></td>
                    <td class="td-input">
                        <div
                            x-data="{
                                    openDropdown: false,
                                    toggle() {
                                        if (this.openDropdown) {
                                            return this.close()
                                        }

                                        this.$refs.button.focus()

                                        this.openDropdown = true
                                    },
                                    close(focusAfter) {
                                        if (!this.openDropdown) return

                                        this.openDropdown = false

                                        focusAfter && focusAfter.focus()
                                    }
                                }"
                            @keydown.escape.prevent.stop="close($refs.button)"
                            @focusin.window="! $refs.panel.contains($event.target) && close()"
                            x-id="['dropdown-button']"
                            class="relative"
                        >
                            <button
                                x-ref="button"
                                @click="toggle()"
                                :aria-expanded="openDropdown"
                                :aria-controls="$id('dropdown-button')"
                                type="button"
                                class=""
                            >
                                <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"/>
                                </svg>
                            </button>

                            <div class="absolute" style="left: 50%">
                                <div
                                    x-ref="panel"
                                    x-show="openDropdown"
                                    x-transition.origin.top.left
                                    @click.outside="close($refs.button)"
                                    :id="$id('dropdown-button')"
                                    x-cloak
                                    class="dropdown-panel relative"
                                    style="left: -50%"
                                >
                                    <button @click="
                                                    openModal = true;
                                                    item = index;
                                                    modalQualification = new Qualification(qualification);
                                                    newQualification = false;
                                                    close($refs.button);
                                                "
                                            @click.prevent
                                            class="dropdown-button"
                                    >
                                        {{__('forms.edit')}}
                                    </button>

                                    <button @click="qualifications.splice(index, 1); close($refs.button)"
                                            @click.prevent
                                            class="dropdown-button dropdown-delete">
                                        {{__('forms.delete')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>

        <div>
            <button @click="
                        openModal = true;
                        newQualification = true;
                        modalQualification = new Qualification();
                    "
                    @click.prevent
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                </svg>
                {{__('Додати підвищення кваліфікації')}}
            </button>

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
                                <span x-text="newQualification ? '{{ __('Додати підвищення кваліфікації') }}' : '{{ __('Редагувати підвищення кваліфікації') }}'"></span>
                            </h3>

                            <form>
                                <div class="form-row-modal grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="qualificationType" class="label-modal">{{__('forms.document_type')}}</label>
                                        <select x-model="modalQualification.type" id="qualificationType" class="input-modal" required>
                                            <option value="" disabled selected>{{ __('forms.select') }}</option>
                                            <template x-for="(label, val) in qualTypeDict" :key="val">
                                                <option :value="val" x-text="label"></option>
                                            </template>
                                        </select>
                                        <p class="text-error text-xs" x-show="!modalQualification.type">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="qualificationInstitution" class="label-modal">{{__('forms.institutionName')}}</label>
                                        <input x-model="modalQualification.institution_name" type="text" id="qualificationInstitution" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalQualification.institution_name.trim().length > 0">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="qualificationSpeciality" class="label-modal">{{__('forms.speciality')}}</label>
                                        <input x-model="modalQualification.speciality" type="text" id="qualificationSpeciality" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalQualification.speciality.trim().length > 0">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="qualificationCertificate" class="label-modal">{{__('forms.certificateNumber')}}</label>
                                        <input x-model="modalQualification.certificate_number" type="text" id="qualificationCertificate" class="input-modal">
                                    </div>
                                    <div>
                                        <label for="qualificationIssuedDate" class="label-modal">{{ __('forms.issuedDate') }}</label>
                                        <input x-model="modalQualification.issued_date" type="date" id="qualificationIssuedDate" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalQualification.issued_date">{{ __('forms.field_empty') }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between space-x-2">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{__('forms.cancel')}}
                                    </button>

                                    <button @click.prevent
                                            @click="newQualification ? qualifications.push(modalQualification) : qualifications[item] = modalQualification; openModal = false"
                                            class="button-primary"
                                            :disabled="!(modalQualification.type && modalQualification.institution_name.trim().length > 0 && modalQualification.speciality.trim().length > 0)"
                                    >
                                        {{__('forms.save')}}
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
        institution_name = '';
        speciality = '';
        certificate_number = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

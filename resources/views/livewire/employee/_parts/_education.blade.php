<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              x-data="{
                  educations: $wire.entangle('form.educations'),
                  openModal: false,
                  modalEducation: new Education(),
                  newEducation: false,
                  item: 0,
                  degreeDict: {
                      'BACHELOR': '{{ __('forms.bachelor') }}',
                      'MASTER': '{{ __('forms.master') }}',
                      'PHD': '{{ __('forms.phd') }}',
                      'ASSOCIATE': '{{ __('forms.associate') }}',
                      'SPECIALIST': '{{ __('forms.specialist') }}'
                  }
              }"
    >
        <legend class="legend">
            <h2>{{ __('forms.education') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.country') }}</th>
                <th scope="col" class="th-input">{{ __('forms.city') }}</th>
                <th scope="col" class="th-input">{{ __('forms.institutionName') }}</th>
                <th scope="col" class="th-input">{{ __('forms.speciality') }}</th>
                <th scope="col" class="th-input">{{ __('forms.degree') }}</th>
                <th scope="col" class="th-input">{{ __('forms.issuedDate') }}</th>
                <th scope="col" class="th-input">{{ __('forms.diplomaNumber') }}</th>
                <th scope="col" class="th-input">{{ __('forms.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(education, index) in educations">
                <tr>
                    <td class="td-input" x-text="education.country"></td>
                    <td class="td-input" x-text="education.city"></td>
                    <td class="td-input" x-text="education.institution_name"></td>
                    <td class="td-input" x-text="education.speciality"></td>
                    <td class="td-input" x-text="degreeDict[education.degree] || education.degree"></td>
                    <td class="td-input" x-text="education.issued_date"></td>
                    <td class="td-input" x-text="education.diploma_number"></td>
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

                            <div class="relative">
                                <div class="absolute top-0 left-0 right-0 z-10 bg-white shadow-lg">
                                    <div
                                        x-ref="panel"
                                        x-show="openDropdown"
                                        x-transition:enter="transition transform duration-300 ease-out"
                                        x-transition:enter-start="opacity-0 translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition transform duration-200 ease-in"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-2"
                                        @click.outside="close($refs.button)"
                                        :id="$id('dropdown-button')"
                                        x-cloak
                                        class="dropdown-panel relative"
                                        style="top: -100%; left: 50%; transform: translateX(-50%);"
                                    >
                                        <button
                                            @click="
                    openModal = true;
                    item = index;
                    modalEducation = new Education(education);
                    newEducation = false;
                    close($refs.button);
                "
                                            @click.prevent
                                            class="dropdown-button"
                                        >
                                            {{ __('forms.edit') }}
                                        </button>

                                        <button
                                            @click="educations.splice(index, 1); close($refs.button)"
                                            @click.prevent
                                            class="dropdown-button dropdown-delete"
                                        >
                                            {{ __('forms.delete') }}
                                        </button>
                                    </div>
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
                        newEducation = true;
                        modalEducation = new Education();
                    "
                    @click.prevent
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                </svg>
                {{__('forms.addEducation')}}
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
                                <span x-text="newEducation ? '{{ __('forms.add_education') }}' : '{{ __('forms.edit_education') }}'"></span>
                            </h3>

                            <form>
                                <div class="form-row-modal grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="educationCountry" class="label-modal">{{__('forms.country')}}</label>
                                        <input x-model="modalEducation.country" type="text" id="educationCountry" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalEducation.country.trim().length > 0">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="educationCity" class="label-modal">{{__('forms.city')}}</label>
                                        <input x-model="modalEducation.city" type="text" id="educationCity" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalEducation.city.trim().length > 0">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="educationInstitution" class="label-modal">{{__('forms.institutionName')}}</label>
                                        <input x-model="modalEducation.institution_name" type="text" id="educationInstitution" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalEducation.institution_name.trim().length > 0">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="educationSpeciality" class="label-modal">{{__('forms.speciality')}}</label>
                                        <input x-model="modalEducation.speciality" type="text" id="educationSpeciality" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalEducation.speciality.trim().length > 0">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="educationDegree" class="label-modal">{{__('forms.degree')}}</label>
                                        <select x-model="modalEducation.degree" id="educationDegree" class="input-modal" required>
                                            <option value="" disabled selected>{{ __('forms.select') }}</option>
                                            <option value="BACHELOR">{{ __('forms.bachelor') }}</option>
                                            <option value="MASTER">{{ __('forms.master') }}</option>
                                            <option value="PHD">{{ __('forms.phd') }}</option>
                                            <option value="ASSOCIATE">{{ __('forms.associate') }}</option>
                                            <option value="SPECIALIST">{{ __('forms.specialist') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="educationIssuedDate" class="label-modal">{{__('forms.issuedDate')}}</label>
                                        <input x-model="modalEducation.issued_date" type="date" id="educationIssuedDate" class="input-modal">
                                    </div>
                                    <div>
                                        <label for="educationDiplomaNumber" class="label-modal">{{__('forms.diplomaNumber')}}</label>
                                        <input x-model="modalEducation.diploma_number" type="text" id="educationDiplomaNumber" class="input-modal">
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
                                            @click="newEducation ? educations.push(modalEducation) : educations[item] = modalEducation; openModal = false"
                                            class="button-primary"
                                            :disabled="!(modalEducation.country.trim().length > 0 &&
                                                      modalEducation.city.trim().length > 0 &&
                                                      modalEducation.institution_name.trim().length > 0 &&
                                                      modalEducation.speciality.trim().length > 0)"
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
    class Education {
        country = '';
        city = '';
        institution_name = '';
        speciality = '';
        degree = '';
        issued_date = '';
        diploma_number = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

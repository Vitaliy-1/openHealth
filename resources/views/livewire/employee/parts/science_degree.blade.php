<div class="overflow-x-auto relative">
    <fieldset class="fieldset" id="section-doctor-science-degree"
              x-data="{
                  scienceDegrees: $wire.entangle('form.doctor.scienceDegrees'),
                  openModal: false,
                  modalScienceDegree: new ScienceDegree(),
                  newScienceDegree: false,
                  item: 0,
                  degreeDict: @js($this->dictionaries['SCIENCE_DEGREE']),
                  specDict: @js($this->dictionaries['SPECIALITY_TYPE']),
                  countryDict: @js($this->dictionaries['COUNTRY']),
              }"
    >
        <legend class="legend">
            <h2>{{ __('forms.science_degree') }}</h2>
        </legend>

        @error('form.doctor.science_degree')
        <p class="text-error -mt-2 mb-4">{{ $message }}</p>
        @enderror

        <table class="table-input w-full">
            <thead class="thead-input">
            <tr>
                <th class="th-input">{{ __('forms.degree') }}</th>
                <th class="th-input">{{ __('forms.country') }}</th>
                <th class="th-input">{{ __('forms.city') }}</th>
                <th class="th-input">{{ __('forms.issuedDate') }}</th>
                <th class="th-input">{{ __('forms.institutionName') }}</th>
                <th class="th-input">{{ __('forms.speciality') }}</th>
                <th class="th-input">{{ __('forms.diplomaNumber') }}</th>
                <th class="th-input">{{ __('forms.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(scienceDegree, index) in scienceDegrees" :key="index">
                <tr>
                    <td class="td-input" x-text="degreeDict[scienceDegree.degree]"></td>
                    <td class="td-input" x-text="countryDict[scienceDegree.country]"></td>
                    <td class="td-input" x-text="scienceDegree.city"></td>
                    <td class="td-input" x-text="scienceDegree.issuedDate"></td>
                    <td class="td-input" x-text="scienceDegree.institutionName"></td>
                    <td class="td-input" x-text="specDict[scienceDegree.speciality]"></td>
                    <td class="td-input" x-text="scienceDegree.diplomaNumber"></td>
                    <td class="td-input">
                        <div
                            x-data="{ openDropdown: false }"
                            @keydown.escape.prevent.stop="openDropdown = false"
                            @focusin.window="!$refs.panel.contains($event.target) && (openDropdown = false)"
                            x-id="['dropdown-button']"
                            class="relative"
                        >
                            <button
                                x-ref="button"
                                @click="openDropdown = !openDropdown"
                                :aria-expanded="openDropdown"
                                :aria-controls="$id('dropdown-button')"
                                type="button"
                                class="cursor-pointer"
                            >
                                <svg class="w-6 h-6 text-gray-800 dark:text-gray-200 svg-hover-action" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"></path>
                                </svg>
                            </button>

                            <div
                                x-ref="panel"
                                x-show="openDropdown"
                                x-transition.origin.top.left
                                @click.outside="openDropdown = false"
                                :id="$id('dropdown-button')"
                                class="dropdown-panel absolute"
                                style="left: -120%; display: none;"
                            >
                                <button
                                    @click.prevent="
                                        openModal = true;
                                        item = index;
                                        modalScienceDegree = new ScienceDegree(scienceDegree);
                                        newScienceDegree = false;
                                        openDropdown = false;
                                    "
                                    class="dropdown-button"
                                >
                                    {{ __('forms.edit') }}
                                </button>

                                <button
                                    @click.prevent="
                                        scienceDegrees.splice(index, 1);
                                        openDropdown = false;
                                    "
                                    class="dropdown-button dropdown-delete"
                                >
                                    {{ __('forms.delete') }}
                                </button>
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
                        newScienceDegree = true;
                        modalScienceDegree = new ScienceDegree({ country: 'UA' });
                    "
                    @click.prevent
                    class="item-add my-5"
            >
                {{ __('forms.addScienceDegree') }}
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
                         class="relative flex min-h-screen items-center justify-start pl-72 p-4"
                    >
                        <div @click.stop
                             x-trap.noscroll.inert="openModal"
                             class="modal-content h-fit w-full max-w-4xl rounded-2xl shadow-lg bg-white"
                        >

                            <h3 class="modal-header" :id="$id('modal-title')">
                                <span x-text="newScienceDegree ? '{{ __('forms.addScienceDegree') }}' : '{{ __('forms.edit') . ' ' . __('forms.science_degree') }}'"></span>
                            </h3>

                            <form>
                                <div class="form-row-modal">
                                    <div>
                                        <label for="scienceDegreeDegree" class="label-modal">{{ __('forms.degree') }} <span class="text-red-600"> *</span></label>
                                        <select x-model="modalScienceDegree.degree" id="scienceDegreeDegree"
                                                class="input-modal" required>
                                            <option value="">{{__('forms.select_level')}}</option>
                                            @foreach($this->dictionaries['SCIENCE_DEGREE'] as $degreeValue => $degreeDescription)
                                                <option value="{{ $degreeValue }}">{{ $degreeDescription }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="scienceDegreeCountry" class="label-modal">{{ __('forms.country') }} <span class="text-red-600"> *</span></label>
                                        <select x-model="modalScienceDegree.country" id="scienceDegreeCountry"
                                                class="input-modal" required>
                                            @foreach($this->dictionaries['COUNTRY'] as $countryValue => $countryDescription)
                                                <option value="{{ $countryValue }}">{{ $countryDescription }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="scienceCity" class="label-modal">{{ __('forms.city') }} <span class="text-red-600"> *</span></label>
                                        <input x-model="modalScienceDegree.city" type="text" id="scienceCity"
                                               class="input-modal" required>
                                    </div>
                                    <div class="relative">
                                        <svg class="svg-input absolute left-1 !top-2/3 transform -translate-y-1/2 pointer-events-none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 5V4a1 1 0 1 1 2 0v1h3V4a1 1 0 1 1 2 0v1h3V4a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v2H3V7a2 2 0 0 1 2-2h1ZM3 19v-8h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm5-6a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8Z" clip-rule="evenodd"/>
                                        </svg>
                                        <label for="scienceDegreeIssuedDate" class="label-modal">{{ __('forms.issuedDate') }}<span class="text-red-600"> *</span></label>
                                        <input x-model="modalScienceDegree.issuedDate" type="text" name="scienceDegreeIssuedDate" id="scienceDegreeIssuedDate" class="input-modal datepicker-input" autocomplete="off">
                                    </div>
                                    <div>
                                        <label for="scienceDegreeInstitutionName" class="label-modal">{{ __('forms.institutionName') }} <span class="text-red-600"> *</span></label>
                                        <input x-model="modalScienceDegree.institutionName" type="text"
                                               id="scienceDegreeInstitutionName" class="input-modal" required>
                                    </div>
                                    <div>
                                        <label for="scienceDegreeSpeciality" class="label-modal">{{ __('forms.speciality') }} <span class="text-red-600"> *</span></label>
                                        <select x-model="modalScienceDegree.speciality" id="scienceDegreeSpeciality"
                                                class="input-modal" required>
                                            <option value="">{{__('forms.select_speciality')}}</option>
                                            @foreach($this->dictionaries['SPECIALITY_TYPE'] as $specValue => $specDescription)
                                                <option value="{{ $specValue }}">{{ $specDescription }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="scienceDegreeDiplomaNumber" class="label-modal">{{ __('forms.diplomaNumber') }} <span class="text-red-600"> *</span></label>
                                        <input x-model="modalScienceDegree.diplomaNumber" type="text"
                                               id="scienceDegreeDiplomaNumber" class="input-modal">
                                    </div>
                                </div>
                                <p class="text-sm text-gray-400 mb-2">{{ __('forms.form_required_note') }}</p>
                                <div class="mt-6 flex justify-between space-x-2">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{ __('forms.cancel') }}
                                    </button>

                                    <button @click.prevent
                                            @click="newScienceDegree ? scienceDegrees.push(modalScienceDegree) : scienceDegrees[item] = modalScienceDegree; openModal = false"
                                            class="button-primary"
                                            :disabled="!(modalScienceDegree.degree && modalScienceDegree.degree.trim().length > 0 &&
                                                modalScienceDegree.institutionName && modalScienceDegree.institutionName.trim().length > 0 &&
                                                modalScienceDegree.issuedDate && modalScienceDegree.issuedDate.trim().length > 0 &&
                                                modalScienceDegree.speciality && modalScienceDegree.speciality.trim().length > 0 &&
                                                modalScienceDegree.country && modalScienceDegree.country.trim().length > 0 &&
                                                modalScienceDegree.city && modalScienceDegree.city.trim().length > 0)"
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
    class ScienceDegree {
        degree = '';
        country = '';
        city = '';
        issuedDate = '';
        institutionName = '';
        speciality = '';
        diplomaNumber = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

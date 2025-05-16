<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              x-data="{
                  scienceDegrees: $wire.entangle('form.scienceDegrees'),
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

        <template x-if="scienceDegrees.length">
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
                        <td class="td-input" x-text="degreeDict[scienceDegree.degree] || scienceDegree.degree"></td>
                        <td class="td-input" x-text="countryDict[scienceDegree.country] || scienceDegree.country"></td>
                        <td class="td-input" x-text="scienceDegree.city"></td>
                        <td class="td-input" x-text="scienceDegree.issued_date"></td>
                        <td class="td-input" x-text="scienceDegree.institution_name"></td>
                        <td class="td-input" x-text="specDict[scienceDegree.speciality] || scienceDegree.speciality"></td>
                        <td class="td-input" x-text="scienceDegree.diploma_number"></td>
                        <td class="td-input relative">
                            <x-dropdown-button
                                :editAction="'openModal = true; item = index; modalScienceDegree = new ScienceDegree(scienceDegrees[index]); newScienceDegree = false;'"
                                :deleteAction="'scienceDegrees.splice(index, 1);'"
                            />
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </template>

        <!-- Кнопка додавання -->
        <button @click.prevent="
            openModal = true;
            newScienceDegree = true;
            modalScienceDegree = new ScienceDegree();
        " class="item-add my-5">
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
            </svg>
            {{ __('forms.addScienceDegree') }}
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
                         class="modal-content h-fit rounded-lg shadow-lg"
                    >
                        <h3 class="modal-header" :id="$id('modal-title')">
                            <span x-text="newScienceDegree ? '{{ __('forms.addScienceDegree') }}' : '{{ __('forms.edit') . ' ' . __('forms.science_degree') }}'"></span>
                        </h3>

                        <form>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="scienceDegreeType" class="label-modal">{{ __('forms.degree') }}</label>
                                    <select x-model="modalScienceDegree.degree" id="degree" class="input-modal" required>
                                        <template x-for="(name, value) in degreeDict" :key="value">
                                            <option :value="value" x-text="name"></option>
                                        </template>
                                    </select>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.degree">{{ __('forms.field_empty') }}</p>
                                </div>



                                <div>
                                    <label for="scienceIssued" class="label-modal">{{ __('forms.issuedDate') }}</label>
                                    <input id="scienceIssued" x-model="modalScienceDegree.issued_date"
                                           class="input-modal datepicker-input"
                                           autocomplete="off" required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.issued_date">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="scienceCountry" class="label-modal">{{__('forms.country')}}</label>
                                    <select x-model="modalScienceDegree.country" id="scienceCountry" class="input-modal" required>
                                        @foreach($this->dictionaries['COUNTRY'] as $typeValue => $typeDescription)
                                            <option value="{{$typeValue}}">{{$typeDescription}}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-error text-xs"
                                       x-show="!Object.keys(dictionary).includes(modalScienceDegree.country)">{{__('forms.field_empty')}}</p>
                                    <p class="text-error text-xs" x-show="!modalScienceDegree.country.trim().length > 0">{{__('forms.field_empty')}}</p>
                                </div>
                                <div>
                                    <label for="scienceCity" class="label-modal">{{__('forms.city')}}</label>
                                    <input x-model="modalScienceDegree.city" type="text" id="scienceCity" class="input-modal" required>
                                    <p class="text-error text-xs" x-show="!modalScienceDegree.city.trim().length > 0">{{__('forms.field_empty')}}</p>
                                </div>
                                <div>
                                    <label for="scienceInstitution" class="label-modal">{{ __('forms.institutionName') }}</label>
                                    <input type="text" id="scienceInstitution" x-model="modalScienceDegree.institution_name"
                                           class="input-modal" required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.institution_name">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="scienceSpeciality" class="label-modal">{{ __('forms.speciality') }}</label>
                                    <select id="scienceSpeciality" x-model="modalScienceDegree.speciality"
                                            class="input-modal" required>
                                        <option value="">{{ __('forms.speciality') }}</option>
                                        <template x-for="(name, value) in specDict" :key="value">
                                            <option :value="value" x-text="name"></option>
                                        </template>
                                    </select>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.speciality">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="scienceDiploma" class="label-modal">{{ __('forms.diplomaNumber') }}</label>
                                    <input type="text" id="scienceDiploma" x-model="modalScienceDegree.diploma_number"
                                           class="input-modal">
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between space-x-2">
                                <button type="button" @click="openModal = false" class="button-minor">
                                    {{ __('forms.cancel') }}
                                </button>
                                <button type="submit"
                                        @click.prevent="
                                            if (newScienceDegree) {
                                                scienceDegrees.push({...modalScienceDegree});
                                            } else {
                                                scienceDegrees.splice(item, 1, {...modalScienceDegree});
                                            }
                                            openModal = false;
                                        "
                                        class="button-primary"
                                        :disabled="!(modalScienceDegree.degree && modalScienceDegree.institution_name && modalScienceDegree.issued_date && modalScienceDegree.speciality)">
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
        coutry = '';
        city = '';
        issued_date = '';
        institution_name = '';
        speciality = '';
        diploma_number = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

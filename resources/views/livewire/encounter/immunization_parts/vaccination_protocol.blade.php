{{-- Component to input values to the table through the Modal, built with Alpine --}}
<div class="relative"> {{-- This required for table overflow scrolling --}}
    <fieldset class="fieldset"
              {{-- Binding vaccinationProtocolCodes to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  openModal: false,
                  modalVaccinationProtocol: new VaccinationProtocol(),
                  newVaccinationProtocol: false,
                  item: 0,
                  vaccinationTargetDiseasesDictionary: $wire.dictionaries['eHealth/vaccination_target_diseases'],
                  vaccinationAuthoritiesDictionary: $wire.dictionaries['eHealth/vaccination_authorities']
              }"
    >
        <legend class="legend">
            <h2>{{ __('patients.vaccination_protocol') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('patients.dose_sequence') }}</th>
                <th scope="col" class="th-input">{{ __('patients.immunization_series') }}</th>
                <th scope="col" class="th-input">{{ __('patients.target_diseases') }}</th>
                <th scope="col" class="th-input">{{ __('forms.action') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(vaccinationProtocol, index) in modalImmunization.vaccinationProtocols">
                <tr>
                    <td class="td-input"
                        x-text="vaccinationProtocol.doseSequence"
                    ></td>
                    <td class="td-input"
                        x-text="vaccinationProtocol.series"
                    ></td>
                    <td class="td-input"
                        x-text="vaccinationTargetDiseasesDictionary[vaccinationProtocol.targetDiseases[0].coding[0].code]"
                    ></td>
                    <td class="td-input">
                        {{-- That all that is needed for the dropdown --}}
                        <div x-data="{
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
                            {{-- Dropdown Button --}}
                            <button x-ref="button"
                                    @click="toggle()"
                                    :aria-expanded="openDropdown"
                                    :aria-controls="$id('dropdown-button')"
                                    type="button"
                            >
                                <svg class="w-6 h-6 text-gray-800 dark:text-gray-200 cursor-pointer" aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                     viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"/>
                                </svg>
                            </button>

                            {{-- Dropdown Panel --}}
                            <div class="absolute" style="left: 50%"> {{-- Center a dropdown panel --}}
                                <div x-ref="panel"
                                     x-show="openDropdown"
                                     x-transition.origin.top.left
                                     @click.outside="close($refs.button)"
                                     :id="$id('dropdown-button')"
                                     x-cloak
                                     class="dropdown-panel relative"
                                     style="left: -50%" {{-- Center a dropdown panel --}}
                                >

                                    <button @click="
                                                openModal = true; {{-- Open the modal --}}
                                                item = index; {{-- Identify the item we are corrently editing --}}
                                                {{-- Replace the previous vaccinationProtocol with the current, don't assign object directly (modalVaccinationProtocol = vaccinationProtocol) to avoid reactiveness --}}
                                                modalVaccinationProtocol = new VaccinationProtocol(vaccinationProtocol)
                                                newVaccinationProtocol = false; {{-- This vaccinationProtocol is already created --}}
                                            "
                                            @click.prevent
                                            class="dropdown-button"
                                    >
                                        {{ __('forms.edit') }}
                                    </button>

                                    <button @click.prevent="vaccinationProtocols.splice(index, 1); close($refs.button)"
                                            class="dropdown-button dropdown-delete"
                                    >
                                        {{ __('forms.delete') }}
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
            {{-- Button to trigger the modal --}}
            <button @click.prevent="
                        openModal = true; {{-- Open the Modal --}}
                        newVaccinationProtocol = true; {{-- We are adding a new vaccinationProtocol --}}
                        modalVaccinationProtocol = new VaccinationProtocol(); {{-- Replace the data of the previous vaccinationProtocol with a new one--}}
                    "
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                     viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14m-7 7V5"
                    />
                </svg>
                {{ __('forms.add') }}
            </button>

            {{-- Modal --}}
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
                    <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/25"></div>

                    {{-- Panel --}}
                    <div x-show="openModal"
                         x-transition
                         @click="openModal = false"
                         class="relative flex min-h-screen items-center justify-center p-4"
                    >
                        <div @click.stop
                             x-trap.noscroll.inert="openModal"
                             class="modal-content h-fit w-full lg:max-w-4xl"
                        >
                            {{-- Title --}}
                            <h3 class="modal-header" :id="$id('modal-title')">{{ __('forms.add') }}</h3>

                            {{-- Content --}}
                            <form>
                                <template x-for="(targetDisease, index) in modalVaccinationProtocol.targetDiseases" :key="index">
                                    <div class="form-row-modal md:mb-0">
                                        <div class="form-group group">
                                            <label :for="'vaccinationTargetDisease-' + index" class="label-modal">
                                                {{ __('patients.target_diseases') }}
                                            </label>
                                            <select x-model="targetDisease.coding[0].code"
                                                    :id="'vaccinationTargetDisease-' + index"
                                                    class="input-modal"
                                                    required
                                            >
                                                <option selected>{{ __('forms.select') }}</option>
                                                @foreach($this->dictionaries['eHealth/vaccination_target_diseases'] as $key => $vaccinationTargetDisease)
                                                    <option value="{{ $key }}" wire:key="{{ $key }}">
                                                        {{ $vaccinationTargetDisease }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <p class="text-error text-xs"
                                               x-show="!Object.keys(vaccinationTargetDiseasesDictionary).includes(targetDisease.coding[0].code)"
                                            >
                                                {{ __('forms.field_empty') }}
                                            </p>
                                        </div>

                                        <!-- Remove Button -->
                                        <template x-if="index == modalVaccinationProtocol.targetDiseases.length - 1 & index != 0">
                                            <button type="button"
                                                    @click="modalVaccinationProtocol.targetDiseases.pop(), index--"
                                                    class="item-remove"
                                            >
                                                <svg>
                                                    <use xlink:href="#svg-minus"></use>
                                                </svg>
                                                {{ __('forms.delete') }}
                                            </button>
                                        </template>
                                        <!-- Add Button -->
                                        <template x-if="index === modalVaccinationProtocol.targetDiseases.length - 1">
                                            <button type="button"
                                                    @click="modalVaccinationProtocol.targetDiseases.push({ coding: [{ system: 'eHealth/vaccination_target_diseases', code: '' }] })"
                                                    class="item-add lg:justify-self-start"
                                                    :class="{ 'lg:justify-self-start': index > 0 }"
                                            >
                                                <svg>
                                                    <use xlink:href="#svg-plus"></use>
                                                </svg>
                                                {{ __('forms.add') }}
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                <div class="form-row-modal">
                                    <div>
                                        <label for="authority" class="label-modal">
                                            {{ __('patients.protocol_author') }}
                                        </label>
                                        <select x-model="modalVaccinationProtocol.authority.coding[0].code"
                                                id="authority"
                                                class="input-modal"
                                                type="text"
                                                required
                                        >
                                            <option selected>{{ __('forms.select') }}</option>
                                            @foreach($this->dictionaries['eHealth/vaccination_authorities'] as $key => $vaccinationAuthority)
                                                <option value="{{ $key }}" wire:key="{{ $key }}">
                                                    {{ $vaccinationAuthority }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- Check if the picked value is the one from the dictionary --}}
                                        <p class="text-error text-xs"
                                           x-show="!Object.keys(vaccinationAuthoritiesDictionary).includes(modalVaccinationProtocol.authority.coding[0].code)"
                                        >
                                            {{ __('forms.field_empty') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="form-row-modal">
                                    <div class="form-group group">
                                        <label for="doseSequence" class="label-modal">
                                            {{ __('patients.dose_sequence') }}
                                        </label>
                                        <input x-model="modalVaccinationProtocol.doseSequence"
                                               type="number"
                                               name="doseSequence"
                                               id="doseSequence"
                                               class="input-modal"
                                               autocomplete="off"
                                               required
                                        >
                                    </div>
                                </div>

                                <div class="form-row-modal">
                                    <div class="form-group group">
                                        <label for="series" class="label-modal">
                                            {{ __('patients.immunization_series') }}
                                        </label>
                                        <input x-model="modalVaccinationProtocol.series"
                                               type="text"
                                               name="series"
                                               id="series"
                                               class="input-modal"
                                               autocomplete="off"
                                               required
                                        >
                                    </div>
                                </div>

                                <div class="form-row-modal">
                                    <div class="form-group group">
                                        <label for="seriesDoses" class="label-modal">
                                            {{ __('patients.series_of_doses_by_protocol') }}
                                        </label>
                                        <input x-model="modalVaccinationProtocol.seriesDoses"
                                               type="number"
                                               name="seriesDoses"
                                               id="seriesDoses"
                                               class="input-modal"
                                               autocomplete="off"
                                               required
                                        >
                                    </div>
                                </div>

                                <div class="form-row-modal">
                                    <div class="form-group group">
                                        <label for="description" class="label-modal">
                                            {{ __('patients.protocol_description') }}
                                        </label>
                                        <textarea class="textarea"
                                                  x-model="modalVaccinationProtocol.description"
                                                  id="description"
                                                  name="description"
                                                  rows="4"
                                                  placeholder="{{ __('patients.write_comment_here') }}"
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between space-x-2">
                                    <button @click.prevent
                                            type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{ __('forms.cancel') }}
                                    </button>

                                    <button @click.prevent
                                            @click="
                                                newVaccinationProtocol !== false
                                                ? modalImmunization.vaccinationProtocols.push(modalVaccinationProtocol)
                                                : modalImmunization.vaccinationProtocols[item] = modalVaccinationProtocol;

                                                openModal = false;
                                            "
                                            class="button-primary"
                                            :disabled="!(modalVaccinationProtocol.authority.coding[0].code.trim().length > 0)"
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
    /**
     * Representation of the user's personal VaccinationProtocol
     */
    class VaccinationProtocol {
        doseSequence;
        description;
        authority = {
            coding: [{ system: 'eHealth/vaccination_authorities', code: '' }],
            text: ''
        };
        series;
        seriesDoses;
        targetDiseases = [
            {
                coding: [{ system: 'eHealth/vaccination_target_diseases', code: '' }],
            }
        ];

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, JSON.parse(JSON.stringify(obj)));
            }
        }
    }
</script>

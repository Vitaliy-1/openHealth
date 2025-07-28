<div class="overflow-x-auto relative" id="clinical-impressions-section">
    <fieldset class="fieldset"
              {{-- Binding ClinicalImpression to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  clinicalImpressions: $wire.entangle('form.clinicalImpressions'),
                  modalClinicalImpression: new ClinicalImpression(),
                  newClinicalImpression: false,
                  item: 0,
                  dictionary: $wire.dictionaries['eHealth/clinical_impression_patient_categories']
              }"
    >
        <legend class="legend">
            <h2>{{ __('patients.clinical_impressions') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
                <th scope="col" class="th-input">{{ __('patients.date') }}</th>
                <th scope="col" class="th-input">{{ __('forms.action') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(clinicalImpression, index) in clinicalImpressions">
                <tr>
                    <td class="td-input"
                        x-text="`${ clinicalImpression.code.coding[0].code } - ${ dictionary[clinicalImpression.code.coding[0].code] }`"
                    ></td>
                    <td class="td-input" x-text="clinicalImpression.effectivePeriodStartDate"></td>
                    <td class="td-input">
                        {{-- That all that is needed for the dropdown --}}
                        <div x-data="{
                                 openDropdown: false,
                                 toggle() {
                                     if (this.openDropdown) {
                                         return this.close();
                                     }

                                     this.$refs.button.focus();

                                     this.openDropdown = true;
                                 },
                                 close(focusAfter) {
                                     if (!this.openDropdown) return;

                                     this.openDropdown = false;

                                     focusAfter && focusAfter.focus();
                                 }
                             }"
                             @keydown.escape.prevent.stop="close($refs.button)"
                             @focusin.window="!$refs.panel.contains($event.target) && close()"
                             x-id="['dropdown-button']"
                             class="relative"
                        >
                            {{-- Dropdown Button --}}
                            <button x-ref="button"
                                    @click="toggle()"
                                    :aria-expanded="openDropdown"
                                    :aria-controls="$id('dropdown-button')"
                                    type="button"
                                    class="cursor-pointer"
                            >
                                <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                     viewBox="0 0 24 24"
                                >
                                    <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"
                                    />
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

                                    <button @click.prevent="
                                                item = index; {{-- Identify the item we are corrently editing --}}
                                                {{-- Replace the previous clinicalImpression with the current, don't assign object directly (modalClinicalImpression = clinicalImpression) to avoid reactiveness --}}
                                                modalClinicalImpression = JSON.parse(JSON.stringify(clinicalImpressions[index]));
                                                newClinicalImpression = false; {{-- This clinical impression is already created --}}

                                                $nextTick(() => {
                                                    const drawer = document.getElementById('clinical-impression-drawer-right');
                                                    drawer.classList.remove('translate-x-full'); {{-- Open manually --}}
                                                    drawer.scrollTop = 0; {{-- Move scroll to the top --}}
                                                });
                                            "
                                            class="dropdown-button"
                                    >
                                        {{ __('forms.edit') }}
                                    </button>

                                    <button @click.prevent="clinicalImpressions.splice(index, 1); close($refs.button)"
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
                        newClinicalImpression = true; {{-- We are adding a new clinicalImpression --}}
                        modalClinicalImpression = new ClinicalImpression(); {{-- Replace the data of the previous clinicalImpression with a new one--}}
                        $wire.problems = [];
                        $wire.findings = [];

                        $nextTick(() => {
                            const drawer = document.getElementById('clinical-impression-drawer-right');
                            drawer.scrollTop = 0; {{-- Move scroll to the top --}}
                        });
                    "
                    class="item-add my-5"
                    data-drawer-target="clinical-impression-drawer-right"
                    data-drawer-show="clinical-impression-drawer-right"
                    data-drawer-placement="right"
                    data-drawer-body-scrolling="false"
                    aria-controls="clinical-impression-drawer-right"
            >
                {{ __('forms.add') }}
            </button>

            {{-- Modal --}}
            <template x-teleport="body"> {{-- This moves the modal at the end of the body tag --}}
                <div id="clinical-impression-drawer-right"
                     class="fixed top-0 right-0 z-40 h-screen pt-20 p-4 overflow-y-auto transition-transform translate-x-full bg-white w-4/5 dark:bg-gray-800"
                     tabindex="-1"
                     aria-labelledby="clinical-impression-drawer-right"
                     wire:ignore
                >
                    {{-- Title --}}
                    <h3 class="modal-header" :id="$id('modal-title')">{{ __('patients.clinical_impression') }}</h3>

                    {{-- Content --}}
                    <form>
                        @include('livewire.encounter.clinical-impression-parts.main-information')
                        @include('livewire.encounter.clinical-impression-parts.problems')
                        @include('livewire.encounter.clinical-impression-parts.findings')
                        @include('livewire.encounter.clinical-impression-parts.supporting-info')
                        @include('livewire.encounter.clinical-impression-parts.additional-information')

                        <div class="mt-6 flex justify-between space-x-2">
                            <button type="button"
                                    class="button-minor"
                                    data-drawer-hide="clinical-impression-drawer-right"
                                    aria-controls="clinical-impression-drawer-right"
                            >
                                {{ __('forms.cancel') }}
                            </button>

                            <button @click.prevent="
                                        newClinicalImpression !== false
                                            ? clinicalImpressions.push(modalClinicalImpression)
                                            : clinicalImpressions[item] = modalClinicalImpression;
                                    "
                                    class="button-primary"
                                    data-drawer-hide="clinical-impression-drawer-right"
                                    :disabled="!modalClinicalImpression.code.coding[0].code.trim()"
                            >
                                {{ __('forms.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </fieldset>
</div>

<script>
    /**
     * Representation of the user's personal clinicalImpression
     */
    class ClinicalImpression {
        code = {
            coding: [{ system: 'eHealth/clinical_impression_patient_categories', code: '' }],
            text: ''
        };
        assessor = {
            identifier: {
                type: {
                    coding: [{ system: 'eHealth/resources', code: 'employee' }],
                    text: ''
                }
            }
        };
        previousList = [];
        problems = [];
        findings = [];
        supportingInfo = [];
        supportingInfoEpisodes = [];

        // Create date
        #now = new Date();
        #endTime = new Date(this.#now.getTime() + 15 * 60 * 1000); // add 15 minutes

        effectivePeriodStartDate = this.#now.toISOString().split('T')[0];
        effectivePeriodStartTime = this.#now.toLocaleTimeString('uk-UA', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
        effectivePeriodEndDate = this.#endTime.toISOString().split('T')[0];
        effectivePeriodEndTime = this.#endTime.toLocaleTimeString('uk-UA', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });

        constructor(obj = null) {
            if (obj) {
                this.clinicalImpression = JSON.parse(JSON.stringify(obj.clinicalImpression || obj));
            }
        }
    }
</script>

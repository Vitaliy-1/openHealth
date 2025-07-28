@use('Carbon\CarbonImmutable')

<div class="relative"> {{-- This required for table overflow scrolling --}}
    <fieldset class="fieldset"
              {{-- Binding Finding to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  openModal: false,
                  modalSupportingInfo: new SupportingInfo(),
                  newSupportingInfo: false,
                  item: 0
              }"
    >
        <legend class="legend">
            <h2>{{ __('patients.supporting_medical_information') }}</h2>
        </legend>

        @include('livewire.encounter.clinical-impression-parts.supporting-info-episodes')

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('patients.date') }}</th>
                <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
                <th scope="col" class="th-input">{{ __('forms.action') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(supporting, index) in modalClinicalImpression.supportingInfo">
                <tr>
                    <td class="td-input"
                        x-text="new Date(supporting.inserted_at).toLocaleDateString('uk-UA')"
                    ></td>
                    <td class="td-input"
                        x-text="(() => {
                            const dictName = $wire.dictionaries['eHealth/LOINC/observation_codes'][supporting.code] ||
                                             $wire.dictionaries['eHealth/ICF/classifiers'][supporting.code] ||
                                             $wire.dictionaries['eHealth/ICPC2/condition_codes'][supporting.code];

                            if (dictName) {
                                return `${ supporting.type } : ${ supporting.code } - ${ dictName }`;
                            }

                            const service = Object.values($wire.dictionaries['custom/services']).find(service => service.id === supporting.code);
                            return service ? `${ supporting.type } : ${ service.code } / ${ service.name }` : `${ supporting.type } : ${ supporting.code }`;
                        })()"
                    ></td>
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

                                     focusAfter && focusAfter.focus()
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
                                                {{-- Replace the previous finding with the current, don't assign object directly (modalSupportingInfo = finding) to avoid reactiveness --}}
                                                modalSupportingInfo = new SupportingInfo(supporting);
                                                newSupportingInfo = false; {{-- This finding is already created --}}
                                            "
                                            @click.prevent
                                            class="dropdown-button"
                                    >
                                        {{ __('forms.edit') }}
                                    </button>

                                    <button
                                        @click.prevent="modalClinicalImpression.supportingInfo.splice(index, 1); close($refs.button);"
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
                        newSupportingInfo = true; {{-- We are adding a new finding --}}
                        modalSupportingInfo = new SupportingInfo(); {{-- Replace the data of the previous finding with a new one--}}
                        {{-- Set to empty to hide previously found data --}}
                        $wire.encounters = [];
                        $wire.procedures = [];
                        $wire.diagnosticReports = [];
                    "
                    class="item-add my-5"
            >
                {{ __('forms.add') }} {{ mb_strtolower(__('patients.medical_record')) }}
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
                            <form x-data="{ selectedSupportingInfoIds: [] }">
                                {{-- Episode info in which the search happens --}}
                                <div class="form-row-3">
                                    {{-- Choose for what search --}}
                                    <div class="form-group group">
                                        <select x-model="modalSupportingInfo.medicalRecordType"
                                                class="input-modal peer"
                                                name="recordType"
                                                id="recordType"
                                        >
                                            <option value="" selected>
                                                {{ __('forms.select') }} {{ __('patients.medical_records_type') }}
                                            </option>
                                            <option value="encounter">{{ __('patients.interaction') }}</option>
                                            <option value="procedure">{{ __('patients.procedure') }}</option>
                                            <option value="diagnosticReport">
                                                {{ __('patients.diagnostic_report') }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group group">
                                        <select x-model="modalSupportingInfo.selectedEpisodeId"
                                                id="episodeId"
                                                class="input-modal peer"
                                        >
                                            <option value="" selected>
                                                {{ __('forms.select') }} {{ mb_strtolower(__('patients.episode')) }}
                                            </option>
                                            @foreach($episodes as $key => $episode)
                                                <option value="{{ $episode['id'] }}" wire:key="{{ $key }}">
                                                    {{ $episode['name'] }} ({{ __('patients.' . $episode['status']) }})
                                                    від {{ CarbonImmutable::parse($episode['inserted_at'])->format('d.m.Y') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Search button --}}
                                    <div>
                                        <button class="flex items-center gap-2 button-primary"
                                                @click.prevent="$wire.searchSupportingInfo(modalSupportingInfo.medicalRecordType, modalSupportingInfo.selectedEpisodeId)"
                                                :disabled="!(modalSupportingInfo.medicalRecordType && modalSupportingInfo.selectedEpisodeId)"
                                        >
                                            <svg width="16" height="16">
                                                <use xlink:href="#svg-search"></use>
                                            </svg>
                                            <span>{{ __('patients.search') }}</span>
                                        </button>
                                    </div>

                                    <x-forms.loading/>
                                </div>

                                {{-- A table that shows the results of founded encounters --}}
                                <template x-if="$wire.encounters.length">
                                    <div class="table-container">
                                        <div class="overflow-visible">
                                            <table class="table-base">
                                                <thead class="table-header">
                                                <tr>
                                                    <th scope="col" class="th-input">{{ __('patients.date') }}</th>
                                                    <th scope="col" class="th-input">
                                                        {{ __('patients.code_and_name') }}
                                                    </th>
                                                    <th scope="col" class="th-input">{{ __('forms.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <template x-for="encounter in $wire.encounters" :key="encounter.id">
                                                    <tr class="border-b dark:border-gray-700">
                                                        <th scope="row" class="table-cell-primary">
                                                            <div class="text-base"
                                                                 x-text="new Date(encounter.inserted_at).toLocaleDateString('uk-UA')"
                                                            ></div>
                                                        </th>

                                                        <td class="td-input">
                                                            <ul>
                                                                <template
                                                                    x-for="(diagnosis, index) in encounter.diagnoses.filter(diagnose => diagnose.role?.coding?.[0]?.code === 'primary')"
                                                                    :key="index"
                                                                >
                                                                    <li x-text="`${ diagnosis.code.coding[0].code} - ${
                                                                            $wire.dictionaries['eHealth/ICPC2/condition_codes'][diagnosis.code.coding[0].code] ||
                                                                            $wire.dictionaries['eHealth/ICD10_AM/condition_codes'][diagnosis.code.coding[0].code]
                                                                        }`"
                                                                    ></li>
                                                                </template>
                                                            </ul>
                                                        </td>

                                                        <td class="td-input">
                                                            <button @click.prevent="
                                                                    const id = encounter.id;
                                                                    const index = selectedSupportingInfoIds.indexOf(id);
                                                                    if (index === -1) {
                                                                        selectedSupportingInfoIds.push(id);
                                                                    } else {
                                                                        selectedSupportingInfoIds.splice(index, 1); // toggle off
                                                                    }"
                                                                    class="button-primary w-28"
                                                                    x-text="selectedSupportingInfoIds.includes(encounter.id)
                                                                        ? '{{ __('patients.added') }}'
                                                                        : '{{ __('forms.add') }}'"
                                                            >
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                {{-- A table that shows the results of founded procedures --}}
                                <template x-if="$wire.procedures.length > 0">
                                    <div class="table-container">
                                        <div class="overflow-visible">
                                            <table class="table-base">
                                                <thead class="table-header">
                                                <tr>
                                                    <th scope="col" class="th-input">{{ __('patients.date') }}</th>
                                                    <th scope="col" class="th-input">
                                                        {{ __('patients.code_and_name') }}
                                                    </th>
                                                    <th scope="col" class="th-input">{{ __('forms.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <template x-for="procedure in $wire.procedures" :key="procedure.id">
                                                    <tr class="border-b dark:border-gray-700">
                                                        <th scope="row" class="table-cell-primary">
                                                            <div class="text-base"
                                                                 x-text="new Date(procedure.inserted_at).toLocaleDateString('uk-UA')"
                                                            ></div>
                                                        </th>
                                                        <td class="td-input"
                                                            x-text="`${ procedure.code.display_value }`"
                                                        ></td>
                                                        <td class="td-input">
                                                            <button @click.prevent="
                                                                        const id = procedure.id;
                                                                        const index = selectedSupportingInfoIds.indexOf(id);

                                                                        if (index === -1) {
                                                                            selectedSupportingInfoIds.push(id);
                                                                        } else {
                                                                            selectedSupportingInfoIds.splice(index, 1); // toggle off
                                                                        }
                                                                    "
                                                                    class="button-primary w-28"
                                                                    x-text="selectedSupportingInfoIds.includes(procedure.id)
                                                                        ? '{{ __('patients.added') }}'
                                                                        : '{{ __('forms.add') }}'"
                                                            >
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                {{-- A table that shows the results of founded diagnosticReports --}}
                                <template x-if="$wire.diagnosticReports.length > 0">
                                    <div class="table-container">
                                        <div class="overflow-visible">
                                            <table class="table-base">
                                                <thead class="table-header">
                                                <tr>
                                                    <th scope="col" class="th-input">{{ __('patients.date') }}</th>
                                                    <th scope="col" class="th-input">
                                                        {{ __('patients.code_and_name') }}
                                                    </th>
                                                    <th scope="col" class="th-input">{{ __('forms.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <template x-for="diagnosticReport in $wire.diagnosticReports"
                                                          :key="diagnosticReport.id"
                                                >
                                                    <tr class="border-b dark:border-gray-700">
                                                        <th scope="row" class="table-cell-primary">
                                                            <div class="text-base"
                                                                 x-text="new Date(diagnosticReport.inserted_at).toLocaleDateString('uk-UA')"
                                                            ></div>
                                                        </th>
                                                        <td class="td-input"
                                                            x-text="`${ diagnosticReport.code.display_value }`"
                                                        ></td>
                                                        <td class="td-input">
                                                            <button @click.prevent="
                                                                        const id = diagnosticReport.id;
                                                                        const index = selectedSupportingInfoIds.indexOf(id);

                                                                        if (index === -1) {
                                                                            selectedSupportingInfoIds.push(id);
                                                                        } else {
                                                                            selectedSupportingInfoIds.splice(index, 1); // toggle off
                                                                        }
                                                                    "
                                                                    class="button-primary w-28"
                                                                    x-text="selectedSupportingInfoIds.includes(diagnosticReport.id)
                                                                        ? '{{ __('patients.added') }}'
                                                                        : '{{ __('forms.add') }}'"
                                                            >
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                {{-- Action buttons --}}
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
                                                {{-- Add encounters --}}
                                                $wire.encounters
                                                    .filter(encounter => selectedSupportingInfoIds.includes(encounter.id))
                                                    .map(encounter => {
                                                        const primaryDiagnosis = encounter.diagnoses.find(diagnosis =>
                                                            diagnosis.role.coding[0].code === 'primary'
                                                        );

                                                        return {
                                                            id: encounter.id,
                                                            inserted_at: encounter.inserted_at,
                                                            code: primaryDiagnosis.code.coding[0].code,
                                                            type: 'encounter',
                                                            ...modalSupportingInfo
                                                        };
                                                    })
                                                    .forEach(item => modalClinicalImpression.supportingInfo.push(item));

                                                {{-- Add procedures --}}
                                                $wire.procedures
                                                    .filter(procedure => selectedSupportingInfoIds.includes(procedure.id))
                                                    .map(procedure => ({
                                                        id: procedure.id,
                                                        inserted_at: procedure.inserted_at,
                                                        code: procedure.code.identifier.value,
                                                        type: 'procedure',
                                                        ...modalSupportingInfo
                                                    }))
                                                    .forEach(item => modalClinicalImpression.supportingInfo.push(item));

                                                {{-- Add diagnostic reports --}}
                                                $wire.diagnosticReports
                                                    .filter(diagnosticReport => selectedSupportingInfoIds.includes(diagnosticReport.id))
                                                    .map(diagnosticReport => ({
                                                        id: diagnosticReport.id,
                                                        inserted_at: diagnosticReport.inserted_at,
                                                        code: diagnosticReport.code.identifier.value,
                                                        type: 'diagnostic_report',
                                                        ...modalSupportingInfo
                                                    }))
                                                    .forEach(item => modalClinicalImpression.supportingInfo.push(item));

                                                openModal = false;
                                            "
                                            class="button-primary"
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
     * Representation of the user's personal SupportingInfo
     */
    class SupportingInfo {
        medicalRecordType = '';
        selectedEpisodeId = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, JSON.parse(JSON.stringify(obj)));
            }
        }
    }
</script>

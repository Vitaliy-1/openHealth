@use('Carbon\CarbonImmutable')

<div class="relative"> {{-- This required for table overflow scrolling --}}
    <div x-data="{
              openModal: false,
              modalSupportingInfoEpisode: new SupportingInfoEpisode(),
              newSupportingInfoEpisode: false,
              item: 0
          }"
    >

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('patients.date') }}</th>
                <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
                <th scope="col" class="th-input">{{ __('forms.action') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(supporting, index) in modalClinicalImpression.supportingInfoEpisodes">
                <tr>
                    <td class="td-input"
                        x-text="new Date(supporting.created_at).toLocaleDateString('uk-UA')"
                    ></td>
                    <td class="td-input"
                        x-text="'Епізод' + `${ supporting.code ? ' : ' + supporting.code + ' ' + (
                            $wire.dictionaries['eHealth/LOINC/observation_codes'][supporting.code] ||
                            $wire.dictionaries['eHealth/ICF/classifiers'][supporting.code] ||
                            $wire.dictionaries['eHealth/ICPC2/condition_codes'][supporting.code] || ''
                        ) : ''}`"
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
                                                {{-- Replace the previous finding with the current, don't assign object directly (modalSupportingInfoEpisode = finding) to avoid reactiveness --}}
                                                modalSupportingInfoEpisode = new SupportingInfoEpisode(supporting);
                                                newSupportingInfoEpisode = false; {{-- This finding is already created --}}
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
                        newSupportingInfoEpisode = true; {{-- We are adding a new finding --}}
                        modalSupportingInfoEpisode = new SupportingInfoEpisode(); {{-- Replace the data of the previous finding with a new one--}}
                    "
                    class="item-add my-5"
            >
                {{ __('forms.add') }} {{ mb_strtolower(__('patients.episode')) }}
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
                                {{-- A table that shows the results of founded episodes --}}
                                <template x-if="$wire.episodes.length > 0">
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
                                                <template x-for="episode in $wire.episodes" :key="episode.id">
                                                    <tr class="border-b dark:border-gray-700">
                                                        <th scope="row" class="table-cell-primary">
                                                            <div class="text-base"
                                                                 x-text="new Date(episode.created_at).toLocaleDateString('uk-UA')"
                                                            >
                                                            </div>
                                                        </th>

                                                        <td class="td-input">
                                                            <ul>
                                                                <template
                                                                    x-for="(diagnosis, index) in episode.current_diagnoses.filter(diagnose => diagnose.role.coding[0].code === 'primary')"
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
                                                                    const id = episode.id;
                                                                    const index = selectedSupportingInfoIds.indexOf(id);
                                                                    if (index === -1) {
                                                                        selectedSupportingInfoIds.push(id);
                                                                    } else {
                                                                        selectedSupportingInfoIds.splice(index, 1); // toggle off
                                                                    }"
                                                                    class="button-primary w-28"
                                                                    x-text="selectedSupportingInfoIds.includes(episode.id)
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
                                                modalClinicalImpression.supportingInfoEpisodes = $wire.episodes
                                                    .filter(episode => selectedSupportingInfoIds.includes(episode.id))
                                                    .map(episode => {
                                                        const currentDiagnosis = episode.current_diagnoses.find(diagnosis =>
                                                            diagnosis.role.coding[0].code === 'primary'
                                                        );

                                                        return {
                                                            id: episode.id,
                                                            created_at: episode.created_at,
                                                            code: currentDiagnosis?.code?.coding?.[0]?.code ?? '',
                                                            type: 'episode_of_care'
                                                        };
                                                    });

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
    </div>
</div>

<script>
    /**
     * Representation of the user's personal SupportingInfoEpisode
     */
    class SupportingInfoEpisode {
        constructor(obj = null) {
            if (obj) {
                Object.assign(this, JSON.parse(JSON.stringify(obj)));
            }
        }
    }
</script>

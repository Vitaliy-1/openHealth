<div class="overflow-x-auto relative" id="immunization-section">
    <fieldset class="fieldset"
              x-data="{
                  actions: $wire.entangle('form.encounter.actions'),
                  openModal: false,
                  newAction: false,
                  item: 0,
                  dictionary: $wire.dictionaries['eHealth/ICPC2/actions']
              }"
    >
        <legend class="legend">
            <h2>{{ __('patients.immunization') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
                <th scope="col" class="th-input">{{ __('patients.dosage') }}</th>
                <th scope="col" class="th-input">{{ __('patients.execution_state') }}</th>
                <th scope="col" class="th-input">{{ __('patients.reason') }}</th>
                <th scope="col" class="th-input">{{ __('patients.date') }}</th>
                <th scope="col" class="th-input">{{ __('forms.action') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(action, index) in actions">
                <tr>
                    <td class="td-input"
                        x-text="`${ action.coding[0].code } - ${ dictionary[action.coding[0].code] }`"
                    ></td>
                    <td class="td-input"
                        x-text="`${ action.text }`"
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
                                                openModal = true; {{-- Open the modal --}}
                                                item = index; {{-- Identify the item we are corrently editing --}}
                                                {{-- Replace the previous action with the current, don't assign object directly (modalAction = action) to avoid reactiveness --}}
                                                modalAction = new Action(action);
                                                newAction = false; {{-- This action is already created --}}
                                            "
                                            class="dropdown-button"
                                    >
                                        {{ __('forms.edit') }}
                                    </button>

                                    <button @click.prevent="actions.splice(index, 1); close($refs.button)"
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

        <a href="{{ route('immunization.create', ['id' => $id]) }}" class="button-primary">
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 viewBox="0 0 24 24"
            >
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"
                />
            </svg>
            {{ __('forms.add') }}
        </a>
    </fieldset>
</div>

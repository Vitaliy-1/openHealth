{{-- Component to input values to the table through the Modal, built with Alpine --}}
<div class="overflow-x-auto relative" id="reasons-section"> {{-- This required for table overflow scrolling --}}
    <fieldset class="fieldset"
              {{-- Binding evidenceCodes to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  reasons: $wire.entangle('form.encounter.reasons'),
                  openModal: false,
                  modalReason: new Reason(),
                  newReason: false,
                  item: 0,
                  dictionary: $wire.dictionaries['eHealth/ICPC2/reasons']
              }"
    >
        <legend class="legend">
            <h2>{{ __('patients.reasons_for_visit') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
                <th scope="col" class="th-input">{{ __('forms.comment') }}</th>
                <th scope="col" class="th-input">{{ __('forms.action') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(reason, index) in reasons">
                <tr>
                    <td class="td-input"
                        x-text="`${ reason.coding[0].code } - ${ dictionary[reason.coding[0].code] }`"
                    ></td>
                    <td class="td-input"
                        x-text="`${ reason.text }`"
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
                                                {{-- Replace the previous reason with the current, don't assign object directly (modalReason = reason) to avoid reactiveness --}}
                                                modalReason = new Reason(reason);
                                                newReason = false; {{-- This reason is already created --}}
                                            "
                                            class="dropdown-button"
                                    >
                                        {{ __('forms.edit') }}
                                    </button>

                                    <button @click.prevent="reasons.splice(index, 1); close($refs.button)"
                                            class="dropdown-button dropdown-delete"
                                    >
                                        {{ __('forms.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                </tr>
            </template>
            </tbody>
        </table>

        <div>
            {{-- Button to trigger the modal --}}
            <button @click.prevent="
                        openModal = true; {{-- Open the Modal --}}
                        newReason = true; {{-- We are adding a new reason --}}
                        modalReason = new Reason() {{-- Replace the data of the previous reason with a new one--}}
                    "
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                     viewBox="0 0 24 24"
                >
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
                             class="modal-content h-fit"
                        >
                            {{-- Title --}}
                            <h3 class="modal-header" :id="$id('modal-title')">{{ __('patients.reason_for_visit') }}</h3>

                            {{-- Content --}}
                            <form>
                                <div class="form-row-modal">
                                    <div>
                                        <label for="reasonCode" class="label-modal">
                                            {{ __('patients.icpc-2_status_code') }}
                                        </label>
                                        <select x-model="modalReason.coding[0].code"
                                                id="reasonCode"
                                                class="input-modal"
                                                type="text"
                                                required
                                        >
                                            <option value="" disabled selected>{{ __('forms.select') }}</option>
                                            @foreach($this->dictionaries['eHealth/ICPC2/reasons'] as $key => $reason)
                                                <option value="{{ $key }}">{{ $key }} - {{ $reason }}</option>
                                            @endforeach
                                        </select>
                                        {{-- Check if the picked value is the one from the dictionary --}}
                                        <p class="text-error text-xs"
                                           x-show="!Object.keys(dictionary).includes(modalReason.coding[0].code)"
                                        >
                                            {{ __('forms.field_empty') }}
                                        </p>
                                    </div>

                                    <div>
                                        <textarea x-model="modalReason.text"
                                                  id="reasonComment"
                                                  name="reasonComment"
                                                  class="textarea"
                                                  rows="4"
                                                  placeholder="{{ __('patients.write_comment_here') }}"
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between space-x-2">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{ __('forms.cancel') }}
                                    </button>

                                    <button @click.prevent="
                                                newReason !== false
                                                ? reasons.push(modalReason)
                                                : reasons[item] = modalReason;

                                                openModal = false;
                                            "
                                            class="button-primary"
                                            :disabled="!(modalReason.coding[0].code.trim().length > 0)"
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
     * Representation of the user's personal reason
     */
    class Reason {
        coding = [
            { system: 'eHealth/ICPC2/reasons', code: '' }
        ];
        text = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

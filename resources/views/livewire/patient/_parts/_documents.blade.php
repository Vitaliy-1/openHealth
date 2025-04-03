{{-- Component to input values to the table through the Modal, built with Alpine --}}

<div class="relative"> {{-- This required for table overflow scrolling --}}
    <fieldset class="fieldset"
              {{-- Binding documents to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  documents: $wire.entangle('form.documents'),
                  openModal: false,
                  modalDocument: new Doc(),
                  newDocument: false,
                  item: 0,
                  dictionary: $wire.dictionaries['DOCUMENT_TYPE']
              }"
    >
        <legend class="legend">
            <h2>{{ __('patients.identity_document') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.type') }}</th>
                <th scope="col" class="th-input">{{ __('forms.number') }} </th>
                <th scope="col" class="th-input">{{ __('forms.issued_by') }}</th>
                <th scope="col" class="th-input">{{ __('forms.issued_at') }}</th>
                <th scope="col" class="th-input">{{ __('forms.valid_until') }}</th>
                <th scope="col" class="th-input">{{ __('forms.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(document, index) in documents">
                <tr>
                    <td class="td-input" x-text="dictionary[document.type]"></td>
                    <td class="td-input" x-text="document.number"></td>
                    <td class="td-input" x-text="document.issuedBy"></td>
                    <td class="td-input" x-text="document.issuedAt"></td>
                    <td class="td-input" x-text="document.expirationDate"></td>
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
                                    x-on:click="toggle()"
                                    :aria-expanded="openDropdown"
                                    :aria-controls="$id('dropdown-button')"
                                    type="button"
                                    class="cursor-pointer"
                            >
                                <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" aria-hidden="true"
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

                                    <button @click.prevent="
                                                    openModal = true; {{-- Open the modal --}}
                                                    item = index; {{-- Identify the item we are corrently editing --}}
                                                    {{-- Replace the previous document with the current, don't assign object directly (modalDocument = document) to avoid reactiveness --}}
                                                    modalDocument = new Doc(document)
                                                    newDocument = false; {{-- This document is already created --}}
                                                "
                                            class="dropdown-button"
                                    >
                                        {{ __('forms.edit') }}
                                    </button>

                                    <button @click.prevent="documents.splice(index, 1); close($refs.button)"
                                            class="dropdown-button dropdown-delete">
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
                        newDocument = true; {{-- We are adding a new document --}}
                        modalDocument = new Doc() {{-- Replace the data of the previous document with a new one--}}
                    "
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                     viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14m-7 7V5"/>
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
                             class="modal-content h-fit w-full lg:max-w-7xl"
                        >
                            {{-- Title --}}
                            <h3 class="modal-header" :id="$id('modal-title')">{{ __('forms.document') }}</h3>

                            {{-- Content --}}
                            <form>
                                <div class="form-row-modal">
                                    <div>
                                        <label for="documentType" class="label-modal">
                                            {{ __('forms.type') }}
                                        </label>
                                        <select x-model="modalDocument.type"
                                                id="documentType"
                                                class="input-modal"
                                                type="text"
                                                required
                                        >
                                            <option selected>{{ __('forms.select') }} *</option>
                                            @foreach($this->dictionaries['DOCUMENT_TYPE'] as $key => $documentType)
                                                <option value="{{ $key }}">{{ $documentType }}</option>
                                            @endforeach
                                        </select>
                                        {{-- Check if the picked value is the one from the dictionary --}}
                                        <p class="text-error text-xs"
                                           x-show="!Object.keys(dictionary).includes(modalDocument.type)"
                                        >
                                            {{ __('forms.field_empty') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label for="documentNumber" class="label-modal">
                                            {{ __('forms.document_number') }}
                                        </label>
                                        <input x-model="modalDocument.number"
                                               type="text"
                                               name="documentNumber"
                                               id="documentNumber"
                                               class="input-modal"
                                               autocomplete="off"
                                               required
                                        >
                                        <p class="text-error text-xs" x-show="!modalDocument.number.trim().length > 0">
                                            {{ __('forms.field_empty') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label for="documentIssuedBy" class="label-modal">
                                            {{ __('forms.document_issued_by') }}
                                        </label>
                                        <input x-model="modalDocument.issuedBy"
                                               type="text"
                                               name="documentIssuedBy"
                                               id="documentIssuedBy"
                                               class="input-modal"
                                               autocomplete="off"
                                        >
                                    </div>
                                    <div class="relative">
                                        <svg width="20" height="20"
                                             class="svg-input absolute left-1 !top-2/3 transform -translate-y-1/2 pointer-events-none"
                                        >
                                            <use xlink:href="#svg-calendar-week"></use>
                                        </svg>
                                        <label for="documentIssuedAt" class="label-modal">
                                            {{ __('forms.document_issued_at') }}
                                        </label>
                                        <input x-model="modalDocument.issuedAt"
                                               datepicker-max-date="{{ now()->format('Y-m-d') }}"
                                               type="text"
                                               name="documentIssuedAt"
                                               id="documentIssuedAt"
                                               class="input-modal datepicker-input"
                                               autocomplete="off"
                                        >
                                    </div>
                                    <div class="relative">
                                        <svg width="20" height="20"
                                             class="svg-input absolute left-1 !top-2/3 transform -translate-y-1/2 pointer-events-none"
                                        >
                                            <use xlink:href="#svg-calendar-week"></use>
                                        </svg>
                                        <label for="documentExpirationDate" class="label-modal">
                                            {{ __('forms.valid_until') }}
                                        </label>
                                        <input x-model="modalDocument.expirationDate"
                                               datepicker-min-date="{{ now()->format('Y-m-d') }}"
                                               type="text"
                                               name="documentExpirationDate"
                                               id="documentExpirationDate"
                                               class="input-modal datepicker-input"
                                               autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between space-x-2">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{ __('forms.cancel') }}
                                    </button>

                                    <button class="button-primary"
                                            @click.prevent="newDocument !== false ? documents.push(modalDocument) : documents[item] = modalDocument; openModal = false"
                                            :disabled="!(modalDocument.type.trim().length > 0 && modalDocument.number.trim().length > 0)"
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
     * Representation of the user's personal document
     */
    class Doc {
        type = '';
        number = '';
        issuedBy = '';
        issuedAt = '';
        expirationDate = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

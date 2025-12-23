{{-- Component to input values to the table through the Modal, built with Alpine --}}
<div class="relative"> {{-- This required for table overflow scrolling --}}
    <fieldset class="fieldset"
              {{-- Binding documents to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  documents: $wire.entangle('form.person.documents'),
                  openModal: false,
                  modalDocument: new Document(),
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
                                    class="cursor-pointer"
                            >
                                @icon('edit-user-outline', 'w-6 h-6 text-gray-800 dark:text-gray-200')
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
                                                    modalDocument = new Document(document);
                                                    newDocument = false; {{-- This document is already created --}}
                                                "
                                            class="dropdown-button"
                                    >
                                        {{ __('forms.edit') }}
                                    </button>

                                    <button @click.prevent="documents.splice(index, 1); close($refs.button);"
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
                        modalDocument = new Document(); {{-- Replace the data of the previous document with a new one--}}
                    "
                    class="item-add my-5"
            >
                {{ __('forms.add') }}
            </button>

            {{-- Modal --}}
            <template x-teleport="body"> {{-- This moves the modal at the end of the body tag --}}
                <div x-show="openModal"
                     x-cloak
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
                                    {{-- Type --}}
                                    <div>
                                        <label for="documentType" class="label-modal">{{ __('forms.type') }}
                                            <span class="text-red-600"> *</span>
                                        </label>
                                        <select x-model="modalDocument.type"
                                                id="documentType"
                                                class="input-modal"
                                                type="text"
                                                required
                                        >
                                            <option selected value="">{{ __('forms.select') }} *</option>
                                            @foreach($this->dictionaries['DOCUMENT_TYPE'] as $key => $documentType)
                                                <option value="{{ $key }}">{{ $documentType }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Issue number --}}
                                    <div>
                                        <label for="documentNumber" class="label-modal">
                                            {{ __('forms.document_number') }}
                                            <span class="text-red-600"> *</span>
                                        </label>
                                        <input x-model="modalDocument.number"
                                               type="text"
                                               name="documentNumber"
                                               id="documentNumber"
                                               class="input-modal"
                                               autocomplete="off"
                                               required
                                        >
                                    </div>

                                    {{-- Authority which issued --}}
                                    <div>
                                        <label for="documentIssuedBy" class="label-modal">
                                            {{ __('forms.document_issued_by') }}
                                            <span class="text-red-600"> *</span>
                                        </label>
                                        <input x-model="modalDocument.issuedBy"
                                               type="text"
                                               name="documentIssuedBy"
                                               id="documentIssuedBy"
                                               class="input-modal"
                                               autocomplete="off"
                                        >
                                    </div>

                                    {{-- The date when was issued --}}
                                    <div class="relative">
                                        @icon('calendar-week', 'w-5 h-5 svg-input absolute left-1 !top-2/3 transform -translate-y-1/2 pointer-events-none')

                                        <label for="documentIssuedAt" class="label-modal">
                                            {{ __('forms.document_issued_at') }}
                                            <span class="text-red-600"> *</span>
                                        </label>
                                        <input x-model="modalDocument.issuedAt"
                                               datepicker-max-date="{{ now()->format('d.m.Y') }}"
                                               datepicker-format="dd.mm.yyyy"
                                               type="text"
                                               name="documentIssuedAt"
                                               id="documentIssuedAt"
                                               class="input-modal datepicker-input"
                                               autocomplete="off"
                                        >
                                    </div>

                                    {{-- The date when expired --}}
                                    <div class="relative">
                                        @icon('calendar-week', 'w-5 h-5 svg-input absolute left-1 !top-2/3 transform -translate-y-1/2 pointer-events-none')

                                        <label for="documentExpirationDate" class="label-modal">
                                            {{ __('forms.valid_until') }}
                                            <span class="text-red-600"> *</span>
                                        </label>
                                        <input x-model="modalDocument.expirationDate"
                                               datepicker-min-date="{{ now()->format('d.m.Y') }}"
                                               datepicker-format="dd.mm.yyyy"
                                               type="text"
                                               name="documentExpirationDate"
                                               id="documentExpirationDate"
                                               class="input-modal datepicker-input"
                                               autocomplete="off"
                                        >
                                    </div>
                                </div>
                                <p class="text-sm text-gray-400 mb-2">
                                    {{ __('forms.form_required_note') }}
                                </p>
                                {{-- Action buttons --}}
                                <div class="mt-6 flex justify-between space-x-2">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{ __('forms.cancel') }}
                                    </button>

                                    <button class="button-primary"
                                            @click.prevent="
                                                newDocument !== false
                                                    ? documents.push(modalDocument)
                                                    : documents[item] = modalDocument;

                                                openModal = false;
                                            "
                                            :disabled="
                                                !modalDocument.type.trim() ||
                                                !modalDocument.number.trim() ||
                                                !modalDocument.issuedBy.trim() ||
                                                !modalDocument.issuedAt.trim()
                                            "
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
    class Document {
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

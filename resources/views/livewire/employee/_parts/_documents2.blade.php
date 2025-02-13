{{-- Component to input values to the table through the Modal, built with Alpine --}}

<div class="overflow-x-auto relative"> {{-- This required for table overflow scrolling --}}
    <fieldset class="fieldset"
              {{-- Binding documents to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  documents: $wire.entangle('employeeRequest.documents'),
                  openModal:false,
                  modalDocument: new Doc(),
                  newDocument: false,
                  item: 0
              }"
    >
        <legend class="legend">
            <h2>{{__('forms.documents')}}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
                <tr>
                    <th scope="col" class="th-input">{{ __('forms.documentType') }}</th>
                    <th scope="col" class="th-input">{{ __('forms.number') }} </th>
                    <th scope="col" class="th-input">{{ __('forms.issuedBy') }}</th>
                    <th scope="col" class="th-input">{{ __('forms.issuedAt') }}</th>
                    <th scope="col" class="th-input">{{ __('forms.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(document, index) in documents">
                    <tr>
                        <td class="td-input" x-text="document.type"></td>
                        <td class="td-input" x-text="document.number"></td>
                        <td class="td-input" x-text="document.issuedBy"></td>
                        <td class="td-input" x-text="document.issuedAt"></td>
                        <td class="td-input" t-text="index">
                            {{-- That all that is needed for the dropdown --}}
                            <div
                                x-data="{
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
                                x-on:keydown.escape.prevent.stop="close($refs.button)"
                                x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                x-id="['dropdown-button']"
                                class="relative"
                            >
                                <!-- Button -->
                                <button
                                    x-ref="button"
                                    x-on:click="toggle()"
                                    :aria-expanded="openDropdown"
                                    :aria-controls="$id('dropdown-button')"
                                    type="button"
                                    class=""
                                >
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"/>
                                    </svg>

                                </button>

                                <!-- Panel -->
                                <div
                                    x-ref="panel"
                                    x-show="openDropdown"
                                    x-transition.origin.top.left
                                    x-on:click.outside="close($refs.button)"
                                    :id="$id('dropdown-button')"
                                    x-cloak
                                    class="absolute left-0 min-w-48 rounded-lg shadow-sm mt-2 z-10 origin-top-left bg-white p-1.5 outline-none border border-gray-200"
                                >
                                    <button @click="
                                                openModal = true; {{-- Open the modal --}}
                                                item = index; {{-- Identify the item we are corrently editing --}}
                                                {{-- Replace the previous document with the current, don't assign object directly (modalDocument = document) to avoid reactiveness --}}
                                                modalDocument = new Doc(document)
                                                newDocument = false; {{-- This document is already created --}}
                                            "
                                            @click.prevent
                                            class="px-2 lg:py-1.5 py-2 w-full flex items-center rounded-md transition-colors text-left text-gray-800 hover:bg-gray-50 focus-visible:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Edit
                                    </button>

                                    <button @click="documents.splice(index, 1); close($refs.button)"
                                            @click.prevent
                                            class="px-2 lg:py-1.5 py-2 w-full flex items-center rounded-md transition-colors text-left text-gray-800 hover:bg-red-50 hover:text-red-600 focus-visible:bg-red-50 focus-visible:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Delete Task
                                    </button>
                                </div>

                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div>

            {{-- Button to trigger the modal --}}
            <button @click="
                        openModal = true; {{-- Open the Modal --}}
                        newDocument = true; {{-- We are adding a new document --}}
                        modalDocument = new Doc() {{-- Replace the data of the previous document with a new one--}}
                    "
                    @click.prevent
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                </svg>

                {{__('forms.addDocument')}}
            </button>

            {{-- Modal --}}
            <template x-teleport="body"> {{-- This moves the modal at the end of the body tag --}}
                <div x-show="openModal"
                     style="display: none"
                     x-on:keydown.escape.prevent.stop="openModal = false"
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
                         x-on:click="openModal = false"
                         class="relative flex min-h-screen items-center justify-center p-4"
                    >
                        <div x-on:click.stop
                             x-trap.noscroll.inert="openModal"
                             class="modal-content h-fit"
                        >
                            {{-- Title --}}
                            <h3 class="modal-header" :id="$id('modal-title')">{{__('forms.addDocument')}}</h3>

                            {{-- Content --}}
                            <form>
                                <div class="form-row-modal">
                                    <div>
                                        <label for="documentType" class="label-modal">{{__('forms.documentType')}}</label>
                                        <input x-model="modalDocument.type" type="text" name="documentType" id="documentType" class="input-modal" required>

                                        <p class="text-error text-xs" x-show="!modalDocument.type.trim().length > 0">{{__('forms.fieldEmpty')}}</p>
                                    </div>
                                    <div>
                                        <label for="documentNumber" class="label-modal">{{__('forms.documentNumber')}}</label>
                                        <input x-model="modalDocument.number" type="text" name="documentNumber" id="documentNumber" class="input-modal" required>
                                        <p class="text-error text-xs" x-show="!modalDocument.number.trim().length > 0">{{__('forms.fieldEmpty')}}</p>
                                    </div>
                                    <div>
                                        <label for="documentIssuedBy" class="label-modal">{{__('forms.documentIssuedBy')}}</label>
                                        <input x-model="modalDocument.issuedBy" type="text" name="documentIssuedBy" id="documentIssuedBy" class="input-modal">
                                    </div>
                                    <div>
                                        <label for="documentIssuedAt" class="label-modal">{{__('forms.documentIssuedAt')}}</label>
                                        <input x-model="modalDocument.issuedAt" type="text" name="documentIssuedAt" id="documentIssuedAt" class="input-modal">
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between space-x-2">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="button-minor"
                                    >
                                        {{__('forms.cancel')}}
                                    </button>

                                    <button @click.prevent
                                            @click="newDocument !== false ? documents.push(modalDocument) : documents[item] = modalDocument; openModal = false"
                                            class="button-primary"
                                            :disabled="!(modalDocument.type.trim().length > 0 && modalDocument.number.trim().length > 0)"
                                    >
                                        {{__('forms.save')}}
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
        issuedAt= '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

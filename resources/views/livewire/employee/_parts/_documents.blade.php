<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              {{-- Binding documents to Alpine, it will be re-used in the modal.
                Note that it's necessary for modal to work properly --}}
              x-data="{
                  documents: $wire.entangle('form.documents'),
                  openModal: false,
                  modalDocument: new Doc(),
                  newDocument: false,
                  item: 0,
                  dictionary: @js($this->dictionaries['DOCUMENT_TYPE'])
              }"
    >
        <legend class="legend">
            <h2>{{__('forms.document')}}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.document_type') }}</th>
                <th scope="col" class="th-input">{{ __('forms.number') }} </th>
                <th scope="col" class="th-input">{{ __('forms.issued_by') }}</th>
                <th scope="col" class="th-input">{{ __('forms.issued_at') }}</th>
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
                    <td class="td-input relative">
                        <!-- Кнопки редагування та видалення -->
                        <x-dropdown-button
                            :editAction="'openModal = true; item = index; modalDocument = new Doc(document); newDocument = false; close($refs.button)'"
                            :deleteAction="'documents.splice(index, 1); close($refs.button)'"                        />
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
                        modalDocument = new Doc(); {{-- Replace the data of the previous document with a new one--}}
                    "
                    @click.prevent
                    class="item-add my-5"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                     viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14m-7 7V5"/>
                </svg>

                {{__('forms.add_document')}}
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
                             class="modal-content h-fit w-full max-w-2xl rounded-2xl shadow-lg bg-white"
                        >

                        {{-- Title --}}
                            <h3 class="modal-header" :id="$id('modal-title')">
                                <span x-text="newDocument ? '{{ __('forms.add_document') }}' : '{{ __('forms.edit') . ' ' . __('forms.document') }}'"></span>
                            </h3>

                            {{-- Content --}}
                            <form>
                                <div class="form-row-modal">
                                    <div>
                                        <label for="documentType"
                                               class="label-modal">{{__('forms.document_type')}}
                                        </label>
                                        <select x-model="modalDocument.type" id="documentType" class="input-modal"
                                                type="text" required>
                                            @foreach($this->dictionaries['DOCUMENT_TYPE'] as $typeValue => $typeDescription)
                                                <option value="{{$typeValue}}">{{$typeDescription}}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-error text-xs"
                                           x-show="!Object.keys(dictionary).includes(modalDocument.type)">{{__('forms.field_empty')}}</p>
                                    </div>

                                    <div>
                                        <label for="documentNumber"
                                               class="label-modal">{{__('forms.document_number')}}</label>
                                        <input x-model="modalDocument.number" type="text" name="documentNumber"
                                               id="documentNumber" class="input-modal" required>
                                        <p class="text-error text-xs"
                                           x-show="!modalDocument.number.trim().length > 0">{{__('forms.field_empty')}}</p>
                                    </div>
                                    <div>
                                        <label for="documentIssuedBy"
                                               class="label-modal">{{__('forms.document_issued_by')}}</label>
                                        <input x-model="modalDocument.issuedBy" type="text" name="documentIssuedBy"
                                               id="documentIssuedBy" class="input-modal">
                                    </div>
                                    <div>
                                        <label for="documentIssuedAt"
                                               class="label-modal">{{__('forms.document_issued_at')}}</label>
                                        <input x-model="modalDocument.issuedAt" name="documentIssuedAt"
                                               id="documentIssuedAt" class="input-modal datepicker-input"
                                               autocomplete="off">
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
        issuedAt = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

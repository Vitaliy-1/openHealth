<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              x-data="{
                  qualifications: $wire.entangle('form.qualifications'),
                  openModal: false,
                  modalQualification: new Qualification(),
                  newQualification: false,
                  item: 0,
                  qualTypeDict: $wire.dictionaries['QUALIFICATION_TYPE']
              }"
    >
        <legend class="legend">
            <h2>{{ __('forms.qualifications') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.document_type') }}</th>
                <th scope="col" class="th-input">{{ __('forms.institutionName') }}</th>
                <th scope="col" class="th-input">{{ __('forms.speciality') }}</th>
                <th scope="col" class="th-input">{{ __('forms.certificateNumber') }}</th>
                <th scope="col" class="th-input">{{ __('forms.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(qualification, index) in qualifications">
                <tr>
                    <td class="td-input" x-text="qualTypeDict[qualification.type] || qualification.type"></td>
                    <td class="td-input" x-text="qualification.institution_name"></td>
                    <td class="td-input" x-text="qualification.speciality"></td>
                    <td class="td-input" x-text="qualification.certificate_number"></td>
                    <td class="td-input">
                        <!-- Кнопки редагування та видалення -->
                        <button @click.prevent="
                                                    openModal = true; {
                                                    item = index;
                                                    modalDocument = new Qualification(qualifications)
                                                    newDocument = false;
                                                "
                                class="dropdown-button"
                        >
                            {{ __('forms.edit') }}
                        </button>

                        <button @click.prevent="qualifications.splice(index, 1); close($refs.button)"
                                class="dropdown-button dropdown-delete">
                            {{ __('forms.delete') }}
                        </button>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>

        <div>
            <button @click="openModal = true; newQualification = true; modalQualification = new Qualification();"
                    @click.prevent
                    class="item-add my-5 text-white"
            >
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                </svg>
                {{__('forms.addQualification')}}
            </button>

            <template x-teleport="body">
                <div x-show="openModal"
                     style="display: none"
                     @keydown.escape.prevent.stop="openModal = false"
                     role="dialog"
                     aria-modal="true"
                     x-id="['modal-title']"
                     :aria-labelledby="$id('modal-title')"
                     class="modal"
                >
                    <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/25"></div>

                    <div x-show="openModal"
                         x-transition
                         @click="openModal = false"
                         class="relative flex min-h-screen items-center justify-center p-4"
                    >
                        <div @click.stop
                             x-trap.noscroll.inert="openModal"
                             class="modal-content h-fit bg-gray-800 text-white"
                        >
                            <h3 class="modal-header" :id="$id('modal-title')">
                                <span x-text="newQualification ? '{{ __('Додати підвищення кваліфікації') }}' : '{{ __('Редагувати підвищення кваліфікації') }}'"></span>
                            </h3>

                            <form>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="qualType" class="block mb-1 text-sm font-medium">{{ __('forms.document_type') }}</label>
                                        <input type="text" id="qualType" x-model="modalQualification.type"
                                               class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                    </div>

                                    <div>
                                        <label for="qualInstitution" class="block mb-1 text-sm font-medium">{{ __('forms.institutionName') }}</label>
                                        <input type="text" id="qualInstitution" x-model="modalQualification.institution_name"
                                               class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                    </div>

                                    <div>
                                        <label for="qualSpeciality" class="block mb-1 text-sm font-medium">{{ __('forms.speciality') }}</label>
                                        <input type="text" id="qualSpeciality" x-model="modalQualification.speciality"
                                               class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                    </div>

                                    <div>
                                        <label for="qualCertificate" class="block mb-1 text-sm font-medium">{{ __('forms.certificateNumber') }}</label>
                                        <input type="text" id="qualCertificate" x-model="modalQualification.certificate_number"
                                               class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end gap-4">
                                    <button type="button"
                                            @click="openModal = false"
                                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                        {{ __('forms.cancel') }}
                                    </button>
                                    <button type="submit"
                                            @click.prevent="newQualification ? qualifications.push(modalQualification) : qualifications[item] = modalQualification; openModal = false"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                            :disabled="!(modalQualification.type && modalQualification.institution_name && modalQualification.speciality)">
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
    class Qualification {
        type = '';
        institution_name = '';
        speciality = '';
        certificate_number = '';

        constructor(obj = null) {
            if (obj) Object.assign(this, obj);
        }
    }
</script>

<div class="overflow-x-auto relative">
    <fieldset class="fieldset"
              x-data="{
                  scienceDegree: $wire.entangle('form.science_degree'),
                  openModal: false,
                  modalScienceDegree: new ScienceDegree(),
                  dictionary: {
                      'BACHELOR': '{{ __('forms.bachelor') }}',
                      'MASTER': '{{ __('forms.master') }}',
                      'PHD': '{{ __('forms.phd') }}',
                      'ASSOCIATE': '{{ __('forms.associate') }}',
                      'SPECIALIST': '{{ __('forms.specialist') }}'
                  }
              }"
    >
        <h5 class="mb-5 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.scienceDegree') }}
        </h5>

        <template x-if="true">
            <table class="table-input w-full">
                <thead class="thead-input">
                <tr>
                    <th class="th-input">{{ __('forms.degree') }}</th>
                    <th class="th-input">{{ __('forms.issuedDate') }}</th>
                    <th class="th-input">{{ __('forms.institutionName') }}</th>
                    <th class="th-input">{{ __('forms.speciality') }}</th>
                    <th class="th-input">{{ __('forms.diplomaNumber') }}</th>
                    <th class="th-input">{{ __('forms.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr x-show="scienceDegree">
                    <td class="td-input" x-text="dictionary[scienceDegree.degree] || scienceDegree.degree"></td>
                    <td class="td-input" x-text="scienceDegree.issued_date"></td>
                    <td class="td-input" x-text="scienceDegree.institution_name"></td>
                    <td class="td-input" x-text="scienceDegree.speciality"></td>
                    <td class="td-input" x-text="scienceDegree.diploma_number"></td>
                    <td class="td-input">
                        <div class="flex space-x-2">
                            <button @click.prevent="openModal = true; modalScienceDegree = new ScienceDegree(scienceDegree)" class="text-blue-600 hover:text-blue-800">✎</button>
                            <button @click.prevent="scienceDegree = null" class="text-red-600 hover:text-red-800">✕</button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>

        <!-- Кнопка додавання -->
        <button @click="
                        openModal = true;
                        newDocument = true;
                        modalDocument = new ScienceDegree()
                    "
                @click.prevent
                class="item-add my-5"
        >
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
            </svg>

            {{__('forms.addScienceDegree')}}
        </button>

        <!-- Modal -->
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
                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/40 z-40"></div>

                <div x-show="openModal"
                     x-transition
                     @click="openModal = false"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div @click.stop
                         x-trap.noscroll.inert="openModal"
                         class="w-full max-w-lg bg-gray-800 text-white rounded-lg shadow-lg p-6"
                    >
                        <h2 class="text-xl font-semibold mb-6" :id="$id('modal-title')">
                            <span x-text="!scienceDegree ? '{{ __('Додати науковий ступінь') }}' : '{{ __('Редагувати науковий ступінь') }}'"></span>
                        </h2>

                        <form>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="scienceDegreeType" class="block mb-1 text-sm font-medium">{{ __('forms.degree') }}</label>
                                    <select x-model="modalScienceDegree.degree" id="scienceDegreeType"
                                            class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                        <option value="" disabled>{{ __('forms.select') }}</option>
                                        <template x-for="(label, val) in dictionary" :key="val">
                                            <option :value="val" x-text="label"></option>
                                        </template>
                                    </select>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.degree">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="scienceIssued" class="block mb-1 text-sm font-medium">{{ __('forms.issuedDate') }}</label>
                                    <input type="date" id="scienceIssued" x-model="modalScienceDegree.issued_date"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="дд.мм.рррр" required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.issued_date">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="scienceInstitution" class="block mb-1 text-sm font-medium">{{ __('forms.institutionName') }}</label>
                                    <input type="text" id="scienceInstitution" x-model="modalScienceDegree.institution_name"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.institution_name">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div>
                                    <label for="scienceSpeciality" class="block mb-1 text-sm font-medium">{{ __('forms.speciality') }}</label>
                                    <input type="text" id="scienceSpeciality" x-model="modalScienceDegree.speciality"
                                           class="input-modal bg-gray-700 text-white border border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-red-500 text-xs mt-1" x-show="!modalScienceDegree.speciality">{{ __('forms.field_empty') }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="scienceDiploma" class="block mb-1 text-sm font-medium">{{ __('forms.diplomaNumber') }}</label>
                                    <input type="text" id="scienceDiploma" x-model="modalScienceDegree.diploma_number"
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
                                        @click.prevent="scienceDegree = modalScienceDegree; openModal = false"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        :disabled="!(modalScienceDegree.degree && modalScienceDegree.institution_name && modalScienceDegree.issued_date && modalScienceDegree.speciality)">
                                    {{ __('forms.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </fieldset>
</div>

<script>
    class ScienceDegree {
        degree = '';
        issued_date = '';
        institution_name = '';
        speciality = '';
        diploma_number = '';

        constructor(obj = null) {
            if (obj) {
                Object.assign(this, obj);
            }
        }
    }
</script>

<?php

namespace App\Livewire\Patient;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person;
use App\Traits\FormTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Random\RandomException;

class PatientForm extends Component
{
    use FormTrait;

    private const string CACHE_PREFIX = 'register_patient_form';

    public string $mode = 'create';
    public PatientFormRequest $patient_request;
    public Person $patient;
    public string $request_id;
    public string $patient_id;
    protected string $patientCacheKey;
    public mixed $key_property;
    public bool $no_tax_id = false;
    public bool $is_incapable = false;
    protected $listeners = ['addressDataFetched'];
    public ?array $dictionaries_field = [
        'DOCUMENT_TYPE',
        'DOCUMENT_RELATIONSHIP_TYPE',
        'GENDER',
        'PHONE_TYPE',
        'PREFERRED_WAY_COMMUNICATION',
        'STREET_TYPE',
    ];

    public function boot(): void
    {
        $this->patientCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, $id = ''): void
    {
        if ($request->has('store_id')) {
            $this->request_id = $request->input('store_id');
        }

        if (!empty($id)) {
            $this->patient_id = $id;
        }

        $this->getPatient();
        $this->getDictionary();
    }

    public function render()
    {
        return view('livewire.patient.patient-form');
    }

    public function create(string $model): void
    {
        $this->mode = 'create';
        $this->patient_request->{$model} = [];
        $this->openModal($model);
        $this->getPatient();
    }

    /**
     * @throws ValidationException
     */
    public function store(string $model): void
    {
        $this->patient_request->rulesForModelValidate($model);

//        $this->fetchDataFromAddressesComponent();
        $this->resetErrorBag();

        if (isset($this->request_id)) {
            $this->storeCachePatient($model);
        }

        $this->closeModalModel();

        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);

        $this->getPatient();
    }

    public function storeCachePatient(string $model): void
    {
        $cacheData = [];

        if ($this->hasCache()) {
            $cacheData = $this->getCache();
        }

        if ($model === 'documents' || $model === 'confidant_person_documents' || $model === 'confidant_person_documents_relationship') {
            $cacheData[$this->request_id][$model][] = $this->patient_request->{$model};
        } else {
            $cacheData[$this->request_id][$model] = $this->patient_request->{$model};
        }

        $this->putCache($cacheData);
    }

    /**
     * @throws ApiException
     */
    public function signedComplete(): void
    {
        $this->sendApiRequest();
    }

    /**
     * @throws ApiException
     * @throws RandomException
     */
    public function sendApiRequest()
    {
        $data = $this->buildPatientRequest();
        $patient = PatientRequestApi::createPatientRequest($data);
        $phoneNumber = $patient['data']['person']['authentication_methods'][0]['phone_number'];

        // додать if чи відповідь 201
        $verificationRequestData = $this->buildVerificationRequest($phoneNumber);
        $initialize = PatientRequestApi::initializeOtpVerification($verificationRequestData);

        $verificationRequestData = $this->buildCompleteVerificationRequest();
        $initialize = PatientRequestApi::completeOtpVerification($phoneNumber, $verificationRequestData);
        // Invalid verification code

        $verifyRequestData = $this->buildVerifyPatientRequest($phoneNumber);
        $initialize = PatientRequestApi::verifyOtpVerification($verifyRequestData);

        $approvePatientRequestData = $this->buildApprovePatientRequest();
        $patientApprove = PatientRequestApi::approvePatientRequest($patient['data']['id'], $approvePatientRequestData);

//        $data2 = $this->build2PatientRequest($patient['meta']['request_id']);
//
//        $patientUpdate = PatientRequestApi::updatePatientRequest($data2);

        $this->getPatient();
    }

    /**
     * Get all data about patient from cache
     * @return void
     */
    public function getPatient(): void
    {
        if (isset($this->request_id) && $this->hasCache()) {
            $patientData = $this->getCache();

            if (isset($patientData[$this->request_id])) {
                $this->patient = (new Person())->forceFill($patientData[$this->request_id]);

                if (!empty($this->patient->patient)) {
                    $this->patient_request->fill(
                        [
                            'patient' => $this->patient->patient,
                            'documents' => $this->patient->documents ?? [],
                            'identity' => $this->patient->identity ?? [],
                            'contact_data' => $this->patient->contact_data ?? [],
                            'emergency_contact' => $this->patient->emergency_contact ?? [],
                            'addresses' => $this->patient->addresses ?? [],
                            'confidant_person' => $this->patient->confidant_person ?? [],
                            'confidant_person_documents' => $this->patient->confidant_person_documents ?? [],
                            'confidant_person_documents_relationship' => $this->patient->confidant_person_documents_relationship ?? [],
                            'legal_representation_contact' => $this->patient->legal_representation_contact ?? [],
                            'authentication_methods' => $this->patient->authentication_methods ?? [],
                        ]
                    );
                }
            }
        }
    }

    private function buildPatientRequest(): array
    {
        $cacheData = $this->getCache();

        $patient = $cacheData[0]['patient'];
        $contact_data = $cacheData[0]['contact_data'];
        $identity = $cacheData[0]['identity'];
        $documents = $cacheData[0]['documents'][0];
        $addresses = $cacheData[0]['addresses'];
        $authentication_methods = $cacheData[0]['authentication_methods'];
        $emergency_contact = $cacheData[0]['emergency_contact'];

//        $confidant_person = $cacheData[0]['confidant_person'] ?? null;
//        $confidant_person_documents = $cacheData[0]['confidant_person_documents'] ?? null;
//        $confidant_person_documents_relationship = $cacheData[0]['confidant_person_documents_relationship'] ?? null;
//        $legal_representation_contact = $cacheData[0]['legal_representation_contact'] ?? null;


        $patientData = [
//            'id' => '13001c60-45a0-4b5a-b425-9505e1de18bd',

            'first_name' => $patient['first_name'],
            'last_name' => $patient['last_name'],
            'second_name' => $patient['second_name'] ?? '',
            'birth_date' => $patient['birth_date'],
            'birth_country' => $patient['birth_country'],
            'birth_settlement' => $patient['birth_settlement'],
            'gender' => $patient['gender'],
            'email' => $contact_data['email'] ?? '',
            'no_tax_id' => $this->no_tax_id,

            'secret' => $contact_data['secret'],

            'documents' => [
                [
                    'type' => $documents['type'],
                    'number' => $documents['number'],
                    'issued_by' => $documents['issued_by'],
                    'issued_at' => $documents['issued_at'],
                    'expiration_date' => $documents['expiration_date'] ?? '',
                ]
            ],

            'addresses' => [
                [
                    // що за тайп? додати в форму?
                    'type' => $addresses['type'] ?? 'RESIDENCE',
                    // також нема в формі контактні дані, спитати за enum country enum, можливо зробити як gender і брати з enum значення?
                    'country' => $addresses['country'] ?? 'UA',
                    'area' => $addresses['area'],
                    // це район області, такого нема в нас у формі, треба? значення не обов'язкове
                    'region' => $addresses['region'] ?? '',
                    'settlement' => $addresses['settlement'],
                    // Dictionary SETTLEMENT_TYPE нема в формі, значення обов'язкове
                    'settlement_type' => $addresses['settlement_type'] ?? 'CITY',
                    // це також нема в формі, і що за uaadresses не зрозуміло
                    'settlement_id' => $addresses['settlement_id'] ?? 'b075f148-7f93-4fc2-b2ec-2d81b19a9b7b',
                    'street_type' => $addresses['street_type'] ?? '',
                    'street' => $addresses['street'] ?? '',
                    'building' => $addresses['building'] ?? '',
                    'apartment' => $addresses['apartment'] ?? '',
                    'zip' => $addresses['zip'] ?? '',
                ]
            ],

            'phones' => [
                [
                    'type' => $contact_data['phones'][0]['type'],
                    'number' => $contact_data['phones'][0]['number'],
                ]
            ],

            'authentication_methods' => [
                [
                    'type' => $authentication_methods['type'],
                    // required for type = OTP
                    'phone_number' => $authentication_methods['phone_number'] ?? '',
                    // required for type = THIRD_PERSON
//                    'value' => $authentication_methods['value'] ?? '',
                    // required it type = THIRD_PERSON, and optional for type = OTP or OFFLINE
//                    'alias' => $authentication_methods['alias'] ?? '',
                ]
            ],

            'unzr' => $documents['unzr'] ?? '',

            'emergency_contact' => (object)
            [
                'first_name' => $emergency_contact['first_name'],
                'last_name' => $emergency_contact['last_name'],
                'second_name' => $emergency_contact['second_name'],

                'phones' => [
                    [
                        'type' => $emergency_contact['phones']['type'],
                        'number' => $emergency_contact['phones']['number'],
                    ]
                ],
            ],
        ];

        if (!$this->no_tax_id) {
            $patientData['tax_id'] = $identity['tax_id'];
        }


        $arr = [
            'person' => (object)$patientData,
            'patient_signed' => false,
            'process_disclosure_data_consent' => true,
        ];

//        dd($arr);

        return $arr;
    }


    private function buildVerificationRequest($phoneNumber): array
    {
        return ['phone_number' => $phoneNumber];
    }

    /**
     * @throws RandomException
     */
    private function buildCompleteVerificationRequest(): array
    {
        return ['code' => random_int(1000, 9999)];
    }

    private function buildVerifyPatientRequest($phoneNumber): array
    {
        return [
            'factor' => $phoneNumber,
            'type' => 'SMS',
        ];
    }

    private function buildApprovePatientRequest(): array
    {
        return [
            'verification_code' => '',
        ];
    }

    private function build2PatientRequest($id): array
    {
        $cacheData = $this->getCache();

        $patient = $cacheData[0]['patient'];
        $contact_data = $cacheData[0]['contact_data'];
        $identity = $cacheData[0]['identity'];
        $documents = $cacheData[0]['documents'][0];
        $addresses = $cacheData[0]['addresses'];
        $authentication_methods = $cacheData[0]['authentication_methods'];
        $emergency_contact = $cacheData[0]['emergency_contact'];


        $confidant_person = $cacheData[0]['confidant_person'] ?? null;
        $confidant_person_documents = $cacheData[0]['confidant_person_documents'] ?? null;
        $confidant_person_documents_relationship = $cacheData[0]['confidant_person_documents_relationship'] ?? null;
        $legal_representation_contact = $cacheData[0]['legal_representation_contact'] ?? null;

        $patientData = [
            'id' => $id,

            'first_name' => $patient['first_name'],
            'last_name' => $patient['last_name'],
            'second_name' => $patient['second_name'] ?? '',
            'birth_date' => $patient['birth_date'],
            'birth_country' => $patient['birth_country'],
            'birth_settlement' => $patient['birth_settlement'],
            'gender' => $patient['gender'],
            'email' => $contact_data['email'] ?? '',
            'no_tax_id' => $this->no_tax_id,

            'secret' => $contact_data['secret'],

            'documents' => [
                [
                    'type' => $documents['type'],
                    'number' => $documents['number'],
                    'issued_by' => $documents['issued_by'],
                    'issued_at' => $documents['issued_at'],
                    'expiration_date' => $documents['expiration_date'] ?? '',
                ]
            ],

            'addresses' => [
                [
                    // що за тайп? додати в форму?
                    'type' => $addresses['type'] ?? 'RESIDENCE',
                    // також нема в формі контактні дані, спитати за enum country enum, можливо зробити як gender і брати з enum значення?
                    'country' => $addresses['country'] ?? 'UA',
                    'area' => $addresses['area'],
                    // це район області, такого нема в нас у формі, треба? значення не обов'язкове
                    'region' => $addresses['region'] ?? '',
                    'settlement' => $addresses['settlement'],
                    // Dictionary SETTLEMENT_TYPE нема в формі, значення обов'язкове
                    'settlement_type' => $addresses['settlement_type'] ?? 'CITY',
                    // це також нема в формі, і що за uaadresses не зрозуміло
                    'settlement_id' => $addresses['settlement_id'] ?? 'b075f148-7f93-4fc2-b2ec-2d81b19a9b7b',
                    'street_type' => $addresses['street_type'] ?? '',
                    'street' => $addresses['street'] ?? '',
                    'building' => $addresses['building'] ?? '',
                    'apartment' => $addresses['apartment'] ?? '',
                    'zip' => $addresses['zip'] ?? '',
                ]
            ],

            'phones' => [
                [
                    'type' => $contact_data['phones'][0]['type'],
                    'number' => $contact_data['phones'][0]['number'],
                ]
            ],

            'authentication_methods' => [
                [
                    'type' => $authentication_methods['type'],
                    // required for type = OTP
                    'phone_number' => $authentication_methods['phone_number'] ?? '',
                    // required for type = THIRD_PERSON
//                    'value' => $authentication_methods['value'] ?? '',
                    // required it type = THIRD_PERSON, and optional for type = OTP or OFFLINE
//                    'alias' => $authentication_methods['alias'] ?? '',
                ]
            ],

            'unzr' => $documents['unzr'] ?? '',

            'emergency_contact' => (object)
            [
                'first_name' => $emergency_contact['first_name'],
                'last_name' => $emergency_contact['last_name'],
                'second_name' => $emergency_contact['second_name'],

                'phones' => [
                    [
                        'type' => $emergency_contact['phones']['type'],
                        'number' => $emergency_contact['phones']['number'],
                    ]
                ],
            ],
        ];

        if (!$this->no_tax_id) {
            $patientData['tax_id'] = $identity['tax_id'];
        }


        // Add if this Person is disabled, underage, etc.
        $patientData['confidant_person'] = (object)
        [
            'person_id' => $id,

            'documents_relationship' => [
                [
                    'type' => $confidant_person_documents_relationship[0]['type'],
                    'number' => $confidant_person_documents_relationship[0]['number'],
//                        'issued_by' => $confidant_person_documents_relationship[0]['issued_by'] ?? '',
//                        'issued_at' => $confidant_person_documents_relationship[0]['issued_at'] ?? '',
                    // ?? schema does not allow additional properties, але в general MIS API є....
//                        'active_to' => $confidant_person_documents_relationship[0]['expiration_date'] ?? '',
                ]
            ],
        ];

        $arr = [
            'person' => (object)$patientData,
            'patient_signed' => false,
            'process_disclosure_data_consent' => true,
        ];

//        dd($arr);

        return $arr;
    }

    public function edit(string $model, $key_property = ''): void
    {
        $this->key_property = $key_property;
        $this->mode = 'edit';
        $this->openModal($model);

        if (isset($this->request_id)) {
            $this->editCachePatient($model, $key_property);
        }
    }

    private function editCachePatient(string $model, $key_property = ''): void
    {
        $cacheData = $this->getCache();

        if (empty($key_property) && $key_property !== 0) {
            $this->patient_request->{$model} = $cacheData[$this->request_id][$model];
        } else {
            $this->patient_request->{$model} = $cacheData[$this->request_id][$model][$key_property];
        }
    }

    public function fetchDataFromAddressesComponent(): void
    {
        $this->dispatch('fetchAddressData');
    }

    public function addressDataFetched($addressData): void
    {
        $this->patient_request->addresses = $addressData;
    }

    /**
     * @throws ValidationException
     */
    public function update(string $model, int $key_property): void
    {
        $this->patient_request->rulesForModelValidate($model);
        $this->resetErrorBag();

        if (isset($this->request_id)) {
            $this->updateCachePatient($model, $key_property);
        }

        $this->closeModalModel($model);
    }

    public function updateCachePatient(string $model, int $key_property): void
    {
        if ($this->hasCache()) {
            $cacheData = $this->getCache();
            $cacheData[$this->request_id][$model][$key_property] = $this->patient_request->{$model};

            $this->putCache($cacheData);
        }
    }

    public function closeModalModel($model = null): void
    {
        if (!empty($model)) {
            $this->patient_request->{$model} = [];
        }

        $this->closeModal();
        $this->getPatient();
    }

    private function hasCache(): bool
    {
        return Cache::has($this->patientCacheKey);
    }

    private function getCache(): mixed
    {
        return Cache::get($this->patientCacheKey, []);
    }

    private function putCache($cacheData): void
    {
        Cache::put($this->patientCacheKey, $cacheData, now()->days(90));
    }
}

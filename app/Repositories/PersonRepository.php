<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PersonRepository
{
    public function __construct(
        protected AddressRepository $addressRepository,
        protected PhoneRepository $phoneRepository,
        protected DocumentRepository $documentRepository,
        protected AuthenticationMethodRepository $authenticationMethodRepository,
        protected ConfidantPersonRepository $confidantPersonRepository
    ) {
    }

    /**
     * Save person request response to DB.
     *
     * @param  array  $response  Response from API
     * @param  string  $modelClass
     * @param  string|null  $personUuid
     * @return bool
     * @throws Throwable
     */
    public function savePersonResponseData(array $response, string $modelClass, ?string $personUuid = null): bool
    {
        DB::beginTransaction();

        try {
            $personRequest = $this->createOrUpdate($response, $modelClass, $personUuid);

            $documents = $response['person']['documents'] ?? $response['documents'] ?? null;
            if ($documents) {
                $this->documentRepository->addDocuments($personRequest, $documents);
            }

            $addresses = $response['person']['addresses'] ?? [$response['addresses']] ?? null;
            if ($addresses) {
                $this->addressRepository->addAddresses($personRequest, $addresses);
            }

            $phones = $response['person']['phones'] ?? $response['patient']['phones'] ?? null;
            if ($phones) {
                $this->phoneRepository->addPhones($personRequest, $phones);
            }

            $authenticationMethods = $response['person']['authentication_methods'] ?? $response['patient']['authentication_methods'] ?? null;
            if ($authenticationMethods) {
                $this->authenticationMethodRepository->addAuthenticationMethod($personRequest, $authenticationMethods);
            }

            if (isset($response['confidant_person'])) {
                $confidantData = [
                    'documents_relationship' => $response['documents_relationship'],
                    'confidantPersonInfo' => $response['confidant_person'][0]
                ];

                $this->confidantPersonRepository->addConfidantPerson(
                    $personRequest,
                    $confidantData
                );
            }

            if (isset($response['person']['confidant_person'])) {
                $this->confidantPersonRepository->addConfidantPerson(
                    $personRequest,
                    $response['person']['confidant_person']
                );
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('db_errors')->error('Error saving person request data', [
                'error' => $e->getMessage(),
                'response' => $response
            ]);

            return false;
        }
    }

    /**
     * Create or update data in DB.
     *
     * @param  array  $data
     * @param  string  $modelClass
     * @param  string|null  $personUuid
     * @return PersonRequest|Person
     */
    protected function createOrUpdate(array $data, string $modelClass, ?string $personUuid = null): PersonRequest|Person
    {
        if (isset($data['patient'])) {
            $data['person'] = $data['patient'];
        }

        $personData = [
            'uuid' => $personUuid ?? $data['id'] ?? null,
            'first_name' => $data['person']['first_name'],
            'last_name' => $data['person']['last_name'],
            'second_name' => $data['person']['second_name'] ?? null,
            'birth_date' => Carbon::parse($data['person']['birth_date'])->format('Y-m-d'),
            'birth_country' => $data['person']['birth_country'],
            'birth_settlement' => $data['person']['birth_settlement'],
            'gender' => $data['person']['gender'],
            'email' => $data['person']['email'] ?? null,
            'no_tax_id' => $data['person']['no_tax_id'],
            'tax_id' => $data['person']['tax_id'] ?? null,
            'secret' => $data['person']['secret'],
            'unzr' => $data['person']['unzr'] ?? null,
            'emergency_contact' => $data['person']['emergency_contact'],
            'patient_signed' => $data['patient_signed'] ?? false,
            'process_disclosure_data_consent' => $data['process_disclosure_data_consent'] ?? true
        ];

        if ($modelClass === PersonRequest::class) {
            $personData['status'] = $data['status'] ?? 'APPLICATION';
        }

        // Update or create data based on id or uuid
        return $modelClass::updateOrCreate(
            [
                'uuid' => $personData['uuid'] ?? null,
                'id' => isset($data['dbId']) && !$personData['uuid'] ? $data['dbId'] : null
            ],
            $personData
        );
    }

    /**
     * Update person request status by provided UUID.
     *
     * @param  array  $response
     * @return bool
     */
    public function updatePersonRequestStatusByUuid(array $response): bool
    {
        try {
            PersonRequest::where('uuid', $response['id'])->update([
                'status' => $response['status']
            ]);

            return true;
        } catch (Exception $e) {
            Log::channel('db_errors')->error('Error updating person request status', [
                'error' => $e->getMessage(),
                'response' => $response
            ]);

            return false;
        }
    }

    /**
     * Establish a connection between PersonRequest and Person.
     *
     * @param  array  $response
     * @return bool
     * @throws Exception
     */
    public function createRelation(array $response): bool
    {
        try {
            $personRequest = PersonRequest::where('uuid', $response['id'])->firstOrFail();
            $person = Person::where('uuid', $response['person_id'])->firstOrFail();

            $personRequest->person()->associate($person);
            $personRequest->save();

            return true;
        } catch (Exception $e) {
            Log::channel('db_errors')->error('Error establishing relation between PersonRequest and Person', [
                'error' => $e->getMessage(),
                'response' => $response
            ]);

            return false;
        }
    }
}

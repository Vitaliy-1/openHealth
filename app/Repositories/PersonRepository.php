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

            $this->documentRepository->addDocuments($personRequest, $response['person']['documents']);
            $this->addressRepository->addAddresses($personRequest, $response['person']['addresses']);
            $this->phoneRepository->addPhones($personRequest, $response['person']['phones'] ?? []);
            $this->authenticationMethodRepository->addAuthenticationMethod(
                $personRequest,
                $response['person']['authentication_methods']
            );
            $this->confidantPersonRepository->addConfidantPerson(
                $personRequest,
                $response['person']['confidant_person'] ?? []
            );

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
        $personData = [
            'uuid' => $personUuid ?? $data['id'],
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
            'patient_signed' => $data['patient_signed'],
            'process_disclosure_data_consent' => $data['process_disclosure_data_consent']
        ];

        if ($modelClass === PersonRequest::class) {
            $personData['status'] = $data['status'];
        }

        return $modelClass::updateOrCreate(
            [
                'uuid' => $personData['uuid']
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

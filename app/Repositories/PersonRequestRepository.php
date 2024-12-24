<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Person;
use App\Models\PersonRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PersonRequestRepository
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
     * @return bool
     * @throws Throwable
     */
    public function savePersonResponseData(array $response): bool
    {
        DB::beginTransaction();

        try {
            $personRequest = $this->createOrUpdate($response);

            $this->documentRepository->addDocuments($personRequest, $response['person']['documents']);
            $this->addressRepository->addAddresses($personRequest, $response['person']['addresses']);
            $this->phoneRepository->addPhones($personRequest, $response['person']['phones']);
            $this->authenticationMethodRepository->addAuthenticationMethod(
                $personRequest,
                $response['person']['authentication_methods']
            );
            $this->confidantPersonRepository->addConfidantPerson(
                $personRequest,
                $response['person']['confidant_person']
            );

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error saving person request data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'response' => $response
            ]);

            return false;
        }
    }

    /**
     * Create or update data in DB.
     *
     * @param  array  $data
     * @return PersonRequest
     */
    protected function createOrUpdate(array $data): PersonRequest
    {
        $personRequestData = [
            'uuid' => $data['id'],
            'status' => $data['status'],
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

        return PersonRequest::updateOrCreate(
            [
                'uuid' => $personRequestData['uuid']
            ],
            $personRequestData
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
            Log::error('Error updating person request status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'response' => $response,
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
            Log::error('Error establishing relation between PersonRequest and Person', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'response' => $response,
            ]);

            return false;
        }
    }
}

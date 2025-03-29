<?php

namespace App\Livewire\LegalEntity;

use App\Mail\OwnerCredentialsMail;
use App\Models\LegalEntity as LegalEntityModel;
use App\Models\License;
use App\Models\Relations\Phone;
use App\Models\User;
use App\Models\Employee\Employee;
use App\Models\Relations\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Repositories\PhoneRepository;
use App\Repositories\AddressRepository;
use Illuminate\Validation\ValidationException;

class CreateLegalEntity extends LegalEntity
{
    protected const string STEP_PATH='views/livewire/legal-entity/step';

    /**
     * @var string
     */
    protected const string CACHE_PREFIX = 'register_legal_entity_form';

    /**
     * @var int The current step of the process
     */
    protected array $steps = [
        'index' => 1,
        'accreditationShow' => false,
        'archivationShow' => false
    ];

    /**
     * @var string The Cache ID to store Legal Entity being filled by the current user
     */
    protected string $entityCacheKey;

    /**
     * @var string The Cache ID to store Owner being filled by the current user
     */
    protected string $ownerCacheKey;

    /**
     * @var string The Cache ID to store Owner being filled by the current user
     */
    protected string $stepCacheKey;

    /**
     * @return void set cache keys
     */
    public function boot(
        AddressRepository $addressRepository,
        PhoneRepository $phoneRepository
    ): void {
        parent::boot($addressRepository, $phoneRepository);

        $this->entityCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . LegalEntityModel::class;
        $this->ownerCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . Employee::class;
        $this->stepCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . 'steps';

        $this->getLegalEntity();

        $this->getCurrentStepFromCache();
    }

    public function mount(): void
    {
        parent::mount();

        $this->getOwnerFields();

        $this->legalEntityForm->owner['phones'] = $this->legalEntityForm->owner['phones'] ?? [];

        $this->legalEntityForm->owner['noTaxId'] = $this->legalEntityForm->owner['noTaxId'] ?? false;

        $this->setOwnerFromCache();
    }

    /**
     * Set the owner information from the cache if available.
     */
    private function setOwnerFromCache(): void
    {
        // Check if the owner information is available in the cache and the user is not a legal entity
        if (Cache::has($this->ownerCacheKey) && !Auth::user()->legalEntity) {
            $this->legalEntityForm->owner = Cache::get($this->ownerCacheKey); // Set the owner information from cache
        }
    }

    /**
     * Set the currentStep information from the cache if available.
     */
    private function getCurrentStepFromCache(): void
    {
        // Check if the information about step state is available in the cache
        if (Cache::has($this->stepCacheKey)) {
            $this->steps = Cache::get($this->stepCacheKey); // Get the current steps information from cache

            $this->legalEntityForm->accreditationShow = $this->steps['accreditationShow'];
            $this->legalEntityForm->archivationShow = $this->steps['archivationShow'];
        }
    }

    /**
     * Set the currentStep information from the cache if available.
     */
    private function putCurrentStepToCache(): void
    {
        $this->steps['accreditationShow'] = $this->legalEntityForm->accreditationShow;
        $this->steps['archivationShow'] = $this->legalEntityForm->archivationShow;

        // Check if the information about step state is available in the cache or step data has been changed
        Cache::put($this->stepCacheKey, $this->steps, now()->days(90));
    }

    /**
     * Increases the current step of the process.
     * Resets the error bag, validates the data, increments the current step, puts the legal entity in cache,
     * and ensures the current step does not exceed the total steps.
     * This will automatically switches to the step's form on the web page.
     *
     * @throws ValidationException
     */
    public function nextStep($activeStep): bool
    {
        $this->resetErrorBag();

        $this->validateData($activeStep);

        $this->putLegalEntityInCache();

        if ($activeStep === $this->steps['index']) {
            $this->increaseStep();
        }

        return true;
    }

    /**
     * Increase step number and save it to the $this->currentStep array
     * Also appropriate step's key will saved also
     *
     * @return void
     */
    protected function increaseStep(): void
    {
        $this->steps['index']++;

        $this->putCurrentStepToCache();
    }

    /**
     * @throws ValidationException
     */
    public function validateData($activeStep = null): void
    {
        $stepNumber = $activeStep ?? $this->steps['index'];

        match ($stepNumber) {
            1 => $this->stepEdrpou(),
            2 => $this->stepOwner(),
            3 => $this->stepContact(),
            4 => $this->stepAddress(),
            5 => $this->stepAccreditation(),
            6 => $this->stepLicense(),
            7 => $this->stepAdditionalInformation(),
            8 => $this->stepSignificancy(),
            default => null,
        };
    }

    // TODO: implement in the future release when EDRPOU will validate from outside also
    public function saveLegalEntityFromExistingData($data): void
    {
        $normalizedData = [];

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'id':
                        $normalizedData['uuid'] = $value;
                        break;
                    case 'residence_address':
                        $normalizedData['residence_address'] = $value;
                        break;
                    case 'edr':
                        foreach ($data['edr'] as $edrKey => $edrValue) {
                            $normalizedData[$edrKey] = $edrValue;
                        }
                        break;
                    default:
                        $normalizedData[$key] = $value;
                        break;
                }
            }

            $this->legalEntity->fill($normalizedData);

            $this->legalEntityForm->fill($normalizedData);

            if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
                Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
            }
        }
    }

    /**
     * Update the legal entity in the cache if changes are detected or it doesn't exist already.
     */
    public function putLegalEntityInCache(): void
    {
        // Convert all camelCase keys to snake_case because the Legal Entity model uses snake_case
        $formData = $this->convertArrayKeysToSnakeCase($this->legalEntityForm->toArray());

        // Fill the legal entity model with data from the form
        $this->legalEntity->fill($formData);

        // Create the new Address (rely on $this->address)
        $address = new Address();
        $address->fill($this->address);

        // Associate the legal entity with the address
        $this->legalEntity->setRelation('address', $address);

        // Create the new License model bsed on the form data
        $license = new License();
        $license->fill($formData['license']);

        // Attach the newly created License model to the current LegalEntity model
        $this->legalEntity->setRelation('licenses', $license);

        // Associate the legal entity with the phones
        if (!empty($formData['phones'])) {
            $phoneCollection = collect($formData['phones'])->map(function($phone) {
                $instance = new Phone();
                $instance->fill($phone);

                return $instance;
            });

            $this->legalEntity->setRelation('phones', $phoneCollection);
        }

        // Check if the entity is not in the cache or if changes are detected
        if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
            // Put the legal entity in the cache with a 90-day expiration
            Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
        }
    }

    /**
     * Check if there are changes in the Legal Entity attributes by comparing with cached data.
     *
     * @return bool Returns true if Legal Entity attributes have changed, false otherwise.
     */
    public function checkChanges(): bool
    {
        // Check if entity cache exists
        if (Cache::has($this->entityCacheKey)) {
            $cachedLegalEntity = Cache::get($this->entityCacheKey);

            $legalEntity = $this->flattenArray($this->getAllAttributes($this->legalEntity));

            $cachedLegalEntity = $this->flattenArray($this->getAllAttributes(Cache::get($this->entityCacheKey)));

            // If the Legal Entity has not changed, return false
            if (!empty(array_diff_assoc($legalEntity,$cachedLegalEntity)) ||
                !empty(array_diff_assoc($cachedLegalEntity, $legalEntity))
            ) {
                return true; // Legal Entity has changed
            }

            return false; // Legal Entity has not changed
        }

        return true; // Legal Entity has changed
    }

    /**
     * Check if the Legal Entity owner has changed.
     *
     * @return bool Returns true if the Legal Entity owner has changed, false otherwise.
     */
    public function checkOwnerChanges(): bool
    {
        // Check if the owner information is cached
        if (Cache::has($this->ownerCacheKey)) {
            $cachedOwner = Cache::get($this->ownerCacheKey);

            $legalEntityOwner = $this->legalEntityForm->owner;

            // Compare the cached owner with the current owner
            if (serialize($cachedOwner) === serialize($legalEntityOwner)) {
                return false; // No change in Legal Entity owner
            }
        }

        return true; // Return true if the Legal Entity owner has changed
    }

    /* - STEPS - */

    // Step #1 set EDRPOU number
    public function stepEdrpou(): void
    {
        $this->legalEntityForm->rulesForEdrpou();

        //TODO: Метод для перевірки ЕДРПОУ getLegalEntity
        $getLegalEntity = [];

        if (!empty($getLegalEntity)) {
            $this->saveLegalEntityFromExistingData($getLegalEntity);
        }
    }

    // Step #2 Create Owner
    public function stepOwner(): void
    {
        $this->legalEntityForm->rulesForOwner();

        // Check if the owner information is available in the cache
        $personData = $this->legalEntityForm->owner;

        // Store the owner information in the cache
        if ($this->checkOwnerChanges()) {
            Cache::put($this->ownerCacheKey, $personData, now()->days(90));
        }
    }

    // Step #3 Create/Update Contact[Phones, Email,beneficiary,receiver_funds_code]
    public function stepContact(): void
    {
        $this->legalEntityForm->rulesForContact();
    }

    // Step #4 Create/Update Address
    public function stepAddress(): void
    {
        $this->legalEntityForm->rulesForAddresses();
    }

    // Step #5 Create/Update Accreditation
    public function stepAccreditation(): void
    {
        if ($this->legalEntityForm->accreditationShow) {
            $this->legalEntityForm->rulesForAccreditation();
        }

        $this->putCurrentStepToCache(); // Only for save $this->legalEntityForm->accreditationShow state
    }

    // Step #6 Create/Update License
    public function stepLicense(): void
    {
        $this->legalEntityForm->license['type'] = 'MSP';

        $this->legalEntityForm->rulesForLicense();
    }

    // Step #7 Create/Update Additional Information
    public function stepAdditionalInformation(): void
    {
        if($this->legalEntityForm->archivationShow) {
            $this->legalEntityForm->rulesForAdditionalInformation();
        }

        $this->putCurrentStepToCache(); // Only for save $this->legalEntityForm->archivationShow state
    }

    // Step #8 KEP Significancy (called on creating new Legal Entity only)
    public function stepSignificancy(): void
    {
        $this->legalEntityForm->rulesForSignificancy();
    }

    public function createUser(): ?User
    {
        // Get the currently authenticated user
        $authenticatedUser = Auth::user();

        // Retrieve the email address of the legal entity owner from the form or set it to null
        $email = $this->legalEntityForm->owner['email'] ?? null;

        // Generate a random password
        $password = Str::random(10);

        // Check if a user with the provided email already exists
        $user = User::where('email', $email)->first();

        // If the authenticated user is the owner, use them as the user
        if (isset($authenticatedUser->email) && strtolower($authenticatedUser->email) === $email) {
            // If the authenticated user is the owner, use them as the user
            $user = $authenticatedUser;
        } elseif (!$user) {
            // If no user exists with that email, create a new user
            $user = User::create([
                'email'    => $email,
                'password' => Hash::make($password),
            ]);
        }

        // Associate the legal entity with the user
        $user->legalEntity()->associate($this->legalEntity);

        try{
            $user->save();
        } catch (\Exception $e) {
            $this->dispatchErrorMessage(__('Сталася помилка під час обробки запиту'), ['error' => $e->getMessage()]);

            return null;
        }

        // Assign the 'OWNER' role to the user
        $user->assignRole('OWNER');

        // Send an email with the owner credentials to the user
        Mail::to($user->email)->send(new OwnerCredentialsMail($user->email));

        return $user;
    }

    /**
     * Create a new license with the provided data.
     *
     * @param array $data The data to fill the license with.
     */
    public function createLicense(array $data): void
    {
        $license = License::firstOrNew(['uuid' => $data['id']]);
        $license->fill($data);
        $license->uuid = $data['id'];
        $license->is_primary = true;

        if (isset($this->legalEntity)) {
            $this->legalEntity->licenses()->save($license);
        }
    }

    public function createLegalEntity(): void
    {
        $this->stepSignificancy();

        $this->signLegalEntity();
    }

    public function render()
    {
        return view('livewire.legal-entity.create-legal-entity', [
            'activeStep' => $this->steps['index'],
            'currentStep' => $this->steps['index'],
            'isEdit' => false
        ]);
    }
}

<?php

namespace App\Services;

use App\Classes\Cipher\Api\CipherApi;
use App\Classes\Cipher\Exceptions\ApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SignatureService
{
    protected CipherApi $cipherApi;

    public function __construct(CipherApi $cipherApi)
    {
        $this->cipherApi = $cipherApi;
    }

    /**
     * Sends data for signing using Cipher API.
     *
     * @param array $dataToSign The data payload to be signed.
     * @param string $password Password for the key container.
     * @param string $knedp KNEPD (Certificate Authority ID).
     * @param TemporaryUploadedFile $keyContainerFile The uploaded key container file.
     * @param string $initiatorType Type of signatory (e.g., CipherApi::SIGNATORY_INITIATOR_PERSON).
     * @param string $taxId Tax ID (ІПН/ЄДРПОУ) for verification.
     * @param string $verificationType Verification type (e.g., CipherApi::VERIFICATION_TYPE_PERSON).
     * @return array|string The signed data from Cipher API, or an array of errors.
     */
    public function signData(
        array $dataToSign,
        string $password,
        string $knedp,
        TemporaryUploadedFile $keyContainerFile,
        string $initiatorType,
        string $taxId,
        string $verificationType
    ): array|string {
        try {
            $base64KeyContainer = $this->convertFileToBase64($keyContainerFile);

            return $this->cipherApi->sendSession(
                json_encode($dataToSign, JSON_THROW_ON_ERROR),
                $password,
                $knedp,
                $base64KeyContainer,
                $initiatorType,
                $taxId,
                $verificationType
            );
        } catch (ApiException $e) {
            Log::error("Cipher API signing error: " . $e->getMessage(), ['errors' => $e->getErrors()]);
            return $e->getErrors();
        } catch (\Exception $e) {
            Log::error("General signing error: " . $e->getMessage(), ['exception' => $e]);
            return ['errors' => ['general' => $e->getMessage()]];
        }
    }

    /**
     * Converts a Livewire TemporaryUploadedFile to a Base64 string.
     *
     * @param TemporaryUploadedFile $file
     * @return string|null
     */
    protected function convertFileToBase64(TemporaryUploadedFile $file): ?string
    {
        if ($file && $file->exists()) {
            $fileContents = file_get_contents($file->getRealPath());
            if ($fileContents !== false) {
                return base64_encode($fileContents);
            }
        }
        return null;
    }

    /**
     * Retrieves supported certificate authorities from Cipher API, cached.
     *
     * @return array
     */
    public function getCertificateAuthorities(): array
    {
        return Cache::remember('knedp_certificate_authority', now()->addDays(7), function () {
            try {
                return $this->cipherApi->getCertificateAuthorityApi();
            } catch (ApiException $e) {
                Log::error("Error fetching certificate authorities from Cipher API: " . $e->getMessage(), ['errors' => $e->getErrors()]);
                return [];
            } catch (\Exception $e) {
                Log::error("General error fetching certificate authorities: " . $e->getMessage(), ['exception' => $e]);
                return [];
            }
        });
    }
}

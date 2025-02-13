<?php

namespace App\Classes\Cipher\Traits;

use App\Classes\Cipher\Api\CipherApi;
use App\Classes\Cipher\Exceptions\ApiException;
use App\Classes\Cipher\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

trait Cipher
{
    /**
     * КНЕДП.
     * @var string|null
     */
    public ?string $knedp;

    public $keyContainerUpload;

    public string $password;

    public mixed $getCertificateAuthority;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'knedp' => 'required|string',
            'keyContainerUpload' => 'required|file',
            'password' => 'required|string|max:255'
        ];
    }

    /**
     * Send Encrypted Data.
     *
     * @param  array  $data
     * @param  string  $taxId
     * @param  string  $signatoryInitiator
     * @return string|array
     */
    protected function sendEncryptedData(
        array $data,
        string $taxId,
        string $signatoryInitiator = CipherApi::SIGNATORY_INITIATOR_PERSON
    ): string|array {
        $this->validate($this->rules());

        return (new CipherApi())->sendSession(
            json_encode($data),
            $this->password,
            $this->convertFileToBase64(),
            $this->knedp,
            $signatoryInitiator,
            $taxId
        );
    }

    /**
     * Convert KEP to Base64.
     *
     * @return string|null
     */
    public function convertFileToBase64(): ?string
    {
        if ($this->keyContainerUpload && $this->keyContainerUpload->exists()) {
            $fileExtension = $this->keyContainerUpload->getClientOriginalExtension();
            $filePath = $this->keyContainerUpload->storeAs('uploads/kep', 'kep.' . $fileExtension, 'public');

            if ($filePath) {
                $fileContents = file_get_contents(storage_path('app/public/' . $filePath));
                if ($fileContents !== false) {
                    $base64Content = base64_encode($fileContents);
                    Storage::disk('public')->delete($filePath);

                    return $base64Content;
                }
            }
        }

        return null;
    }

    /**
     * Get Certificate Authority
     *
     * @return array
     * @throws ApiException
     */
    public function getCertificateAuthority(): array
    {
        if (!Cache::has('knedp_certificate_authority')) {
            $data = (new Request('get', '/certificateAuthority/supported', ''))->sendRequest();

            if ($data === false) {
                throw new \RuntimeException('Failed to fetch data from the API.');
            }

            $this->getCertificateAuthority = Cache::put('knedp_certificate_authority', $data['ca'], now()->addDays(7));
        }

        return $this->getCertificateAuthority = Cache::get('knedp_certificate_authority');
    }
}

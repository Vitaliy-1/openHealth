<?php

namespace App\Traits;

use App\Classes\Cipher\Api\CipherApi;
use App\Classes\Cipher\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

trait Cipher
{

    public ?string $knedp;

    public  $keyContainerUpload;

    public  string $password;



    protected function sendEncryptedData(array $data): string|array
    {
        return (new CipherApi())->sendSession(
            json_encode($data),
            $this->password,
            $this->convertFileToBase64(),
            $this->knedp,
        );
    }

    public function convertFileToBase64(): ?string
    {
        if ($this->keyContainerUpload && $this->keyContainerUpload->exists()) {
            $fileExtension = $this->keyContainerUpload->getClientOriginalExtension();
            $filePath = $this->keyContainerUpload->storeAs('uploads/kep', 'kep.'.$fileExtension, 'public');
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


    public function getCertificateAuthority(): array
    {
        if (!Cache::has('knedp_certificate_authority')) {
            $data = (new Request('get', '/certificateAuthority/supported', ''))->sendRequest();
            if ($data === false) {
                throw new \RuntimeException('Failed to fetch data from the API.');
            }
            Cache::put('knedp_certificate_authority', $data['ca'], now()->addDays(7));
        }
        return Cache::get('knedp_certificate_authority');
    }

}

<?php

namespace App\Traits;

use App\Classes\Cipher\Api\CipherApi;

trait Cipher
{

    public ?string $knedp;

    public ?string $keyContainerUpload = null;

    public  string $password;

    protected function sendEncryptedData(array $data): array
    {
        return (new CipherApi())->sendSession(
            json_encode($data),
            $this->password,
            $this->keyContainerUpload,
            $this->knedp
        );
    }

}

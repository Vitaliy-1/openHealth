<?php

namespace App\Classes\Cipher\Api;

use App\Classes\Cipher\Request;
use App\Classes\Cipher\Exceptions\ApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CipherApi
{
    private string $ticketUuid = '';
    private string $base64File = '';
    private string $password = '';
    private string $dataSignature;
    private string $knedp;


    /**
     * Send request to create session and subsequently upload KEYP.
     *
     * @param string $dataSignature Base64 encoded signed data.
     * @param string $password Password for KEYP creation.
     * @param string $base64File KEYP file in base64 format.
     * @param string $knedp Certificate Authority Identifier (KNEPD).
     * @return string Returns KEYP in base64 format.
     * @throws array ApiException
     */
    public function sendSession(string $dataSignature, string $password, string $base64File, string $knedp): array|string
    {
        $this->dataSignature = base64_encode($dataSignature);
        $this->password = $password;
        $this->base64File = $base64File;
        $this->knedp = $knedp;

        try {
            $this->createSession();
            $this->loadData();
            $this->setSessionParameters();
            $this->uploadFile();
            $this->createKeyp();
            $this->getKeypCreator();
            return $this->getKeyp();
        } catch (ApiException $e) {
            return $e->getErrors();
        } finally {
            $this->deleteSession();
        }
    }

    private function createSession(): void
    {
        $this->ticketUuid = $this->sendRequest('post', '/ticket')['ticketUuid'] ?? '';
    }

    private function loadData(): void
    {
        $this->sendRequest('post', "/ticket/{$this->ticketUuid}/data", ['base64Data' => $this->dataSignature]);
    }

    private function setSessionParameters(): void
    {
        $params = [
            "caId" => $this->knedp,
            "cadesType" => "CADES_X_LONG",
            "signatureType" => "attached",
            'embedDataTs' => 'true'
        ];
        $this->sendRequest('put', "/ticket/{$this->ticketUuid}/option", $params);
    }

    private function uploadFile(): void
    {
        $this->sendRequest('put', "/ticket/{$this->ticketUuid}/keyStore", ['base64Data' => $this->base64File], true);
    }

    private function createKeyp(): void
    {
        $this->sendRequest('post', "/ticket/{$this->ticketUuid}/ds/creator", ['keyStorePassword' => $this->password]);
    }

    private function getKeypCreator($retryCount = 0, $maxRetries = 5)
    {
        $status = $this->sendRequest('get', "/ticket/{$this->ticketUuid}/ds/creator");

        if ($status['status'] == 202 && $retryCount < $maxRetries) {
            sleep(2);
            return $this->getKeypCreator($retryCount + 1, $maxRetries);
        }

        return $status['status'] == 200 ? $status : null;
    }

    private function getKeyp(): string
    {
        return $this->sendRequest('get', "/ticket/{$this->ticketUuid}/ds/base64Data")['base64Data'] ?? '';
    }

    private function deleteSession(): void
    {
        $this->sendRequest('delete', "/ticket/{$this->ticketUuid}");
    }

    private function sendRequest(string $method, string $url, array $data = [], bool $isFileUpload = false)
    {
        return (new Request($method, $url, json_encode($data), $isFileUpload))->sendRequest();
    }

    // Additional methods for decoding file container
    public function getDecodingFileContainerBase64()
    {
        return $this->sendRequest('get', "/ticket/{$this->ticketUuid}/decryptor/base64Data");
    }

    private function getDecodingFileContainerResultData($retryCount = 0, $maxRetries = 5)
    {
        $status = $this->sendRequest('get', "/ticket/{$this->ticketUuid}/decryptor");

        if ($status['status'] == 202 && $retryCount < $maxRetries) {
            return $this->getDecodingFileContainerResultData($retryCount + 1, $maxRetries);
        }

        return $status;
    }

    private function decodingFileContainer(): void
    {
        $this->sendRequest('post', "/ticket/{$this->ticketUuid}/decryptor", ['keyStorePassword' => '1111']);
    }
}

<?php

namespace App\Classes\Cipher\Api;

use App\Classes\Cipher\Request;
use App\Classes\eHealth\Exceptions\ApiException;
use Illuminate\Support\Facades\Cache;

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
     * @throws ApiException
     */
    public function sendSession(string $dataSignature, string $password, string $base64File, string $knedp): string
    {
        $this->dataSignature = base64_encode($dataSignature);
        $this->password = $password;
        $this->base64File = $base64File;
        $this->knedp = $knedp;
        $this->createSession();
        $this->loadTicket();
        $this->setParamsSession();
        $this->uploadFileContainerSession();
        $this->createKep();
        $this->getKepCreator();
        $kep = $this->getKep();
        $this->deleteSession();

        return $kep;
    }

    // Create session
    private function createSession(): void
    {
        $ticket = (new Request('post', '/ticket', ''))->sendRequest();
        $this->ticketUuid = $ticket['ticketUuid'] ?? '';
    }

    // Load data into session
    private function loadTicket(): void
    {
        $data = ['base64Data' => $this->dataSignature];
        (new Request('post', "/ticket/{$this->ticketUuid}/data", json_encode($data)))->sendRequest();
    }

    // Set session parameters
    private function setParamsSession(): void
    {
        $data = [
            'caId' => $this->knedp,
            'signatureType' => 'attached',
            'cadesType' => 'CAdESXLong',
        ];
        (new Request('put', "/ticket/{$this->ticketUuid}/option", json_encode($data)))->sendRequest();
    }

    // Upload file to session
    private function uploadFileContainerSession(): void
    {
        $data = ['base64Data' => $this->base64File];
        (new Request('put', "/ticket/{$this->ticketUuid}/keyStore", json_encode($data)))->sendRequest();
    }

    // Create KEYP
    private function createKep(): void
    {
        $data = ['keyStorePassword' => $this->password];
        (new Request('post', "/ticket/{$this->ticketUuid}/ds/creator", json_encode($data)))->sendRequest();
    }

    // Get information about KEYP creator
    private function getKepCreator(): void
    {
        (new Request('get', "/ticket/{$this->ticketUuid}/ds/creator", ''))->sendRequest();
    }

    // Get KEP in base64
    private function getKep(): string
    {
        $base64Data = (new Request('get', "/ticket/{$this->ticketUuid}/ds/base64Data", ''))->sendRequest();
        return $base64Data['base64Data'] ?? '';
    }

    // Delete session
    private function deleteSession(): void
    {
        (new Request('get', "/ticket/{$this->ticketUuid}", ''))->sendRequest();
    }

    /**
     * Get list of supported Certificate Authorities.
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
            Cache::put('knedp_certificate_authority', $data['ca'], now()->addDays(7));
        }
        return Cache::get('knedp_certificate_authority');
    }
}

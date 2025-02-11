<?php

namespace App\Livewire\Patient\Forms\Api;

use App\Classes\eHealth\Api\PersonApi;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PatientRequestApi extends PersonApi
{
    /**
     * Build an array of parameters for uploading files to storage.
     *
     * @param  TemporaryUploadedFile  $uploadedFile
     * @return array[]
     */
    public static function buildUploadFileRequest(TemporaryUploadedFile $uploadedFile): array
    {
        return [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($uploadedFile->getRealPath(), 'rb'),
                    'filename' => $uploadedFile->getClientOriginalName()
                ],
            ],
        ];
    }

    /**
     * Build an array of parameters for a patient request list.
     *
     * @param  string  $status  The status of the patient requests to fetch (NEW, APPROVED, SIGNED, REJECTED, CANCELLED).
     * @param  int  $page  The page number of the results to fetch. Default 1.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 300. Default: 50.
     * @return array
     */
    public static function buildGetPersonRequestList(string $status, int $page = 1, int $pageSize = 50): array
    {
        return [
            'status' => $status,
            'page' => $page,
            'page_size' => $pageSize
        ];
    }

    /**
     * Build an array of parameters for a patient request list.
     *
     * @param  bool  $isExpired  True if active to date-time less than now, false if active to date-time greater than now or null.
     * @param  int  $page  The page number of the results to fetch. Default 1.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 300. Default: 50.
     * @return array
     */
    public static function buildGetConfidantPersonRelationships(
        bool $isExpired,
        int $page = 1,
        int $pageSize = 50
    ): array {
        return [
            'is_expired' => $isExpired,
            'page' => $page,
            'page_size' => $pageSize
        ];
    }

    /**
     * Build an array of parameters for a patient request list.
     *
     * @param  array  $filters
     * @return array
     */
    public static function buildSearchForPerson(array $filters): array
    {
        foreach ($filters as $key => $filter) {
            $result[Str::snake($key)] = $filter;
        }

        self::removeEmptyKeys($result);

        return $result;
    }

    /**
     * Build an array of parameters for a patient short episodes.
     *
     * @param  int  $page  The page number of the results to fetch. Default 1.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 300. Default: 50.
     * @param  string|null  $periodStartFrom  Example: 2017-01-01.
     * @param  string|null  $periodStartTo  Example: 2018-01-01.
     * @param  string|null  $periodEndFrom  Example: 2017-01-01.
     * @param  string|null  $periodEndTo  Example: 2018-01-01.
     * @return array
     */
    public static function buildGetShortEpisodes(
        int $page = 1,
        int $pageSize = 50,
        string $periodStartFrom = null,
        string $periodStartTo = null,
        string $periodEndFrom = null,
        string $periodEndTo = null
    ): array {
        return [
            'page' => $page,
            'page_size' => $pageSize,
            'period_start_from' => $periodStartFrom,
            'period_start_to' => $periodStartTo,
            'period_end_from' => $periodEndFrom,
            'period_end_to' => $periodEndTo
        ];
    }

    /**
     * Build an array of parameters for a patient active diagnoses.
     *
     * @param  int  $page  Page number. Default 1.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 100. Default: 50.
     * @param  string|null  $code  Example: A20.
     * @return int[]
     */
    public static function buildGetActiveDiagnoses(int $page = 1, int $pageSize = 50, ?string $code = null): array
    {
        return [
            'page' => $page,
            'page_size' => $pageSize,
            'code' => $code
        ];
    }

    /**
     * Build an array of parameters for a patient active diagnoses.
     *
     * @param  int  $page  Page number. Default 1.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 100. Default: 50.
     * @param  string|null  $code  Example: 10569-2.
     * @param  string|null  $issuedFrom  Example: 1990-01-01.
     * @param  string|null  $issuedTo  Example: 2000-01-01.
     * @return int[]
     */
    public static function buildGetObservations(
        int $page = 1,
        int $pageSize = 50,
        ?string $code = null,
        ?string $issuedFrom = null,
        ?string $issuedTo = null
    ): array {
        return [
            'page' => $page,
            'page_size' => $pageSize,
            'code' => $code,
            'issued_from' => $issuedFrom,
            'issued_to' => $issuedTo
        ];
    }

    /**
     * Remove keys from an array if their values are empty strings.
     *
     * @param  array  $data
     * @return void
     */
    protected static function removeEmptyKeys(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if (is_object($value)) {
                // Convert object to array
                $value = (array) $value;
                self::removeEmptyKeys($value);
                // Convert array back to object
                $value = (object) $value;
            } elseif (is_array($value)) {
                self::removeEmptyKeys($value);
            } elseif ($value === '') {
                unset($data[$key]);
            }
        }
    }
}

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
     * @param  int  $page  The page number of the results to fetch.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 300. Default: 50.
     * @return array
     */
    public static function buildGetPersonRequestList(string $status, int $page, int $pageSize = 50): array
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

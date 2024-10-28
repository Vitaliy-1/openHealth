<?php

namespace App\Livewire\License\Forms;

use App\Classes\eHealth\Api\LicenseApi;
use App\Classes\eHealth\Request;
use Livewire\Component;

class LicenseRequestApi extends LicenseApi
{
    public static function getLicenses(array $params = []): array
    {
        if(!isset($params['page'])) {
            $params['page'] = 1;
        }

        if(!isset($params['page_size'])) {
            $params['page_size'] = 50;
        }

        $licensesApi = self::_get(
            $params
        );

        return !empty($licensesApi) ? $licensesApi : [];
    }

    public static function getLicense(string $license_id): array
    {
        $licenseApi = self::_get(
            [
                'license_id' => $license_id,
                'page' => 1,
                'page_size' => 50,
            ]
        );

        return !empty($licenseApi) ? $licenseApi : [];
    }

    public static function create($data): array
    {
        $data['type'] = 'MSP';
        $licenseCreateApi = self::_create($data);

        return !empty($licenseCreateApi) ? $licenseCreateApi : [];
    }

    public static function update($id, $data): array
    {
        $data['type'] = '"LEGAL_ENTITY_' . $data['type'] . '_ADDITIONAL_LICENSE_TYPE';

        $licenseUpdateApi = self::_update($id, $data);

        return !empty($licenseUpdateApi) ? $licenseUpdateApi : [];
    }
}

<?php

namespace App\Livewire\Registration\Forms;

class LegalEntitiesFormBuilder
{
    public static function saveBuilderLegalEntity($data): array
    {
        if (empty($data)) {
            return [];
        }

        return [
            'legal_entities_uuid' => $data['id'],
            'name' => $data['edr']['name'],
            'short_name' => $data['edr']['short_name'],
            'public_name' => $data['edr']['public_name'] ?? '',
            'type' => $data['type'] ?? '',
            'owner_property_type' => $data['owner_property_type'] ?? '',
            'legal_form' => $data['legal_form'] ?? '',
            'edrpou' => $data['edrpou'] ?? '',
            'kveds' => $data['edr']['kveds'] ?? '',
            'addresses' => $data['edr']['registration_address'] ?? '',
            'phones' => $data['phones'] ?? '',
            'email' => $data['email'] ?? '',
            'is_active' => $data['is_active'] ?? false,
            'mis_verified' => $data['mis_verified'] ?? '',
            'nhs_verified' => $data['nhs_verified'] ?? false,
            'website' => $data['website'] ?? '',
            'beneficiary' => $data['beneficiary'] ?? '',
            'receiver_funds_code' => $data['receiver_funds_code'] ?? '',
            'archive' => $data['archive'] ?? '',
        ];
    }

    public static function saveBuilderPerson($data){

        return  [
            'last_name' => $data['last_name'] ?? '',
            'first_name' => $data['first_name'] ?? '',

            'second_name' => $data['second_name'] ?? '',
            'birth_date' => $data['birth_date'] ?? '',
            'gender' => $data['gender'] ?? '',
            'email' => $data['email'] ?? '',
            'phones' => $data['phones'] ?? '',
            'tax_id' => $data['tax_id'] ?? '',
            'invalid_tax_id' => $data['invalid_tax_id'] ?? false,
            'is_active' => $data['is_active'] ?? false,
            'documents' => $data['documents'] ?? [],
        ];
    }

    public static function saveBuilderEmployee($data){

        return  [
            'position' => $data['position'] ?? '',
        ];
    }
    public static function getBuilderContact($data)
    {
        return [
            'website' => $data['website'] ?? '',
            'phones' => $data['phones'] ?? '',
            'email' => $data['email'] ?? '',
        ] ?? [];
    }

    public static function getBuilderRegionAddress($data)
    {
        return $data['residence_address'] ?? [];
    }

    public static function getBuilderAccreditation($data)
    {
        return $data['accreditation'] ?? [];
    }

    public static function getBuilderLicense($data)
    {
        return $data['license'] ?? [];
    }

    public static function getBuilderAdditionalInformation($data)
    {
        return $data['additional_information'] = [
            'archive' => $data['archive'][0] ?? '',
            'beneficiary' => $data['beneficiary'] ?? '',
            'receiver_funds_code' => $data['receiver_funds_code'] ?? '',
        ] ?? [];
    }
}

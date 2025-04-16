<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class TestUserMigrate extends Seeder
{
    public function run(): void
    {
        // Don't run seeder if data of the test instance isn't set
        if (!(config()->has('ehealth.test.client_id') && config()->has('ehealth.test.client_secret'))) {
            return;
        }

        $legalEntityId = DB::table('legal_entities')->insertGetId([
            'uuid' => config('ehealth.test.client_id'),
            'client_id' => config('ehealth.test.client_id'),
            'client_secret' => config('ehealth.test.client_secret'),
            'accreditation' => json_encode([
                'category' => 'SECOND',
                'expiry_date' => new Carbon('2027-02-28'),
                'issued_date' => new Carbon('2017-02-28'),
                'order_date' => new Carbon('2017-02-28'),
                'order_no' => 'fd123443',
            ]),
            'archive' => json_encode([
                [
                    'date' => new Carbon('2017-02-28'),
                    'place' => 'вул. Грушевського 15'
                ]
            ]),
            'beneficiary' => 'Безшейко Віталій Григорович',
            'edr' => json_encode([
                'edrpou' => '3139821559',
                'id' => '8ac2c0b8-e236-4d3c-9603-cc6c5f645d31',
                'kveds' => [
                    [
                        'code' => '86.90',
                        'is_primary' => false,
                        'name' => 'Інша діяльність у сфері охорони здоров\'я',
                    ],
                    [
                        'code' => '85.60',
                        'is_primary' => false,
                        'name' => 'Допоміжна діяльність у сфері освіти',
                    ],
                    [
                        'code' => '85.59',
                        'is_primary' => false,
                        'name' => 'Інші види освіти, н.в.і.у.',
                    ],
                    [
                        'code' => '74.90',
                        'is_primary' => false,
                        'name' => 'Інша професійна, наукова та технічна діяльність, н.в.і.у.',
                    ],
                    [
                        'code' => '74.30',
                        'is_primary' => false,
                        'name' => 'Надання послуг перекладу',
                    ],
                    [
                        'code' => '58.29',
                        'is_primary' => false,
                        'name' => 'Видання іншого програмного забезпечення',
                    ],
                    [
                        'code' => '58.11',
                        'is_primary' => false,
                        'name' => 'Видання книг',
                    ],
                    [
                        'code' => '58.14',
                        'is_primary' => false,
                        'name' => 'Видання журналів і періодичних видань',
                    ],
                    [
                        'code' => '62.09',
                        'is_primary' => false,
                        'name' => 'Інша діяльність у сфері інформаційних технологій і комп\'ютерних систем',
                    ],
                    [
                        'code' => '62.03',
                        'is_primary' => false,
                        'name' => 'Діяльність із керування комп\'ютерним устаткованням',
                    ],
                    [
                        'code' => '62.02',
                        'is_primary' => false,
                        'name' => 'Консультування з питань інформатизації',
                    ],
                    [
                        'code' => '62.01',
                        'is_primary' => true,
                        'name' => 'Комп\'ютерне програмування',
                    ]
                ],
                'legal_form' => null,
                'name' => 'БЕЗШЕЙКО ВІТАЛІЙ ГРИГОРОВИЧ',
                'public_name' => 'БЕЗШЕЙКО ВІТАЛІЙ ГРИГОРОВИЧ',
                'registration_address' => [
                    'address' => 'Україна, 02093, місто Київ, ВУЛИЦЯ АННИ АХМАТОВОЇ, будинок 22, квартира 22',
                    'country' => 'Україна',
                    'parts' => [
                        'atu' => 'місто Київ',
                        'atu_code' => '8036300000',
                        'building' => null,
                        'building_type' => null,
                        'house' => '22',
                        'house_type' => 'будинок',
                        'num' => '22',
                        'num_type' => 'квартира',
                        'street' => 'ВУЛИЦЯ АННИ АХМАТОВОЇ',
                    ],
                    'zip' => '02000',
                ],
                'short_name' => null,
                'state' => 1,
            ]),
            'edr_verified' => null,
            'edrpou' => '3139821559',
            'email' => 'vitaliybezsh@gmail.com',
            'inserted_by' => '4261eacf-8008-4e62-899f-de1e2f7065f0',
            'is_active' => true,
            'nhs_comment' => '',
            'nhs_reviewed' => true,
            'nhs_verified' => true,
            'receiver_funds_code' => '777',
            'status' => 'ACTIVE',
            'type' => 'PRIMARY_CARE',
            'updated_by' => '4261eacf-8008-4e62-899f-de1e2f7065f0',
            'website' => 'www.openhealths.com',
            'inserted_at' => new Carbon('2024-06-06T12:41:30.000000Z'),
            'created_at' => new Carbon('2024-10-17T13:29:18.000000Z'),
            'updated_at' => new Carbon('2024-10-17T13:29:24.000000Z'),
        ]);

        $addressId = DB::table('addresses')->insert([
            'type' => 'RESIDENCE',
            'country' => 'UA',
            'area' => 'М.КИЇВ',
            'region' => null,
            'settlement' => 'Київ',
            'settlement_type' => 'CITY',
            'settlement_id' => 'adaa4abf-f530-461c-bcbf-a0ac210d955b',
            'street_type' => 'STREET',
            'street' => 'Анни Ахматової',
            'building' => '22',
            'apartment' => '22',
            'zip' => '02000',
            'addressable_type' => 'App\Models\LegalEntity',
            'addressable_id' => $legalEntityId,
            'created_at' => new Carbon('2025-03-06T15:41:30Z'),
            'updated_at' => new Carbon('2025-03-10T13:40:10Z'),
        ]);

        $licenseId = DB::table('licenses')->insert([
            'uuid' => '869b92a2-5511-45c3-beca-b5c9e3ad099b',
            'type' => 'MSP',
            'legal_entity_id' => $legalEntityId,
            'issued_by' => 'Кваліфікацйна комісія',
            'issued_date' => new Carbon('2017-02-28'),
            'active_from_date' => new Carbon('2017-02-28'),
            'order_no' => 'ВА43234',
            'license_number' => 'fd123443',
            'expiry_date' => new Carbon('2027-02-28'),
            'what_licensed' => 'реалізація наркотичних засобів',
            'is_primary' => true,
            'created_at' => new Carbon('2024-06-06T15:41:30Z'),
            'updated_at' => new Carbon('2024-09-10T13:40:10Z'),
        ]);

        $userId = DB::table('users')->insertGetId([
            'id' => 1,
            'uuid' => '82d1f518-23c9-4c6c-868b-6f7ab26c6da8',
            'email' => 'vitaliybezsh@gmail.com',
            'password' => Hash::make(Str::random()),
            'email_verified_at' => null,
            'current_team_id' => null,
            'profile_photo_path' => null,
            'tax_id' => null,
            'settings' => null,
            'priv_settings' => null,
            'is_blocked' => null,
            'block_reason' => null,
            'person_id' => null,
            'created_at' => new Carbon('2024-09-11T10:00:52.000000Z'),
            'updated_at' => new Carbon('2024-09-11T10:03:10.000000Z'),
            'two_factor_confirmed_at' => null,
            'legal_entity_id' => $legalEntityId,
        ]);

        $ownerRoleId = DB::table('roles')->where('name', 'OWNER')->value('id');
        DB::table('model_has_roles')->insert([
            'role_id' => $ownerRoleId,
            'model_type' => 'App\Models\User',
            'model_id' => $userId
        ]);

        $partyId = DB::table('parties')->insertGetId([
            'uuid' => '8656775d-9258-405c-8841-10769360ee1e',
            'last_name' => 'Безшейко',
            'first_name' => 'Віталій',
            'second_name' => 'Григорович',
            'email' => 'vitaliybezsh@gmail.com',
            'birth_date' => new Carbon('1987-10-02'),
            'gender' => 'MALE',
            'tax_id' => '3139821559',
            'no_tax_id' => false,
            'about_myself' => null,
            'working_experience' => null
        ]);

        $documentId = DB::table('documents')->insertGetId([
            'type' => 'PASSPORT',
            'number' => 'РО8927422',
            'issued_by' => 'Рокитнянський РОВД',
            'issued_at' => new Carbon('2025-03-27'),
            'expiration_date' => null,
            'documentable_type' => 'App\Models\Relations\Party',
            'documentable_id' => $partyId
        ]);

        $legalEntityPhoneId = DB::table('phones')->insertGetId([
            'type' => 'MOBILE',
            'number' => '+380506491244',
            'phoneable_type' => 'App\Models\LegalEntity',
            'phoneable_id' => $legalEntityId
        ]);

        $partyPhoneId = DB::table('phones')->insertGetId([
            'type' => 'MOBILE',
            'number' => '+380506491244',
            'phoneable_type' => 'App\Models\Relations\Party',
            'phoneable_id' => $partyId
        ]);

        DB::table('employees')->insert([
            'uuid' => '85b30921-bcef-4a27-8997-5ef11290fbe6',
            'division_uuid' => null,
            'legal_entity_uuid' => config('ehealth.test.client_id'),
            'position' => 'P2',
            'start_date' => new Carbon('2024-09-04T21:00:00.000000Z'),
            'end_date' => null,
            'employee_type' => 'OWNER',
            'inserted_at' => null,
            'status' => 'APPROVED',
            'legal_entity_id' => $legalEntityId,
            'division_id' => null,
            'user_id' => $userId,
            'party_id' => $partyId,
            'created_at' => new Carbon('2024-11-14T10:37:35.000000Z'),
            'updated_at' => new Carbon('2024-11-14T10:37:35.000000Z'),
        ]);
    }
}

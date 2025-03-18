<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Classes\eHealth\Exceptions\ApiException;
use DB;
use Illuminate\Database\Seeder;
use JsonException;

class ICD10Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws ApiException
     * @throws JsonException
     */
    public function run(): void
    {
        $dictionary = dictionary()->getLargeDictionary(['name' => 'eHealth/ICD10_AM/condition_codes'])['eHealth/ICD10_AM/condition_codes'];

        $data = [];
        foreach ($dictionary as $key => $value) {
            $data[] = [
                'code'         => $key,
                'description'  => $value['description'],
                'is_active'    => $value['is_active'],
                'child_values' => json_encode($value['child_values'], JSON_THROW_ON_ERROR),
                'created_at'   => now(),
                'updated_at'   => now()
            ];
        }

        // Вставка порціями по 1000 записів
        $chunks = array_chunk($data, 10000);
        foreach ($chunks as $chunk) {
            DB::table('icd_10')->insert($chunk);
        }
    }
}

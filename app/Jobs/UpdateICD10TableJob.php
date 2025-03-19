<?php

declare(strict_types=1);

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateICD10TableJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::channel('task_scheduling')->info('Updating ICD-10 codes begins.');

            $dictionary = dictionary()->getLargeDictionary(['name' => 'eHealth/ICD10_AM/condition_codes'])['eHealth/ICD10_AM/condition_codes'];

            $data = [];
            foreach ($dictionary as $key => $value) {
                $data[] = [
                    'code' => $key,
                    'description' => $value['description'],
                    'is_active' => $value['is_active'],
                    'child_values' => json_encode($value['child_values'], JSON_THROW_ON_ERROR),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Clear an old table before inserting new data
            DB::table('icd_10')->truncate();

            // Insert data by chunks
            $chunks = array_chunk($data, 10000);
            foreach ($chunks as $chunk) {
                DB::table('icd_10')->insert($chunk);
            }

            Log::channel('task_scheduling')->info('Updating ICD-10 codes successfully ended.');
        } catch (Exception $e) {
            Log::channel('task_scheduling')->error('Error while updating ICD-10 codes.', [
                'message' => $e->getMessage()
            ]);
        }
    }
}

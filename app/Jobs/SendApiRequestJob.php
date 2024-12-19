<?php

namespace App\Jobs;

use App\Classes\eHealth\Api\EmployeeApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Repositories\EmployeeRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendApiRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed
     */
    private string $signData;

    /**
     * Create a new job instance.
     */
    public function __construct($singData)
    {
        $this->signData = $singData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employeeRequest = EmployeeRequestApi::createEmployeeRequest([
            'signed_content' => $this->signData,
            'signed_content_encoding' => 'base64',
        ]);
        $this->apiResponse($employeeRequest);
    }


    public function apiResponse($response)
    {
        $employeeResponse = schemaService()->setDataSchema($response,app(EmployeeApi::class))
            ->responseSchemaNormalize()
            ->replaceIdsKeysToUuid(['id','legal_entity_id','division_id','party_id'])
            ->filterNormalizedData()
            ->getNormalizedData();

        app(EmployeeRepository::class)->saveEmployeeData($employeeResponse, auth()->user()->legalEntity, 'employeeRequest');

    }
}

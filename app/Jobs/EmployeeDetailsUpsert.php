<?php

namespace App\Jobs;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use App\Classes\eHealth\EHealth;

class EmployeeDetailsUpsert implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Employee $employee,
        public User $user,
        protected string $token
    ) {}

    public function middleware(): array
    {
        return [new RateLimited('ehealth-employee-get')];
    }

    public function handle(): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $response = EHealth::employee()->withToken($this->token)->getDetails($this->employee->uuid, groupByEntities: true);

        [
            'party' => $party,
            'documents' => $documents,
            'phones' => $phones,
            'educations' => $educations,
            'specialities' => $specialities,
            'qualifications' => $qualifications,
            'scienceDegrees' => $scienceDegrees,
        ] = $response->validate();

        Repository::employee()->updateDetails($this->employee, $party, $documents, $phones, $educations, $specialities, $qualifications, $scienceDegrees);
    }
}

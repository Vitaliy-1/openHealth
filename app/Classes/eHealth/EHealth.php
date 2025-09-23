<?php

declare(strict_types=1);

namespace App\Classes\eHealth;

use App\Classes\eHealth\Api\Declaration;
use App\Classes\eHealth\Api\DeclarationRequest;
use App\Classes\eHealth\Api\Employee;
use App\Classes\eHealth\Api\EmployeeRequest;
use App\Classes\eHealth\Api\License;
use App\Classes\eHealth\Api\Job;
use App\Classes\eHealth\Api\Division;
use App\Classes\eHealth\Api\HealthcareService;
use App\Classes\eHealth\Api\Person;
use App\Classes\eHealth\Api\PersonRequest;
use App\Classes\eHealth\Api\RuleEngineRules;

final class EHealth
{
    public static function license(): License
    {
        return app(License::class);
    }

    public static function job(): Job
    {
        return app(Job::class);
    }

    public static function personRequest(): PersonRequest
    {
        return app(PersonRequest::class);
    }

    public static function person(): Person
    {
        return app(Person::class);
    }

    public static function declarationRequest(): DeclarationRequest
    {
        return app(DeclarationRequest::class);
    }

    public static function declaration(): Declaration
    {
        return app(Declaration::class);
    }

    public static function ruleEngineRules(): RuleEngineRules
    {
        return app(RuleEngineRules::class);
    }

    public static function division()
    {
        return app(Division::class);
    }

    public static function healthcareService()
    {
        return app(HealthcareService::class);
    }

    public static function employee(): Employee
    {
        return app(Employee::class);
    }

    public static function employeeRequest(): EmployeeRequest
    {
        return app(EmployeeRequest::class);
    }
}

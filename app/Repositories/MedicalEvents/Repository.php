<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

final class Repository
{
    public static function identifier(): IdentifierRepository
    {
        return app(IdentifierRepository::class);
    }

    public static function coding(): CodingRepository
    {
        return app(CodingRepository::class);
    }

    public static function codeableConcept(): CodeableConceptRepository
    {
        return app(CodeableConceptRepository::class);
    }

    public function encounter(): EncounterRepository
    {
        return app(EncounterRepository::class);
    }

    public static function condition(): ConditionRepository
    {
        return app(ConditionRepository::class);
    }

    public static function episode(): EpisodeRepository
    {
        return app(EpisodeRepository::class);
    }
}

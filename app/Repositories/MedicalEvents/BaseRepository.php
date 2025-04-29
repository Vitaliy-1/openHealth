<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    // Add logical operators after implementing MongoDB, like Model|MongoModel
    // For now, it's only SQL models for the IDE to prompt
    public function __construct(protected Model $model)
    {
    }
}

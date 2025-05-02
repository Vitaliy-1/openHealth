<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Mongo;

//use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperEncounter
 */
class Encounter extends Model
{
    //    protected $connection = 'mongodb';
    protected $guarded = [];
}

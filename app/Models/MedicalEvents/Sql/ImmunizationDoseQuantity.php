<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperImmunizationDoseQuantity
 */
class ImmunizationDoseQuantity extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'id',
        'immunization_id',
        'created_at',
        'updated_at'
    ];
}

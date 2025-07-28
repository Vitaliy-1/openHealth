<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperClinicalImpressionSupportingInfo
 */
class ClinicalImpressionSupportingInfo extends Model
{
    protected $table = 'clinical_impression_supporting_info';

    protected $guarded = [];
}

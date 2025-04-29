<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperPeriod
 */
class Period extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'id',
        'periodable_type',
        'periodable_id',
        'created_at',
        'updated_at'
    ];

    public function periodable(): MorphTo
    {
        return $this->morphTo();
    }
}

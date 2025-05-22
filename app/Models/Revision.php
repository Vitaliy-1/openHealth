<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Revision extends Model
{
    use HasCamelCasing,
        SoftDeletes;

    public const STATUS_APPLIED = 'APPLIED';
    public const STATUS_OUTDATED = 'OUTDATED';
    public const STATUS_PENDING = 'PENDING';

    protected $hidden = [
        'id',
        'revisionable_type',
        'revisionable_id',
    ];

    protected $fillable = [
        'data',
        'status',
        'revisionable_type',
        'revisionable_id'
    ];

    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function setApplied(): void
    {
        $this->status = self::STATUS_APPLIED;
        $this->save();
        $this->delete();
    }

    public function setOutdated(): void
    {
        $this->status = self::STATUS_OUTDATED;
        $this->save();
        $this->delete();
    }
}

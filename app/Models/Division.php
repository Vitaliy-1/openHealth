<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'external_id',
        'name',
        'type',
        'mountaint_group',
        'location',
        'addresses',
        'phones',
        'email',
        'working_hours',
        'is_active',
        'legal_entity_id',
        'status',
    ];

    protected $casts = [
        'location' => 'geometry',
        'addresses' => 'json',
        'phones' => 'json',
        'working_hours' => 'json',
        'is_active' => 'boolean',
    ];

    public function legalEntity()
    {
        return $this->belongsTo(LegalEntity::class);
    }


}

<?php

namespace App\Models\Relations;

use App\Models\PersonRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfidantPerson extends Model
{
    protected $table = 'confidant_persons';

    protected $fillable = [
        'person_request_id',
        'person_id',
        'documents_relationship'
    ];

    protected $casts = [
        'documents_relationship' => 'array'
    ];

    public function personRequest(): BelongsTo
    {
        return $this->belongsTo(PersonRequest::class);
    }
}

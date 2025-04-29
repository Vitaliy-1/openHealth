<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperIdentifier
 */
class Identifier extends Model
{
    protected $guarded = [];

    protected $appends = ['identifier'];

    protected $hidden = [
        'id',
        'type',
        'value',
        'identifiable_type',
        'identifiable_id',
        'created_at',
        'updated_at'
    ];

    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'type' => $this->type->map(fn (CodeableConcept $codeableConcept) => [
                    'text' => $codeableConcept->text,
                    'coding' => $codeableConcept->coding->toArray()
                ])->toArray(),
                'value' => $this->value
            ]
        );
    }

    public function identifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function type(): MorphMany
    {
        return $this->morphMany(CodeableConcept::class, 'codeable_conceptable');
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Models\User;
use App\Models\LegalEntity;
use App\Models\Relations\Party;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An abstract base class for Employee and EmployeeRequest models.
 * It contains only the common logic, properties, and relationships.
 *
 * @mixin IdeHelperBaseEmployee
 */
abstract class BaseEmployee extends Model
{
    use HasCamelCasing;

    /**
     * Common fillable attributes for both employees and requests.
     */
    protected $fillable = [
        'uuid',
        'legal_entity_uuid',
        'division_uuid',
        'legal_entity_id',
        'status',
        'position',
        'start_date',
        'end_date',
        'party_id',
        'employee_type',
        'user_id',
        'division_id',
        'inserted_at',
        'is_active'
    ];

    /**
     * Common casts.
     */
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    // --- COMMON ACCESSORS ---

    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn () => implode(
                ' ',
                array_filter(
                    [
                        $this->party?->last_name,
                        $this->party?->first_name,
                        $this->party?->second_name,
                    ]
                )
            )
        );
    }

    protected function isVerified(): Attribute
    {
        return Attribute::get(fn () => $this->user?->email_verified_at !== null);
    }

    // --- COMMON RELATIONS ---

    public function legalEntity(): BelongsTo
    {
        return $this->belongsTo(LegalEntity::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Division::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

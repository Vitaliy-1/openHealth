<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'legal_entity_id',
        'type',
        'issued_by',
        'issued_date',
        'active_from_date',
        'order_no',
        'license_number',
        'expiry_date',
        'what_licensed',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($license) {
            self::invalidateCache($license->legal_entity_id);
        });

        static::updated(function ($license) {
            self::invalidateCache($license->legal_entity_id);
        });

        static::deleted(function ($license) {
            self::invalidateCache($license->legal_entity_id);
        });

        static::updated(function ($license) {
            $cacheKey = "license_{$license->id}";
            Cache::forget($cacheKey);
        });

        static::deleted(function ($license) {
            $cacheKey = "license_{$license->id}";
            Cache::forget($cacheKey);
        });
    }

    protected static function invalidateCache($legal_entity_id)
    {
        $user = auth()->user();
        if ($user && $user->legal_entity_id == $legal_entity_id) {
            $userId = $user->id;
            $licenseTypes = ['all']; // Include 'all' as one of the types to invalidate
            $licenseStatusOptions = ['is_primary', 'is_additional', 'all'];

            foreach ($licenseTypes as $type) {
                foreach ($licenseStatusOptions as $status) {
                    $cacheKey = "licenses_user_id-{$userId}-{$type}-{$status}";
                    Cache::forget($cacheKey);
                }
            }
        }
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'legal_entity_id', 'legal_entity_id');
    }


    public function legalEntity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LegalEntity::class, 'legal_entity_id', 'id');
    }
}

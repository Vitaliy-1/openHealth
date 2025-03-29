<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Employee\EmployeeRequest;
use App\Models\Person\Person;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles {
        getAllPermissions as getAllPermissionsTrait;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'legal_entity_id',
        'secret_key',
        'tax_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['legalEntity', 'person'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function legalEntity(): BelongsTo
    {
        return $this->belongsTo(LegalEntity::class);
    }

    public function isClientId(): bool
    {
        return $this->legalEntity->client_id ?? false;
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'legal_entity_id', 'legal_entity_id');
    }

    public function employeeRequests(): HasMany
    {
        return $this->hasMany(EmployeeRequest::class);
    }

    /**
     * Overides trait's method to exclude unused scopes
     * @return Collection<Permission> a list of scopes associated with the user and entity type
     */
    public function getAllPermissions(): Collection
    {
        $scopes = $this->getAllPermissionsTrait();
        $legalEntityType = $this->legalEntity->type;
        $exclude = []; // exclude scopes not used by the entity
        switch ($legalEntityType) {

            case LegalEntity::TYPE_PRIMARY_CARE:
                $exclude = array_merge($exclude, ['contract:', 'contract_request:']);
                break;
        }

        return $scopes->filter(fn(Permission $permission) =>
            !Str::startsWith($permission->name, $exclude)
        );
    }
}

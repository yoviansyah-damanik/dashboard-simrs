<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Ramsey\Uuid\Uuid;
use App\Traits\BaseModelTrait;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, BaseModelTrait, HasRoles, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Generate a new UUID for the model.
     */
    public function newUniqueId(): string
    {
        return (string) Uuid::uuid4();
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function isInternalUser(): Attribute
    {
        return new Attribute(
            get: fn() => in_array($this->roleName, ['Kepala Rumah Sakit', 'Staf Rumah Sakit', 'IT'])
        );
    }

    public function isExternalUser(): Attribute
    {
        return new Attribute(
            get: fn() => in_array($this->roleName, ['Puskesad'])
        );
    }

    public function roleName(): Attribute
    {
        return new Attribute(
            get: fn() => $this->roles[0]->name
        );
    }

    public function histories(): HasMany
    {
        return $this->hasMany(UserHistory::class);
    }
}

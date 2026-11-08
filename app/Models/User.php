<?php

namespace App\Models;

use App\Enums\UserLevel;
use App\Models\Role;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute as CastAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use SoftDeletes;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string>|null */
    protected ?array $permissionNamesCache = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'level' => UserLevel::class,
        ];
    }

    public static function normalizeEmail(string $email): string
    {
        $email = preg_replace('/[\p{Cf}\p{Cc}]/u', '', $email) ?? '';
        $email = preg_replace('/^[\p{Z}\s]+|[\p{Z}\s]+$/u', '', $email) ?? '';

        return mb_strtolower($email, 'UTF-8');
    }

    protected function email(): CastAttribute
    {
        return CastAttribute::make(
            set: fn ($value) => $value === null ? null : static::normalizeEmail((string) $value),
        );
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool{
        return $this->roles()->where('name', $role)->exists();
    }

    public function isCreator(): bool
    {
        return $this->level === UserLevel::Creator;
    }

    public function isAdmin(): bool
    {
        return $this->level === UserLevel::Admin;
    }

    public function canAccessPanel(): bool
    {
        return $this->hasVerifiedEmail() && ($this->isCreator() || $this->isAdmin());
    }

    public function hasPermission(string $permission): bool{
        if($this->isCreator()){
            return true;
        }

        if(! $this->isAdmin()){
            return false;
        }

        return in_array($permission, $this->permissionNames(), true);  
    }

    /**
     * @return list<string>
     */
    public function permissionNames(): array{
        if($this->permissionNamesCache !== null){
            return $this->permissionNamesCache;
        }

        return $this->permissionNamesCache = $this->roles()->with('permissions')->get()->flatMap(
            fn($role) => $role->permissionNames()
        )->unique()->values()->all();
    }
    
    public function markEmailVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail());
    }
}

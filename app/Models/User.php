<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Enums\UserLevel;
use App\Models\Role;

class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const DEFAULT_PERMISSIONS =[
        'create-post',
        'update-own-post',
        'delete-own-post',
    ];

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

    public function hasPermission(string $permission): bool{
        if($this->isCreator()){
            return true;
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
        $roles = $this->roles()->with('permissions')->get();

        if($roles->isEmpty()){
            return $this->permissionNamesCache = self::DEFAULT_PERMISSIONS;
        }
        return $this->permissionNamesCache = $roles->flatMap(fn($role) => $role->permissions->pluck('name'))->unique()->values()->all();
    }
}

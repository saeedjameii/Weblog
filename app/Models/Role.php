<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];
    
    public function permissions(){
        return $this->belongsToMany(Permission::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function pluckPermissions(string $column = 'name'): array
    {
        return $this->permissions->pluck($column)->toArray();
    }

    public function permissionIds(): array
    {
        return $this->pluckPermissions('id');
    }
    
    public function permissionNames(): array
    {
        return $this->pluckPermissions('name');
    }

    public function hasPermission(string $permissionName): bool
    {
        return in_array($permissionName, $this->permissionNames(), true);
    }
}

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

    public function permissionIds(){
        return $this->permissions->pluck('id')->toArray();
    }
    
    public function permissionNames(){
        return $this->permissions->pluck('name')->toArray();
    }

    public function hasPermission(string $permissionName): bool{
        return in_array($permissionNames(), true);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(){
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }
    public function create(){
        $permissions = Permission::where('name', '!=', 'create-role')->get();
        return view('roles.create', compact('permissions'));
    }
    
    public function store(RoleRequest $request){
        $role = Role::create([
            'name' => $request->name,
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.create')->with('success', 'نقش با موفقیت ساخته شد');
    }

    public function edit(Role $role){
        $permissions = Permission::where('name', '!=', 'create-role')->get();

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact(
            'role','permissions','rolePermissions'
        ));
    }

    public function update(RoleRequest $request, Role $role){

        $role->update([
            'name' => $request->name,
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'نقش مورد نظر با موفقیت ویرایش گردید');
    }

    public function destroy(Role $role){

        $role->delete();
        
        return redirect()->route('roles.index')->with('success', 'نقش با موفقیت حذف گردید');
    }
}

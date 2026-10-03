<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {
    }

    public function index(){
        $roles = Role::with('permissions')->get();
        return view('panel.roles.index', compact('roles'));
    }
    public function create(){
        $permissions = Permission::all();
        return view('panel.roles.create', compact('permissions'));
    }
    
    public function store(RoleRequest $request){
        $data = $request->validated();

        $this->roleService->create($data);

        return redirect()->route('panel.roles.create')->with('success', 'نقش با موفقیت ساخته شد');
    }

    public function edit(Role $role){
        $permissions = Permission::all();

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('panel.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(RoleRequest $request, Role $role){
        $data = $request->validated();

        $this->roleService->update($role, $data);

        return redirect()->route('panel.roles.index')->with('success', 'نقش مورد نظر با موفقیت ویرایش گردید');
    }

    public function destroy(Role $role){

        $role->delete();
        
        return redirect()->route('panel.roles.index')->with('success', 'نقش با موفقیت حذف گردید');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRoleRequest;
use App\Models\Role;
use App\Models\User;

class UserRoleController extends Controller
{
    public function index(){
        
        $users = User::withTrashed()->with('roles')->orderBy('first_name')->get();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }
    
    public function update(UserRoleRequest $request, User $user){
        $data = $request->validated();

        if($user->isCreator()){
            abort(403, 'نقش creator قابل تغییر نمی‌باشد');
        }

        $user->roles()->sync($data['role_ids'] ?? []);
        return back()->with('success', 'نقش کاربر با موفقیت بروزرسانی شد');
    }

    public function destroy(User $user){
        if($user->isCreator()){
            abort(403, 'creator را نمی‌توانید حذف کنید');
        }

        $user->posts()->update([
            'status' => 'archived'
        ]);

        $user->delete();

        return back()->with('success', 'کاربر با موفقیت حذف گردید');
    }

    public function restore($id){
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            return back()->withErrors([
                'user' => 'این کاربر حذف نشده است.'
            ]);
        }

        if($user->isCreator()){
            abort(403, 'creator قابل بازگشت نمی‌باشد');
        }
        
        $user->restore();
        return back()->with('success', 'کاربر با موفقیت بازیابی شد');
    }
}

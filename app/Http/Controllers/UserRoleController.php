<?php

namespace App\Http\Controllers;

use App\Enums\UserLevel;
use App\Http\Requests\UserRoleRequest;
use App\Models\Role;
use App\Models\User;

class UserRoleController extends Controller
{
    public function index(){

        $users = User::withTrashed()->with('roles')->orderBy('first_name')->get();
        $roles = Role::all();

        return view('panel.users.index', compact('users', 'roles'));
    }

    public function update(UserRoleRequest $request, User $user){
        $data = $request->validated();

        if($user->isCreator()){
            abort(403, 'نقش creator قابل تغییر نمی‌باشد');
        }

        if(! $user->isAdmin()){
            abort(403, 'ابتدا این کاربر را به سطح ادمین ارتقا دهید');
        }

        $user->roles()->sync($data['role_ids'] ?? []);
        return back()->with('success', 'نقش کاربر با موفقیت بروزرسانی شد');
    }

    public function promote(User $user){
        if(! auth('api')->user()->isCreator()){
            abort(403, 'فقط سازنده می‌تواند سطح دسترسی کاربران را تغییر دهد');
        }

        if($user->isCreator()){
            abort(403, 'سطح دسترسی creator قابل تغییر نیست');
        }

        if($user->isAdmin()){
            return back()->with('success', 'این کاربر از قبل ادمین است');
        }

        $user->update(['level' => UserLevel::Admin]);

        return back()->with('success', 'کاربر با موفقیت به سطح ادمین ارتقا یافت');
    }

    public function demote(User $user)
    {
        if (! auth('api')->user()->isCreator()) {
            abort(403, 'فقط سازنده می‌تواند دسترسی کاربران را تغییر دهد');
        }

        if ($user->isCreator()) {
            abort(403, 'سطح دسترسی creator قابل تغییر نیست');
        }

        if (! $user->isAdmin()) {
            return back()->with('success', 'این کاربر از قبل یوزر است');
        }

        $user->update(['level' => UserLevel::User]);
        $user->roles()->sync([]);

        return back()->with('success', 'ادمین با موفقیت به سطح کاربر تنزل یافت');
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

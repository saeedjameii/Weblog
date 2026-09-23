<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
use App\Http\Middleware\JwtCookieMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'home'])->name('home');

Route::get('/panel', [DashboardController::class, 'index'])->middleware(JwtCookieMiddleware::class)->name('panel');

Route::get('sign-up', [AuthController::class, 'signUp'])->name('signUp');
Route::post('sign-up', [AuthController::class, 'signUpPost'])->name('signUp.post');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('posts')->group(function (){
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::get('/my-posts', [PostController::class, 'myPosts'])->middleware(JwtCookieMiddleware::class)->name('posts.mine');

    Route::middleware(['permission:create-post'])->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create_post');
        Route::post('/create', [PostController::class, 'createPost'])->name('create_post.post');
    });

    Route::get('/trashed', [PostController::class, 'trashed'])->middleware('permission:delete-any-post')->name('posts.trashed');
    Route::get('/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/{post}', [PostController::class, 'update'])->name('posts.update');

    Route::delete('/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::patch('/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
    
    Route::get('/{post}', [PostController::class, 'show'])->name('posts.show');
});

Route::prefix('categories')->group(function(){
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::middleware(['permission:create-category'])->group(function () {
        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    });
    Route::middleware(['permission:update-category'])->group(function () {
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('/{category}', [CategoryController::class, 'update'])->name('categories.update');
    });
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete-category')->name('categories.destroy');
});

Route::get('/users', [UserRoleController::class, 'index'])->middleware('permission:assign-role')->name('users.index');
Route::put('/users/{user}/role', [UserRoleController::class, 'update'])->middleware('permission:assign-role')->name('users.role.update');
Route::delete('/users/{user}', [UserRoleController::class, 'destroy'])->middleware('permission:manage-users,assign-role')->name('users.destroy');
Route::patch('/users/{id}/restore', [UserRoleController::class, 'restore'])->middleware('permission:manage-users')->name('users.restore');

Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:create-role,update-role')->name('roles.index');
Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:create-role')->name('roles.create');
Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:create-role')->name('roles.store');

Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:update-role')->name('roles.edit');
Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:update-role')->name('roles.update');

Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-role')->name('roles.destroy');

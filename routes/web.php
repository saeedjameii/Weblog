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

Route::get('sign-up', [AuthController::class, 'signUp'])->name('signUp');
Route::post('sign-up', [AuthController::class, 'signUpPost'])->name('signUp.post');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::prefix('panel')->middleware(['can:access-panel'])->group(function(){
    Route::get('/', [DashboardController::class, 'index'])->middleware([JwtCookieMiddleware::class])->name('panel');
    Route::name('panel.')->group(function(){
        Route::prefix('/posts')->name('posts.')->group(function(){
            Route::get('/my-posts', [PostController::class, 'myPosts'])->middleware(JwtCookieMiddleware::class)->name('mine');
            Route::middleware('permission:create-post')->group(function(){
                Route::get('/create', [PostController::class, 'create'])->name('create');
                Route::post('/create', [PostController::class, 'createPost'])->name('store');
            });
            Route::get('/trashed', [PostController::class, 'trashed'])->middleware('permission:delete-any-post')->name('trashed');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/restore', [PostController::class, 'restore'])->name('restore');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'panelIndex'])->middleware('permission:create-category,update-category,delete-category')->name('index');
            Route::middleware('permission:create-category')->group(function () {
                Route::get('/create', [CategoryController::class, 'create'])->name('create');
                Route::post('/', [CategoryController::class, 'store'])->name('store');
            });

            Route::middleware('permission:update-category')->group(function () {
                Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
                Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
            });

            Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete-category')->name('destroy');
        });
        Route::prefix('users')->name('users.')->group(function () {
            Route::middleware('permission:assign-role')->group(function () {
                Route::get('/', [UserRoleController::class, 'index'])->name('index');
                Route::put('/{user}/role', [UserRoleController::class, 'update'])->name('role.update');
            });

            Route::delete('/{user}', [UserRoleController::class, 'destroy'])->middleware('permission:assign-role,manage-users')->name('destroy');

            Route::patch('/{id}/restore', [UserRoleController::class, 'restore'])->middleware('permission:manage-users')->name('restore');

            Route::middleware(JwtCookieMiddleware::class)->group(function () {
                Route::patch('/{user}/promote', [UserRoleController::class, 'promote'])->name('promote');
                Route::patch('/{user}/demote', [UserRoleController::class, 'demote'])->name('demote');
            });
        });
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->middleware('permission:create-role,update-role')->name('index');

            Route::middleware('permission:create-role')->group(function () {
                Route::get('/create', [RoleController::class, 'create'])->name('create');
                Route::post('/', [RoleController::class, 'store'])->name('store');
            });

            Route::middleware('permission:update-role')->group(function () {
                Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
                Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            });

            Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-role')->name('destroy');
        });
    });
});





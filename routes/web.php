<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JwtCookieMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'home'])->name('home');

Route::middleware([RedirectIfAuthenticated::class])->group(function () {
    Route::get('sign-up', [AuthController::class, 'signUp'])->name('signUp');
    Route::post('sign-up', [AuthController::class, 'signUpPost'])->middleware('throttle:5,1')->name('signUp.post');

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->middleware('throttle:10,1')->name('login.post');
});

Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'verifyResend'])->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

Route::get('/forget-password', [AuthController::class, 'forgetPassword'])->middleware('guest')->name('password.request');
Route::post('/forget-password', [AuthController::class, 'sendResetLink'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->middleware('guest')->name('password.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::get('/categories/posts', [CategoryController::class, 'postIndex'])->name('categories.posts.index');
Route::get('/categories/products', [CategoryController::class, 'productIndex'])->name('categories.products.index');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::prefix('/compareList/module')->middleware(['auth:api', 'verified'])->group(function () {
    Route::post('/store', [ProductController::class, 'storeCompareList'])->name('compareList.store');
    Route::delete('/', [ProductController::class, 'removeCompareList'])->name('compareList.destroy');
    Route::get('/show', [ProductController::class, 'showCompareList'])->name('compareList.show');
});


Route::prefix('panel')->middleware(['can:access-panel'])->group(function(){
    Route::get('/', [DashboardController::class, 'index'])->middleware([JwtCookieMiddleware::class])->name('panel');
    Route::name('panel.')->group(function(){
        Route::prefix('/posts')->name('posts.')->group(function(){
            Route::get('/my-posts', [PostController::class, 'myPosts'])->middleware(JwtCookieMiddleware::class)->name('mine');
            Route::get('/all-posts', [PostController::class, 'allPosts'])
                ->middleware('permission:update-any-post,delete-any-post')
                ->name('all');
            Route::middleware('permission:create-post')->group(function(){
                Route::get('/create', [PostController::class, 'create'])->name('create');
                Route::post('/create', [PostController::class, 'createPost'])->name('store');
            });
            Route::get('/trashed', [PostController::class, 'trashed'])->middleware('permission:delete-any-post')->name('trashed');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/restore', [PostController::class, 'restore'])->name('restore');
            
            Route::get('/{post}', [PostController::class, 'PanelShow'])->name('show');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::post('/reorder', [CategoryController::class, 'reorder'])->middleware('permission:update-category')->name('reorder');
            Route::middleware('permission:create-category,update-category,delete-category')->group(function () {
                Route::get('/posts', [CategoryController::class, 'postPanelIndex'])->name('posts.index');
                Route::get('/products', [CategoryController::class, 'productPanelIndex'])->name('products.index');
            });
            Route::middleware('permission:create-category')->group(function () {
                Route::get('/posts/create', [CategoryController::class, 'createPost'])->name('posts.create');
                Route::post('/posts', [CategoryController::class, 'storePost'])->name('posts.store');
                Route::get('/products/create', [CategoryController::class, 'createProduct'])->name('products.create');
                Route::post('/products', [CategoryController::class, 'storeProduct'])->name('products.store');
            });
            Route::middleware('permission:update-category')->group(function () {
                Route::get('/posts/{category}/edit', [CategoryController::class, 'editPost'])->name('posts.edit');
                Route::put('/posts/{category}', [CategoryController::class, 'updatePost'])->name('posts.update');
                Route::get('/products/{category}/edit', [CategoryController::class, 'editProduct'])->name('products.edit');
                Route::put('/products/{category}', [CategoryController::class, 'updateProduct'])->name('products.update');
            });
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete-category')->name('destroy');
        });
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])
                ->middleware('permission:assign-role,manage-users')
                ->name('index');

            Route::put('/{user}/role', [UserController::class, 'update'])
                ->middleware('permission:assign-role')
                ->name('role.update');
            
            Route::middleware('permission:manage-users')->group(function () {
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                Route::patch('/{id}/restore', [UserController::class, 'restore'])->name('restore');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserController::class, 'updateUser'])->name('update');
            });

            Route::middleware(JwtCookieMiddleware::class)->group(function () {
                Route::patch('/{user}/promote', [UserController::class, 'promote'])->name('promote');
                Route::patch('/{user}/demote', [UserController::class, 'demote'])->name('demote');
            });
        });
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->middleware('permission:create-role,update-role,delete-role')->name('index');

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

        Route::prefix('products')->name('products.')->group(function (){
            Route::get('/', [ProductController::class, 'panelIndex'])->middleware('permission:create-product,update-product,delete-product')->name('index');

            Route::middleware(['permission:create-product'])->group(function () {
                Route::get('/create', [ProductController::class, 'create'])->name('create');
                Route::post('/', [ProductController::class, 'store'])->name('store');
            });

            Route::middleware(['permission:update-product'])->group(function () {
                Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
                Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            });
                Route::get('/trashed', [ProductController::class, 'trashed'])->middleware('permission:delete-product,update-product')->name('trashed');
                Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete-product')->name('destroy');
                Route::patch('/{id}/restore', [ProductController::class, 'restore'])->middleware('permission:update-product,delete-product')->name('restore');
        });

        Route::prefix('attributes')->name('attributes.')->group(function (){
            Route::get('/', [AttributeController::class, 'index'])->middleware('permission:create-attribute,update-attribute,delete-attribute')->name('index');

            Route::middleware(['permission:create-attribute'])->group(function () {
                Route::get('/create', [AttributeController::class, 'create'])->name('create');
                Route::post('/', [AttributeController::class, 'store'])->name('store');
            });

            Route::middleware(['permission:update-attribute'])->group(function () {
                Route::get('/{attribute}/edit', [AttributeController::class, 'edit'])->name('edit');
                Route::put('/{attribute}', [AttributeController::class, 'update'])->name('update');
            });
                Route::delete('/{attribute}', [AttributeController::class, 'destroy'])->middleware('permission:delete-attribute')->name('destroy');
        });

        Route::prefix('attribute-values')->name('attribute-values.')->group(function (){

            Route::post('/', [AttributeValueController::class, 'store'])->middleware('permission:create-attribute')->name('store');
            Route::put('/{attributeValue}', [AttributeValueController::class, 'update'])->middleware('permission:update-attribute')->name('update');
            Route::delete('/{attributeValue}', [AttributeValueController::class, 'destroy'])->middleware('permission:delete-attribute')->name('destroy');
        });

    });
});





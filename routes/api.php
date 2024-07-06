<?php

use App\Http\Controllers\Api\AdminArchiveController;
use App\Http\Controllers\Api\AdminRoleController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\UserArchivesController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PageContentController;
use App\Http\Controllers\Api\AttributeTagsController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductArchivesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::get('all-admins', [AdminController::class, 'index']);


Route::get('/locale/{lang}', [LocaleController::class, 'setlocale']);
Route::prefix('site')->group(function () {
    Route::get('menu-categories', [CategoryController::class,  "menuCategory"])->name('menu-categories');
});


Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class, 'login'])->name('admin.login');

    // Admins route >>> for deleted , add and update admins
    Route::controller(AdminController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-admins', 'index')->name('all-admins');
        Route::get('refresh-token', 'index')->name('refresh-token');
        Route::post('store-admin', 'store')->name('store-admin');
        Route::post('update-admin/{id}', 'update')->name('update-admin');
        Route::get('changestatus-admin/{id}', 'changestatus')->name('changestatus-admin');
        Route::get('softdelete-admin/{id}', 'destroy')->name('softdelete-admin');
        Route::post('archive-admin-array', 'softDeleteArray')->name('archive-admin-array');
    });

    Route::controller(UserController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-users', 'index')->name('all-users');
        Route::post('store-user', 'store')->name('store-user');
        Route::post('update-user/{id}', 'update')->name('update-user');
        Route::get('changestatus-user/{id}', 'changestatus')->name('changestatus-user');
        Route::get('softdelete-user/{id}', 'destroy')->name('softdelete-user'); // Corrected to 'softDelete'
        Route::post('archive-user-array', 'softDeleteArray')->name('archive-user-array');
    });

    Route::controller(AdminArchiveController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('admin-archives', 'index')->name('admin-archives');
        Route::get('reset-admin/{id}', 'update')->name('reset-admin');
        Route::get('reset-admins/{id}', 'updatearray')->name('reset-admins');
        Route::get('delete-admin/{id}', 'destroy')->name('delete-admin');
        Route::post('delete-admins', 'destroyarray')->name('delete-admins');
    });

    Route::controller(UserArchivesController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('user-archives', 'index')->name('user-archives');
        Route::get('reset-user/{id}', 'update')->name('reset-user');
        Route::get('reset-users/{id}', 'updatearray')->name('reset-users');
        Route::get('delete-user/{id}', 'destroy')->name('delete-user');
        Route::post('delete-users', 'destroyarray')->name('delete-users');
    });

    Route::controller(AdminRoleController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-roles', 'index')->name('all-roles');
        Route::post('store-role', 'store')->name('store-role');
        Route::get('show-role/{id}', 'show')->name('show-role');
        Route::post('show-role-permissions/{id}', 'permission')->name('show-role-permissions');
        Route::post('update-role/{id}', 'update')->name('update-role');
        Route::post('delete-role-array', 'softDeleteArray')->name('delete-role-array');
        Route::get('changestatus-role/{id}', 'changestatus')->name('changestatus-role');
        Route::get('delete-role/{id}', 'destroy')->name('delete-role');
    });

    Route::controller(CategoryController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-categories', 'index')->name('all-categories');
        Route::get('all-categories-list', 'allcategories')->name('all-categories-list');
        Route::post('store-category', 'store')->name('store-category');
        Route::post('update-category/{id}', 'update')->name('update-category');
        Route::get('delete-category/{id}', 'destroy')->name('delete-category');
        Route::get('changestatus-category/{id}', 'changestatus')->name('changestatus-category');
        Route::get('changeview-category/{id}', 'changeview')->name('changeview-category');
        Route::post('delete-categories', 'destroyarray')->name('delete-categories');
    });

    Route::controller(BrandController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-brands', 'index')->name('all-brands');
        Route::post('store-brand', 'store')->name('store-brand');
        Route::post('update-brand/{id}', 'update')->name('update-brand');
        Route::get('changestatus-brand/{id}', 'changestatus')->name('changestatus-brand');
        Route::get('delete-brand/{id}', 'destroy')->name('delete-brand');
        Route::post('delete-brands', 'destroyarray')->name('delete-brands');
    });

    Route::controller(AttributeController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-attributes', 'index')->name('all-attributes');
        Route::post('store-attribute', 'store')->name('store-attribute');
        Route::post('update-attribute/{id}', 'update')->name('update-attribute');
        Route::get('changestatus-attribute/{id}', 'changestatus')->name('changestatus-attribute');
        Route::get('delete-attribute/{id}', 'destroy')->name('delete-attribute');
        Route::post('delete-attributes', 'destroyarray')->name('delete-attributes');
    });

    Route::controller(AttributeTagsController::class)->middleware('auth:sanctum')->group(function () {
        // Route::get('all-tags', 'index')->name('all-tags');
        Route::get('all-tags-id/{id}', 'alltags')->name('all-tags-id');
        Route::post('store-tag', 'store')->name('store-tag');
        Route::post('update-tag/{id}', 'update')->name('update-tag');
        Route::get('changestatus-tag/{id}', 'changestatus')->name('changestatus-tag');
        Route::get('delete-tag/{id}', 'destroy')->name('delete-tag');
        Route::post('delete-tags', 'destroyarray')->name('delete-tags');
    });

    Route::controller(ProductController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-products', 'index')->name('all-products');
        // Route::get('all-products-id/{id}', 'alltags')->name('all-products-id');
        Route::post('store-product', 'store')->name('store-product');
        // Route::post('update-product/{id}', 'update')->name('update-product');
        Route::get('changestatus-product/{id}', 'changestatus')->name('changestatus-product');
        Route::get('delete-product/{id}', 'destroy')->name('delete-product');
        Route::post('delete-products', 'softDeleteArray')->name('delete-products');
    });

    Route::controller(ProductArchivesController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-archives-products', 'index')->name('all-archives-products');
        // Route::get('all-products-id/{id}', 'alltags')->name('all-products-id');
        // Route::post('store-product', 'store')->name('store-product');
        // Route::post('update-product/{id}', 'update')->name('update-product');
        // Route::get('changestatus-product/{id}', 'changestatus')->name('changestatus-product');
        // Route::get('delete-product/{id}', 'destroy')->name('delete-product');
        // Route::post('delete-products', 'softDeleteArray')->name('delete-products');
    });

    Route::controller(CountryController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-countries', 'index')->name('all-countries');
        Route::post('store-country', 'store')->name('store-country');
        Route::post('update-country/{id}', 'update')->name('update-country');
        Route::get('delete-country/{id}', 'destroy')->name('delete-country');
        Route::post('delete-countries', 'destroyarray')->name('delete-countries');
    });

    Route::controller(StateController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-states', 'index')->name('all-states');
        Route::get('all-states-id/{id}', 'allstates')->name('all-states-id');
        Route::post('store-state', 'store')->name('store-state');
        Route::post('update-state/{id}', 'update')->name('update-state');
        Route::get('delete-state/{id}', 'destroy')->name('delete-state');
        Route::post('delete-states', 'destroyarray')->name('delete-states');
    });

    Route::controller(CityController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-cities', 'index')->name('all-cities');
        Route::get('all-cities-id/{id}', 'allcities')->name('all-cities-id');
        Route::post('store-city', 'store')->name('store-city');
        Route::post('update-city/{id}', 'update')->name('update-city');
        Route::get('delete-city/{id}', 'destroy')->name('delete-city');
        Route::post('delete-cities', 'destroyarray')->name('delete-cities');
    });

    Route::controller(PageContentController::class)->middleware('auth:sanctum')->group(function () {
        Route::post('pageContent/store', 'store')->name('pageContent.store');
        Route::post('pageContent/show', 'show')->name('pageContent.show');
        Route::post('pageContent/showbytitle', 'showByTitle')->name('pageContent.showbytitle');
        Route::post('pageContent/update', 'update')->name('pageContent.update');
        Route::delete('pageContent/delete/{id}', 'destroy')->name('pageContent.destroy');
    });

    Route::controller(ContactController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-contacts', 'index')->name('all-contacts');
        Route::post('store-contact', 'store')->name('store-contact');
        Route::post('update-contact/{id}', 'update')->name('update-contact');
        Route::post('show-contact', 'show')->name('show-contact');
        Route::get('delete-contact/{id}', 'destroy')->name('delete-contact');
    });
});

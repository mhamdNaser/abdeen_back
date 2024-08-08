<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductTagsController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\OrderProductController;
use App\Http\Controllers\Api\PageContentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::get('all-admins', [AdminController::class, 'index']);

Route::get('/locale/{lang}', [LocaleController::class, 'setlocale']);

Route::prefix('site')->group(function () {
    Route::get('menu-categories', [CategoryController::class, "menuCategory"])->name('menu-categories');
    Route::get('menu-Brand', [BrandController::class, "menuBrand"])->name('menu-Brand');
    Route::get('active-languages', [LanguageController::class, 'active'])->name('active-languages');
    Route::get('all-countries', [CountryController::class, 'index'])->name('all-countries');
    Route::get('all-states', [StateController::class, 'index'])->name('all-states');
    Route::get('all-cities', [CityController::class, 'index'])->name('all-cities');
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::post('singup', [UserController::class, 'store'])->name('singup');
    Route::get('socialmedia', [SocialMediaController::class, 'index'])->name('socialmedia');
    Route::get('show-oreders/{id}', [OrderController::class, 'ordersUser'])->name('show-oreders');
    Route::get('show-brand/{name}', [BrandController::class, 'showbyname'])->name('show-brand');
    Route::get('show-category/{name}', [CategoryController::class, 'showbyname'])->name('show-category');
    Route::get('get-productTag/{id}', [ProductTagsController::class, 'getTagByProductId'])->name('get-productTag');
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');


    Route::controller(DeliveryController::class)->group(function () {
        Route::get('all-deliveries', 'index')->name('all-deliveries');
    });

    Route::controller(PageContentController::class)->group(function () {
        Route::post('pageContent/showbytitle', 'showByTitle')->name('pageContent.showbytitle');
    });

    Route::controller(TaxController::class)->group(function () {
        Route::get('tax', 'index')->name('tax');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('topbuy-products', 'topSellingProducts')->name('topbuy-products');
        Route::get('category-products/{id}', 'categoryProducts')->name('category-products');
        Route::get('brand-products/{id}', 'brandProducts')->name('brand-products');
        Route::get('all-products', 'allproducts')->name('all-products');
        Route::get('topDiscounted-products', 'topDiscountedProducts')->name('topDiscounted-products');
        Route::get('show-product/{id}', 'cartProduct')->name('show-product');
        Route::get('show-product-images/{productId}', 'showImages')->name('show-product-images');
    });

    Route::controller(PaymentMethodController::class)->group(function () {
        Route::get('/paymentMethod', 'index')->name('paymentMethod');
        Route::get('/getClinentIdPaypal', 'getClinentIdPaypal')->name('getClinentIdPaypal');
    });

    Route::controller(PaymentController::class)->middleware('auth:sanctum')->group(function () {
        Route::post('create-payment', 'createPayPalOrder')->name('create-payment');
        Route::post('cashe-payment', 'casheOnDelivery')->name('cashe-payment');
        Route::post('capture-payment', 'capturePayment')->name('capture-payment');
        Route::get('getClientIdPaypal', 'getClientIdPaypal')->name('getClientIdPaypal');
    });

    Route::controller(OrderController::class)->middleware('auth:sanctum')->group(function () {
        Route::post('store-order', 'store')->name('store-order');
        Route::get('show-order/{id}', 'show')->name('show-order');
        Route::get('delete-oreder/{id}', 'destroy')->name('delete-oreder');
    });

    Route::controller(OrderProductController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('delete-orderProduct/{id}', 'destroy')->name('delete-orderProduct');
    });
});

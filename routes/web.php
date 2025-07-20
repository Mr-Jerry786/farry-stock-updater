<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryUploadController;
use App\Http\Controllers\PromotionProductController; // ✅ NEW

/*
|--------------------------------------------------------------------------
| Web Routes - Amazon Stock Updater Application
|--------------------------------------------------------------------------
*/

// === Dashboard (Default View) ===
Route::get('/', [ProductController::class, 'index'])->name('dashboard');

// === General Product Upload Routes (Amazon Stock Updater) ===
Route::prefix('upload')->group(function () {
    Route::get('/', [FileUploadController::class, 'showForm'])->name('upload.form');
    Route::post('/', [FileUploadController::class, 'handleUpload'])->name('upload.handle');
});

// === Product Routes ===
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/missing', [ProductController::class, 'missing'])->name('missing');
    Route::post('/{product}/update', [ProductController::class, 'update'])->name('update');

    // Export Buttons
    Route::get('/export/in-stock', [ProductController::class, 'exportInStock'])->name('export.instock');
    Route::get('/export/out-of-stock', [ProductController::class, 'exportOutOfStock'])->name('export.outofstock');
    Route::get('/missing/export', [ProductController::class, 'exportMissing'])->name('missing.export');

    // ✅ NEW PRODUCTS TAB + EXPORT + TRANSFER
    Route::get('/new', [ProductController::class, 'newProducts'])->name('new');
    Route::post('/transfer', [ProductController::class, 'transferToInventory'])->name('transfer');
    Route::get('/new/export', [ProductController::class, 'exportNewProducts'])->name('new.export');
});

// === Amazon Inventory Routes ===
Route::prefix('inventory')->name('inventory.')->group(function () {
    // Views
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::get('/missing', [InventoryController::class, 'missing'])->name('missing');

    // Upload Form & Handler (Now handled by InventoryUploadController)
    Route::get('/upload', [InventoryUploadController::class, 'showForm'])->name('upload.form');
    Route::post('/upload', [InventoryUploadController::class, 'handleUpload'])->name('upload.handle');

    // Export Inventory
    Route::get('/export/in-stock', [InventoryController::class, 'exportInStock'])->name('export.instock');
    Route::get('/export/out-of-stock', [InventoryController::class, 'exportOutOfStock'])->name('export.outofstock');
    Route::get('/missing/export', [InventoryController::class, 'exportMissing'])->name('export.missing');
});

// === ✅ Promotion Products Route ===
Route::get('/promotions', [PromotionProductController::class, 'index'])->name('promotions.index');

<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;

Route::pattern('id', '[0-9]+');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister']);

Route::get('/', [WelcomeController::class, 'index']);

Route::group(['prefix' => 'guest'], function () {
    Route::post('/banner', [WelcomeController::class, 'banner']);
    Route::post('/product', [WelcomeController::class, 'product']);
});

Route::middleware(['auth'])->group(function () {
    Route::middleware(['authorize:CUS'])->group(function () {
        Route::group(['prefix' => 'public'], function () {
            Route::put('/{id}/update_profile', [CustomerController::class, 'update_ajax']);
            Route::post('/add_cart', [WelcomeController::class, 'add_cart']);
            Route::post('/update_cart', [WelcomeController::class, 'update_cart']);
            Route::post('/checkout', [WelcomeController::class, 'checkout']);
            Route::post('/history', [WelcomeController::class, 'history']);
        });
    });

    Route::middleware(['authorize:ADM'])->group(function () {
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::post('/getCardData', [DashboardController::class, 'getCardData']);
            Route::post('/getChartData', [DashboardController::class, 'getChartData']);
        });
        // Route::group(['prefix' => 'user'], function () {
        //     Route::get('/', [UserController::class, 'index']);
        //     Route::post('/list', [UserController::class, 'list']);
        //     Route::get('/create_ajax', [UserController::class, 'create_ajax']);
        //     Route::post('/ajax', [UserController::class, 'store_ajax']);
        //     Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
        //     Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);
        //     Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);
        //     Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
        //     Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);
        //     Route::get('/import', [UserController::class, 'import']);
        //     Route::post('/import_ajax', [UserController::class, 'import_ajax']);
        //     Route::get('/export_excel', [UserController::class, 'export_excel']);
        //     Route::get('/export_pdf', [UserController::class, 'export_pdf']);
        // });

        Route::group(['prefix' => 'admin'], function () {
            Route::get('/', [AdminController::class, 'index']);
            Route::post('/list', [AdminController::class, 'list']);
            Route::get('/create_ajax', [AdminController::class, 'create_ajax']);
            Route::post('/ajax', [AdminController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
            Route::get('/{id}/edit_ajax', [AdminController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [AdminController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [AdminController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [AdminController::class, 'delete_ajax']);
            Route::get('/import', [AdminController::class, 'import']);
            Route::post('/import_ajax', [AdminController::class, 'import_ajax']);
            Route::get('/export_excel', [AdminController::class, 'export_excel']);
            Route::get('/export_pdf', [AdminController::class, 'export_pdf']);
        });

        Route::group(['prefix' => 'customer'], function () {
            Route::get('/', [CustomerController::class, 'index']);
            Route::post('/list', [CustomerController::class, 'list']);
            Route::get('/create_ajax', [CustomerController::class, 'create_ajax']);
            Route::post('/ajax', [CustomerController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
            Route::get('/{id}/edit_ajax', [CustomerController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [CustomerController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [CustomerController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [CustomerController::class, 'delete_ajax']);
            Route::get('/import', [CustomerController::class, 'import']);
            Route::post('/import_ajax', [CustomerController::class, 'import_ajax']);
            Route::get('/export_excel', [CustomerController::class, 'export_excel']);
            Route::get('/export_pdf', [CustomerController::class, 'export_pdf']);
        });

        Route::group(['prefix' => 'kategori'], function () {
            Route::get('/', [KategoriController::class, 'index']);
            Route::post('/list', [KategoriController::class, 'list']);
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);
            Route::post('/ajax', [KategoriController::class, 'store_ajax']);
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);
            Route::get('/import', [KategoriController::class, 'import']);
            Route::post('/import_ajax', [KategoriController::class, 'import_ajax']);
            Route::get('/export_excel', [KategoriController::class, 'export_excel']);
            Route::get('/export_pdf', [KategoriController::class, 'export_pdf']);
        });

        // Route::group(['prefix' => 'level'], function () {
        //     Route::get('/', [LevelController::class, 'index']);
        //     Route::post('/list', [LevelController::class, 'list']);
        //     Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
        //     Route::post('/ajax', [LevelController::class, 'store_ajax']);
        //     Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
        //     Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
        //     Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
        //     Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
        //     Route::get('/import', [LevelController::class, 'import']);
        //     Route::post('/import_ajax', [LevelController::class, 'import_ajax']);
        //     Route::get('/export_excel', [LevelController::class, 'export_excel']);
        //     Route::get('/export_pdf', [LevelController::class, 'export_pdf']);
        // });

        Route::group(['prefix' => 'supplier'], function () {
            Route::get('/', [SupplierController::class, 'index']);
            Route::post('/list', [SupplierController::class, 'list']);
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);
            Route::post('/ajax', [SupplierController::class, 'store_ajax']);
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);
            Route::get('/import', [SupplierController::class, 'import']);
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax']);
            Route::get('/export_excel', [SupplierController::class, 'export_excel']);
            Route::get('/export_pdf', [SupplierController::class, 'export_pdf']);
        });

        Route::group(['prefix' => 'produk'], function () {
            Route::get('/', [BarangController::class, 'index']);
            Route::post('/list', [BarangController::class, 'list']);
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
            Route::post('/ajax', [BarangController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
            Route::get('/import', [BarangController::class, 'import']);
            Route::post('/import_ajax', [BarangController::class, 'import_ajax']);
            Route::get('/export_excel', [BarangController::class, 'export_excel']);
            Route::get('/export_pdf', [BarangController::class, 'export_pdf']);
        });

        Route::group(['prefix' => 'stok'], function () {
            Route::get('/', [StokController::class, 'index']);
            Route::post('/list', [StokController::class, 'list']);
            Route::get('/create_ajax', [StokController::class, 'create_ajax']);
            Route::post('/ajax', [StokController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax']);
            Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax']);
            Route::get('/import', [StokController::class, 'import']);
            Route::post('/import_ajax', [StokController::class, 'import_ajax']);
            Route::get('/export_excel', [StokController::class, 'export_excel']);
            Route::get('/export_pdf', [StokController::class, 'export_pdf']);
            Route::get('/rekap_per_bulan', [StokController::class, 'rekap_per_bulan']);

        });

        Route::group(['prefix' => 'penjualan'], function () {
            Route::get('/', [PenjualanController::class, 'index']);
            Route::post('/list', [PenjualanController::class, 'list']);
            Route::get('/create_ajax', [PenjualanController::class, 'create_ajax']);
            Route::post('/ajax', [PenjualanController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [PenjualanController::class, 'show_ajax']);
            Route::get('/{id}/edit_ajax', [PenjualanController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [PenjualanController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']);
            Route::get('/export_excel', [PenjualanController::class, 'export_excel']);
            Route::get('/export_pdf', [PenjualanController::class, 'export_pdf']);

            Route::post('/{id}/update_status', [PesananController::class, 'update_status']);
            Route::get('/{id}/print_struk', [PesananController::class, 'print_struk']);
        });

        Route::group(['prefix' => 'pesanan'], function () {
            Route::get('/', [PesananController::class, 'index']);
            Route::post('/list', [PesananController::class, 'list']);
            Route::get('/create_ajax', [PesananController::class, 'create_ajax']);
            Route::post('/ajax', [PesananController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [PesananController::class, 'show_ajax']);
            Route::get('/{id}/edit_ajax', [PesananController::class, 'edit_ajax']);
            Route::get('/{id}/edit_ajax', [PesananController::class, 'edit_ajax']);
            Route::put('/{id}/update_ajax', [PesananController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [PesananController::class, 'confirm_ajax']);
            Route::delete('/{id}/delete_ajax', [PesananController::class, 'delete_ajax']);
            Route::get('/export_excel', [PesananController::class, 'export_excel']);
            Route::get('/export_pdf', [PesananController::class, 'export_pdf']);

            Route::post('/{id}/update_status', [PesananController::class, 'update_status']);
            Route::get('/{id}/print_struk', [PesananController::class, 'print_struk']);
        });
    });

});

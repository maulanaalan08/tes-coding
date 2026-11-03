<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategorisController;
use App\Http\Controllers\MasterItemsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home');

// Custom Master Items Routes (specific routes before resource)
Route::get('/master-items/search', [MasterItemsController::class, 'search'])->name('master-items.search');
Route::get('/master-items/export-excel', [MasterItemsController::class, 'exportExcel'])->name('master-items.export');
Route::get('/master-items/view/{kode}', [MasterItemsController::class, 'show'])->name('master-items.show');
Route::get('/master-items/update-random-data', [MasterItemsController::class, 'updateRandomData'])->name('master-items.random-data');

// Master Items Resource Routes
Route::resource('master-items', MasterItemsController::class)
    ->except(['show'])
    ->names([
        'index' => 'master-items.index',
        'create' => 'master-items.create',
        'store' => 'master-items.store',
        'edit' => 'master-items.edit',
        'update' => 'master-items.update',
        'destroy' => 'master-items.destroy',
    ]);

// Custom Kategori Routes (specific routes before resource)
Route::get('/kategoris/search', [KategorisController::class, 'search'])->name('kategoris.search');
Route::get('/kategoris/export-pdf/{id}', [KategorisController::class, 'exportPdf'])->name('kategoris.export-pdf');
Route::get('/kategoris/view/{kode}', [KategorisController::class, 'show'])->name('kategoris.show');

// Kategori Resource Routes
Route::resource('kategoris', KategorisController::class)
    ->except(['show'])
    ->names([
        'index' => 'kategoris.index',
        'create' => 'kategoris.create',
        'store' => 'kategoris.store',
        'edit' => 'kategoris.edit',
        'update' => 'kategoris.update',
        'destroy' => 'kategoris.destroy',
    ]);

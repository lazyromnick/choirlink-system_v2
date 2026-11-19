<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\ViewMangAawitAccount;
use App\Filament\Pages\ViewNagsasanayAccount;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/mang-aawit-account/{record}/view', ViewMangAawitAccount::class)
    ->middleware(['web', 'auth'])
    ->name('filament.admin.pages.view-mang-aawit-account')
    ->withTrashed();

Route::get('/admin/nagsasanay-account/{record}/view', ViewNagsasanayAccount::class)
    ->middleware(['web', 'auth'])
    ->name('filament.admin.pages.view-nagsasanay-account')
    ->withTrashed();
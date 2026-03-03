<?php
use Illuminate\Support\Facades\Route;
use Inertia\inertia;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MoneyExchangeController;

// Route::get('/', function () {
//     return inertia('Home');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/moneyexchange', [MoneyExchangeController::class, 'openForm'])->name('open.moneyexchange.form');
Route::post('/calculateMoney', [MoneyExchangeController::class, 'SaveCalculatedMoney']);
Route::get('/invoices/{id}', [MoneyExchangeController::class, 'showInvoices'])->name('invoices.show');

// Route::inertia('/', 'Home');
// Route::inertia('/moneyexchange', 'ExchangeMoneyForm');
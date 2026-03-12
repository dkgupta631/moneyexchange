<?php
use Illuminate\Support\Facades\Route;
use Inertia\inertia;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MoneyExchangeController;

// Route::get('/', function () {
//     return inertia('Home');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/ExchangeRate', [HomeController::class, 'ShowExchangeRate'])->name('showExchangeRate');
Route::get('/moneyexchange', [MoneyExchangeController::class, 'openForm'])->name('open.moneyexchange.form');
// Route::post('/calculateMoney', [MoneyExchangeController::class, 'SaveCalculatedMoney']);
Route::get('/moneyexchange-invoices/{invoice_number}', [MoneyExchangeController::class, 'showMoneyExchangeInvoices'])->name('MoneyExchangeInvoices.show');

Route::post('/get-exchange-rate', [MoneyExchangeController::class, 'getRate']);
Route::post('/calculateMoney', [MoneyExchangeController::class, 'SaveCalculatedMoney']);


Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en','th-TH','km'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return back();
});
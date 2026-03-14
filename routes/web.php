<?php
use Illuminate\Support\Facades\Route;
use Inertia\inertia;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MoneyExchangeController;
use App\Http\Controllers\Web\MoneyTransferController;

// Route::get('/', function () {
//     return inertia('Home');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/ExchangeRate', [HomeController::class, 'ShowExchangeRate'])->name('showExchangeRate');
Route::get('/moneyexchange', [MoneyExchangeController::class, 'openForm'])->name('open.moneyexchange.form');
Route::get('/moneyexchange-invoices/{invoice_number}', [MoneyExchangeController::class, 'showMoneyExchangeInvoices'])->name('MoneyExchangeInvoices.show');
Route::post('/get-exchange-rate', [MoneyExchangeController::class, 'getRate']);
Route::post('/calculateMoney', [MoneyExchangeController::class, 'SaveCalculatedMoney']);


Route::get('/money-transfer-in', [MoneyTransferController::class, 'moneyTransferINForm'])->name('moneytransfer.in.form');
Route::post('/money-transfer-in/store', [MoneyTransferController::class, 'storeTransferIN']);

Route::get('/money-transfer-in', [MoneyTransferController::class, 'moneyTransferINForm'])->name('moneytransfer.in.form');
Route::get('/money-transfer/invoice/{encodedInvoice}', [MoneyTransferController::class, 'showTransferINInvoice'])->name('money.transfer.in.invoice');


Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en','th-TH','km'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return back();
});
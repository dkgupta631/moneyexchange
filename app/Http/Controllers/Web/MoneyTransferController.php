<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\MoneyTransferCharge;
use App\Models\MoneyTransferInvoice;

class MoneyTransferController extends Controller
{
    public function moneyTransferINForm()
    {
        $gettransferchanges = MoneyTransferCharge::select('*')->orderBy('id', 'asc')->get();
        //  dd($gettransferchanges);
        return Inertia::render('MoneyTransferINForm', [
            // 'records' => $records,
        ]);

    }
}

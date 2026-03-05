<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\ExchangeRate;

class HomeController extends Controller
{
    public function index()
    {
        $Leftrecords = ExchangeRate::select(['id', 'from_currency', 'to_currency', 'normal_buy_rate', 'normal_sell_rate'])->orderBy('ordering', 'asc')->get();
        $Rightrecords = ExchangeRate::select([
                                                'id',
                                                'from_currency',
                                                'to_currency',
                                                'standard_buy_rate',
                                                'standard_sell_rate'
                                            ])
                                            ->whereNotNull('standard_buy_rate')
                                            ->whereNotNull('standard_sell_rate')
                                            ->orderBy('ordering', 'asc')
                                            ->get();
       
        return Inertia::render('Home', [
            'Leftrecords' => $Leftrecords,
            'Rightrecords' => $Rightrecords,
        ]);



    }
}

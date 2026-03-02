<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\ExchangeRate;

class MoneyExchangeController extends Controller
{
    public function openForm()
    {
        $records = ExchangeRate::select('*')->orderBy('id', 'asc')->get();
        //  dd($recommendedstors);
     
       
        return Inertia::render('ExchangeMoneyForm', [
            'records' => $records,
        ]);

    }

    public function SaveCalculatedMoney(Request $request)
    {
        $request->validate([
            'Customer_name' => ['required'],
            'phone' => ['required'],
            'enter_amount' => ['required'],
        ]);
    //    dd($request);
        


       return redirect('/invoices'.'/'.$request->Customer_name)->with('greet' , 'Invoice Generated Successfully!');

    }

    public function showInvoices($id)
    {
    //    dd($id);
       $records = ExchangeRate::where('id', $id)->get();
      
       
        return Inertia::render('ShowInvoices', [
            'records' => $records,
        ]);
        


     

    }


}

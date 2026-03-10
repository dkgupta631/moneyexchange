<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\ExchangeRate;
use App\Models\Invoice;
use Illuminate\Support\Str;

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
            // 'customer_name' => ['required'],
            // 'phone' => ['required'],
            'enter_amount' => ['required'],
        ]);
       dd($request);

       Invoice::create([
        'invoice_number' => 'INV'.time(),
        'customer_name' => $request->customer_name,
        'phone' => $request->phone,

        'exchange_rate_id' => $request->exchange_rate_id,
        'pair' => $request->from_currency.'-'.$request->to_currency,
        'exchange_type' => $request->exchange_type,
        'exchange_rate' => $request->exchange_rate,

        'where_to_send' => $request->where_to_send,
        'entered_amount' => $request->enter_amount,
        'subtotal' => $request->subtotal,
        'service_fee' => $request->service_fee,
        'final_amount' => $request->final_amount
    ]);

    // return back()->with('success','Invoice Created Successfully');

       return redirect('/invoices'.'/'.$request->Customer_name)->with('greet' , 'Invoice Generated Successfully!');

    }


   
    public function getRate(Request $request)
    {
        //  dd($request->all());
        // return $request;
        $rate = ExchangeRate::where('from_currency',$request->from_currency)
            ->where('to_currency',$request->to_currency)
            ->first();

        if($rate){
                if($request->exchange_type == "Normal"){
                    $exchangeRate = $rate->normal_rate;
                }else{
                    $exchangeRate = $rate->standard_rate;
                }
                if($rate->buy_or_sell == 'sell'){
                     $subtotal  = $request->enter_amount*$exchangeRate;
                }else{
                    $subtotal  = $request->enter_amount/$exchangeRate;
                }
            
         } 


        return response()->json([
            'id'=>$rate->id,
            'from_currency'=>$rate->from_currency,
            'to_currency'=>$rate->to_currency,
            'buy_or_sell'=>$rate->buy_or_sell,
            'exchange_rate'=>$exchangeRate,
            'subtotal'=>$subtotal,
            'service_fee'=> '0.1',
            'total'=>$subtotal,
            
        ]);
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

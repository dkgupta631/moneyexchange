<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\MoneyTransferCharge;
use App\Models\MoneyTransferInvoice;
use Illuminate\Support\Facades\Auth;

class MoneyTransferController extends Controller
{
    /**
     * Show the Transfer-IN form.
     */
    public function moneyTransferINForm()
    {
        $gettransferchanges = MoneyTransferCharge::select('trf_fee_in_persentage')->where('transfer_type', 'Transfer-IN')->first();
        // dd($gettransferchanges);
        return Inertia::render('MoneyTransferINForm', [
            'gettransferchanges' => $gettransferchanges,
        ]);
    }

    public function moneyTransferOUTForm()
    {
        $gettransferchanges = MoneyTransferCharge::select('trf_fee_in_persentage')->where('transfer_type', 'Transfer-OUT')->first();
        return Inertia::render('MoneyTransferOUTForm', [
            'gettransferchanges' => $gettransferchanges,
        ]);
    }

    /**
     * Store a new Transfer-IN invoice.
     */
    public function storeTransferIN(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'customer_name'       => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:13',
            'bank_name'           => 'required|string|max:255',
            'acc_name'            => 'required|string|max:255',
            'acc_number'          => 'required|string|max:255',
            'entered_amount'      => [
                'required',
                'numeric',
                'min:500',
                'max:100000',
            ],
        ], [
            'bank_name.required'      => 'Please select a bank.',
            'acc_name.required'       => 'Account name is required.',
            'acc_number.required'     => 'Account number is required.',
            'entered_amount.required' => 'Amount is required.',
            'entered_amount.numeric'  => 'Amount must be a valid number.',
            'entered_amount.min'      => 'Minimum transfer amount is ฿500 THB.',
            'entered_amount.max'      => 'Maximum transfer amount is ฿100,000 THB.',
        ]);

        // Re-calculate server-side (never trust client values)
        // $charge        = MoneyTransferCharge::where('transfer_type', 'Transfer-IN')->first();
        // $feePercent    = $charge ? (float) $charge->trf_fee_in_persentage : 0;
        $enteredAmount = (float) $validated['entered_amount'];
        // $trfFee        = round($enteredAmount * $feePercent / 100, 2);
        // $netAmount     = round($enteredAmount - $trfFee, 2);

        // Generate invoice number
        $now           = now()->setTimezone('Asia/Bangkok');
        $invoiceNumber = '#TI' . $now->format('dmyHis');

        // Get logged-in user ID
        $user = Auth::user();
        $authId = $user ? $user->id : null;
        $invoice_url = URL('/money-transfer/invoice' . '/' . base64_encode($invoiceNumber));

        // Persist
        MoneyTransferInvoice::create([
            'invoice_number'        => $invoiceNumber,
            'customer_name'         => $validated['customer_name'],
            'phone'                 => $validated['phone'] ?? null,
            'transfer_type'         => 'Transfer-IN',
            'bank_name'             => $validated['bank_name'],
            'acc_name'              => $validated['acc_name'],
            'acc_number'            => $validated['acc_number'],
            'currency'              => $request->currency ?? 'THB',
            'entered_amount'        => $enteredAmount,
            'trf_fee_in_persentage' => $request->trf_fee_in_persentage,
            'trf_fee'               => $request->trf_fee,
            'net_amount'            => $request->net_amount,
            'invoice_url'         => $invoice_url,
            // 'invoice_slip'         => '',
            'createdBy'         => $authId,
            'created_at'         => $now,
        ]);

        $msg = __('message.Invoice Generated Successfully!');
        return redirect('/money-transfer/invoice' . '/' . base64_encode($invoiceNumber))->with('greet', $msg);
    }

    /**
     * Store a new Transfer-IN invoice.
     */
    public function storeTransferOUT(Request $request)
    {
        // Validation
        // dd($request->all());
        $validated = $request->validate([
            'customer_name'       => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:13',
            'bank_name'           => 'required|string|max:255',
            'acc_name'            => 'required|string|max:255',
            'acc_number'          => 'required|string|max:255',
            'entered_amount'      => [
                'required',
                'numeric',
                'min:500',
                'max:100000',
            ],
        ], [
            'bank_name.required'      => 'Please select a bank.',
            'acc_name.required'       => 'Account name is required.',
            'acc_number.required'     => 'Account number is required.',
            'entered_amount.required' => 'Amount is required.',
            'entered_amount.numeric'  => 'Amount must be a valid number.',
            'entered_amount.min'      => 'Minimum transfer amount is ฿500 THB.',
            'entered_amount.max'      => 'Maximum transfer amount is ฿100,000 THB.',
        ]);

        // Re-calculate server-side (never trust client values)
        // $charge        = MoneyTransferCharge::where('transfer_type', 'Transfer-OUT')->first();
        // $feePercent    = $charge ? (float) $charge->trf_fee_in_persentage : 0;
        $enteredAmount = (float) $validated['entered_amount'];
        // $trfFee        = round($enteredAmount * $feePercent / 100, 2);
        // $netAmount     = round($enteredAmount - $trfFee, 2);

        // Generate invoice number
        $now           = now()->setTimezone('Asia/Bangkok');
        $invoiceNumber = '#TO' . $now->format('dmyHis');

         // Get logged-in user ID
        $user = Auth::user();
        $authId = $user ? $user->id : null;
        $invoice_url = URL('/money-transfer/invoice' . '/' . base64_encode($invoiceNumber));

        // Persist
        MoneyTransferInvoice::create([
            'invoice_number'        => $invoiceNumber,
            'customer_name'         => $validated['customer_name'],
            'phone'                 => $validated['phone'] ?? null,
            'transfer_type'         => 'Transfer-OUT',
            'bank_name'             => $validated['bank_name'],
            'acc_name'              => $validated['acc_name'],
            'acc_number'            => $validated['acc_number'],
            'currency'              => $request->currency ?? 'THB',
            'entered_amount'        => $enteredAmount,
            'trf_fee_in_persentage' => $request->trf_fee_in_persentage,
            'trf_fee'               => $request->trf_fee,
            'net_amount'            => $request->net_amount,
            'invoice_url'         => $invoice_url,
            // 'invoice_slip'         => '',
            'createdBy'         => $authId,
            'created_at'         => $now,
        ]);

        $msg = __('message.Invoice Generated Successfully!');
        return redirect('/money-transfer/invoice' . '/' . base64_encode($invoiceNumber))->with('greet', $msg);
    }

    public function showTransferINInvoice(string $encodedInvoice)
    {
        $invoiceNumber = base64_decode($encodedInvoice);
 
        $invoice = MoneyTransferInvoice::where('invoice_number', $invoiceNumber)->firstOrFail();
 
        return Inertia::render('MoneyTransferInvoice', [
            'invoice' => $invoice,
            'appUrl'  => config('app.url'),
        ]);
    }

    


}
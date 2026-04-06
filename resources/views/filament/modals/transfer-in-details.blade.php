{{-- resources/views/filament/modals/transfer-in-details.blade.php --}}
<div class="transfer-in-modal">

    {{-- Header Badge Row --}}
    <div class="flex items-center justify-between mb-5 px-1">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide
                {{ $record->status === 'completed' ? 'bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-500/40' : 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/40' }}">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                </svg>
                #{{ $record->invoice_number }}
            </span>

            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-widest
                {{ $record->status === 'completed' ? 'bg-emerald-500 text-white' :
                   ($record->status === 'pending_bkk_approval' ? 'bg-amber-500 text-white' :
                   ($record->status === 'accepted_bkk' ? 'bg-blue-500 text-white' : 'bg-red-500 text-white')) }}">
                {{ match($record->status) {
                    'completed'            => __('message.completed'),
                    'pending_bkk_approval' => __('message.pending'),
                    'accepted_bkk'         => __('message.accepted'),
                    'Rejected'             => __('message.rejected'),
                    'cancelled'            => __('message.cancelled'),
                    default                => $record->status,
                } }}
            </span>
        </div>

        <div class="text-xs text-gray-400">
            {{ __('message.received_at') }}: {{ $record->created_at?->format('H:i A') }}
        </div>
    </div>

    {{-- Detail Cards Grid --}}
    <div class="grid grid-cols-2 gap-3">

        {{-- Invoice Number --}}
        <div class="detail-card rounded-xl p-4 bg-gray-800/60 border border-gray-700/50 backdrop-blur-sm">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 mb-1.5">
                {{ __('message.invoice_number') }}
            </p>
            <p class="text-sm font-bold text-amber-400 truncate">
                {{ $record->invoice_number ?? '—' }}
            </p>
        </div>

        {{-- Account Name --}}
        <div class="detail-card rounded-xl p-4 bg-gray-800/60 border border-gray-700/50 backdrop-blur-sm">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 mb-1.5">
                {{ __('message.account_name') }}
            </p>
            <p class="text-sm font-bold text-white truncate">
                {{ $record->acc_name ?? '—' }}
            </p>
        </div>

        {{-- Bank Name --}}
        <div class="detail-card rounded-xl p-4 bg-gray-800/60 border border-gray-700/50 backdrop-blur-sm">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 mb-1.5">
                {{ __('message.bank_name') }}
            </p>
            <p class="text-sm font-bold text-white">
                {{ $record->bank_name ?? '—' }}
            </p>
        </div>

        {{-- Account Number --}}
        <div class="detail-card rounded-xl p-4 bg-gray-800/60 border border-gray-700/50 backdrop-blur-sm">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 mb-1.5">
                {{ __('message.account_number') }}
            </p>
            <p class="text-sm font-bold text-white font-mono tracking-wider">
                {{ $record->acc_number ?? '—' }}
            </p>
        </div>

        {{-- Amount --}}
        <div class="detail-card rounded-xl p-4 bg-amber-500/10 border border-amber-500/30 backdrop-blur-sm col-span-1">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-amber-500/70 mb-1.5">
                {{ __('message.amount') }}
            </p>
            <p class="text-xl font-black text-amber-400">
                ฿ {{ number_format((float) $record->entered_amount, 2) }}
            </p>
        </div>

        {{-- Net Amount --}}
        <div class="detail-card rounded-xl p-4 bg-emerald-500/10 border border-emerald-500/30 backdrop-blur-sm col-span-1">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-emerald-500/70 mb-1.5">
                {{ __('message.net_amount') }}
            </p>
            <p class="text-xl font-black text-emerald-400">
                ฿ {{ number_format((float) $record->net_amount, 2) }}
            </p>
        </div>

        {{-- Transfer Fee --}}
        <div class="detail-card rounded-xl p-4 bg-gray-800/60 border border-gray-700/50 backdrop-blur-sm">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 mb-1.5">
                {{ __('message.transfer_fee') }}
            </p>
            <p class="text-sm font-bold text-rose-400">
                ฿ {{ number_format((float) $record->trf_fee, 2) }}
                @if($record->trf_fee_in_persentage)
                    <span class="text-xs text-gray-500 ml-1">({{ $record->trf_fee_in_persentage }}%)</span>
                @endif
            </p>
        </div>

        {{-- Customer --}}
        <div class="detail-card rounded-xl p-4 bg-gray-800/60 border border-gray-700/50 backdrop-blur-sm">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 mb-1.5">
                {{ __('message.customer_name') }}
            </p>
            <p class="text-sm font-bold text-white">
                {{ $record->customer_name ?? '—' }}
            </p>
            @if($record->phone)
                <p class="text-xs text-gray-500 mt-0.5">{{ $record->phone }}</p>
            @endif
        </div>

    </div>

    {{-- Footer links --}}
    @if($record->invoice_url || $record->transaction_slip)
    <div class="flex gap-3 mt-4 pt-4 border-t border-gray-700/50">
        @if($record->invoice_url)
        <a href="{{ $record->invoice_url }}" target="_blank"
           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold hover:bg-amber-500/20 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ __('message.view_invoice') }}
        </a>
        @endif

        @if($record->transaction_slip)
        <a href="{{ $record->transaction_slip }}" target="_blank"
           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-500/10 border border-blue-500/30 text-blue-400 text-xs font-semibold hover:bg-blue-500/20 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            {{ __('message.transaction_slip') }}
        </a>
        @endif
    </div>
    @endif

</div>

<style>
.transfer-in-modal .detail-card {
    transition: transform 0.15s ease, border-color 0.15s ease;
}
.transfer-in-modal .detail-card:hover {
    transform: translateY(-1px);
    border-color: rgba(212, 146, 10, 0.4);
}
</style>
<div
    x-data="{
        showPopup: @entangle('showPopup'),
        transfer: @entangle('latestTransfer'),
        audioCtx: null,

        playAlertSound() {
            try {
                this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const playBeep = (freq, start, duration) => {
                    const oscillator = this.audioCtx.createOscillator();
                    const gainNode   = this.audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(this.audioCtx.destination);
                    oscillator.frequency.value = freq;
                    oscillator.type = 'sine';
                    gainNode.gain.setValueAtTime(0.3, this.audioCtx.currentTime + start);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + start + duration);
                    oscillator.start(this.audioCtx.currentTime + start);
                    oscillator.stop(this.audioCtx.currentTime + start + duration);
                };
                // Triple alert tone
                playBeep(880, 0, 0.2);
                playBeep(1100, 0.25, 0.2);
                playBeep(880, 0.5, 0.3);
                playBeep(1100, 0.85, 0.5);
            } catch(e) {
                console.warn('Audio not available:', e);
            }
        },

        speakAlert(transfer) {
            if ('speechSynthesis' in window && transfer) {
                window.speechSynthesis.cancel();
                const msg = new SpeechSynthesisUtterance(
                    'Attention! New Transfer Out request received from ' +
                    (transfer.customer_name || 'customer') +
                    '. Amount ' + (transfer.currency || '') + ' ' + (transfer.amount || '') +
                    '. Please review immediately.'
                );
                msg.rate  = 0.95;
                msg.pitch = 1.0;
                msg.volume = 1.0;
                window.speechSynthesis.speak(msg);
            }
        }
    }"
    x-on:new-transfer-arrived.window="
        transfer = $event.detail.transfer;
        showPopup = true;
        playAlertSound();
        setTimeout(() => speakAlert(transfer), 800);
    "
    wire:poll.8000ms="checkNewTransfers"
>

    {{-- Pending Banner --}}
    @if($pendingCount > 0)
    <div class="mb-4 rounded-xl border-2 border-warning-400 bg-warning-50 dark:bg-warning-950 dark:border-warning-600 px-5 py-3 flex items-center gap-3 shadow-sm">
        <span class="relative flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-warning-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-warning-500"></span>
        </span>
        <span class="text-warning-800 dark:text-warning-200 font-semibold text-sm">
            {{ $pendingCount }} Transfer-OUT {{ Str::plural('request', $pendingCount) }} awaiting your approval
        </span>
        <span class="ml-auto text-warning-500 text-xs">Auto-refreshing every 8s</span>
    </div>
    @endif

    {{-- Popup Overlay --}}
    <div
        x-show="showPopup"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
        style="display:none;"
    >
        <div class="relative w-full max-w-md mx-4 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border-2 border-warning-400 overflow-hidden">

            {{-- Animated top bar --}}
            <div class="h-1.5 w-full bg-gradient-to-r from-warning-400 via-orange-400 to-red-400 animate-pulse"></div>

            {{-- Header --}}
            <div class="bg-warning-500 px-6 py-4 flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-lg leading-tight">New Transfer-OUT Request!</p>
                    <p class="text-warning-100 text-sm">Requires your immediate attention</p>
                </div>
            </div>

            {{-- Transfer Details --}}
            <div class="px-6 py-5 space-y-3" x-show="transfer">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Invoice</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" x-text="transfer?.invoice_number ?? '—'"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Customer</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" x-text="transfer?.customer_name ?? '—'"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Bank</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" x-text="transfer?.bank_name ?? '—'"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Currency</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" x-text="transfer?.currency ?? '—'"></p>
                    </div>
                    <div class="bg-warning-50 dark:bg-warning-950 rounded-lg p-3">
                        <p class="text-xs text-warning-600 dark:text-warning-400 uppercase tracking-wide mb-1">Amount</p>
                        <p class="font-bold text-warning-700 dark:text-warning-300 text-lg" x-text="transfer?.amount ?? '—'"></p>
                    </div>
                    <div class="bg-success-50 dark:bg-success-950 rounded-lg p-3">
                        <p class="text-xs text-success-600 dark:text-success-400 uppercase tracking-wide mb-1">Net Amount</p>
                        <p class="font-bold text-success-700 dark:text-success-300 text-lg" x-text="transfer?.net_amount ?? '—'"></p>
                    </div>
                </div>

                <p class="text-xs text-gray-400 text-center" x-text="'Received at: ' + (transfer?.created_at ?? '')"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="px-6 pb-6 flex gap-3">
                <button
                    x-show="transfer"
                    wire:click="acceptTransfer({{ $latestTransfer['id'] ?? 0 }})"
                    wire:loading.attr="disabled"
                    class="flex-1 bg-success-500 hover:bg-success-600 text-white font-bold py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-success-500/30 hover:scale-105 active:scale-95"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Accept
                </button>

                <button
                    x-show="transfer"
                    wire:click="rejectTransfer({{ $latestTransfer['id'] ?? 0 }})"
                    wire:loading.attr="disabled"
                    class="flex-1 bg-danger-500 hover:bg-danger-600 text-white font-bold py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-danger-500/30 hover:scale-105 active:scale-95"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reject
                </button>

                <button
                    @click="showPopup = false"
                    class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 px-4 rounded-xl transition-all duration-200"
                    title="Review Later"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

</div>
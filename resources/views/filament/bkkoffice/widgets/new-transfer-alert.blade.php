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

    {{-- ══════════════════════════════════════
         PENDING BANNER
    ══════════════════════════════════════ --}}
    @if($pendingCount > 0)
    <div style="margin-bottom:16px; background:linear-gradient(135deg,rgba(245,158,11,0.07) 0%,rgba(249,115,22,0.04) 100%); border:1px solid rgba(245,158,11,0.2); border-left:3px solid #f59e0b; border-radius:12px; padding:13px 16px; display:flex; align-items:center; gap:12px; box-shadow:0 2px 16px rgba(245,158,11,0.05),inset 0 1px 0 rgba(255,255,255,0.03); font-family:'Inter',system-ui,sans-serif;">

        {{-- Clock icon with ping ring --}}
        <div style="position:relative; flex-shrink:0; width:36px; height:36px;">
            <div style="position:absolute; inset:0; border-radius:10px; background:rgba(245,158,11,0.15); animation:banner-ping 2s ease-in-out infinite;"></div>
            <div style="position:relative; width:36px; height:36px; border-radius:10px; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.25); display:flex; align-items:center; justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Label --}}
        <div style="flex:1; min-width:0;">
            <div style="display:flex; align-items:center; gap:7px; flex-wrap:wrap;">
                <span style="font-size:13px; font-weight:700; color:#fbbf24; letter-spacing:-0.01em;">
                    {{ $pendingCount }} {{ __('message.Transfer-OUT') }} {{ Str::plural('request', $pendingCount) }}
                </span>
                <span style="font-size:9px; font-weight:700; color:#f59e0b; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.28); padding:2px 7px; border-radius:20px; text-transform:uppercase; letter-spacing:0.06em;">
                    Pending
                </span>
            </div>
            <p style="font-size:11px; color:#64748b; margin:2px 0 0;">{{ __('message.awaiting your approval') }}</p>
        </div>

        {{-- Live refresh indicator --}}
        <div style="flex-shrink:0; display:flex; align-items:center; gap:5px; padding:5px 10px; background:rgba(255,255,255,0.025); border:1px solid rgba(255,255,255,0.06); border-radius:8px;">
            <span style="width:5px; height:5px; border-radius:50%; background:#4ade80; display:inline-block; flex-shrink:0; animation:blink 1.5s ease-in-out infinite;"></span>
            <span style="font-size:10px; color:#475569; white-space:nowrap;">{{ __('message.Auto-refreshing every') }} 8s</span>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         POPUP OVERLAY
    ══════════════════════════════════════ --}}
    <div
        x-show="showPopup"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none; background:rgba(0,0,0,0.75); backdrop-filter:blur(6px);"
    >
        <div class="relative w-full max-w-md" style="font-family:'Inter',system-ui,sans-serif;">

            {{-- Card --}}
            <div style="background:#0f172a; border:1px solid rgba(255,255,255,0.08); border-radius:20px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.6),0 0 0 1px rgba(251,191,36,0.15);">

                {{-- Animated shimmer bar --}}
                <div style="height:3px; background:linear-gradient(90deg,#f59e0b,#f97316,#ef4444,#f97316,#f59e0b); background-size:200%; animation:shimmer 2s linear infinite;"></div>

                {{-- Header --}}
                <div style="padding:20px 24px 16px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; border-radius:12px; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center; flex-shrink:0; animation:pulse-border 2s ease-in-out infinite;">
                        <svg width="22" height="22" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:2px;">
                            <span style="font-size:15px; font-weight:700; color:#f1f5f9; letter-spacing:-0.01em;">{{ __('message.New Transfer-OUT Request!') }}</span>
                            <span style="font-size:10px; font-weight:600; color:#f59e0b; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); padding:2px 7px; border-radius:20px; text-transform:uppercase; letter-spacing:0.05em; animation:blink 1.5s ease-in-out infinite;">LIVE</span>
                        </div>
                        <p style="font-size:12px; color:#64748b; margin:0;">{{ __('message.Requires your immediate attention') }}</p>
                    </div>
                    <button
                        @click="showPopup = false"
                        style="width:30px; height:30px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748b; transition:all 0.15s; flex-shrink:0;"
                        onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#94a3b8';"
                        onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#64748b';"
                        title="{{ __('message.Close') }}"
                    >
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Transfer Details --}}
                <div x-show="transfer" style="padding:20px 24px;">

                    {{-- Invoice + Customer --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:12px 14px;">
                            <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.07em; margin:0 0 5px;">{{ __('message.Invoice') }}</p>
                            <p style="font-size:12px; font-weight:700; color:#e2e8f0; margin:0; font-family:'JetBrains Mono',monospace;" x-text="transfer?.invoice_number ?? '—'"></p>
                        </div>
                        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:12px 14px;">
                            <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.07em; margin:0 0 5px;">{{ __('message.Customer') }}</p>
                            <p style="font-size:12px; font-weight:700; color:#e2e8f0; margin:0;" x-text="transfer?.customer_name ?? '—'"></p>
                        </div>
                    </div>

                    {{-- Bank + Currency --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:12px 14px;">
                            <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.07em; margin:0 0 5px;">{{ __('message.Bank Name') }}</p>
                            <p style="font-size:12px; font-weight:700; color:#e2e8f0; margin:0;" x-text="transfer?.bank_name ?? '—'"></p>
                        </div>
                        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:12px 14px;">
                            <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.07em; margin:0 0 5px;">{{ __('message.Currency') }}</p>
                            <p style="font-size:12px; font-weight:700; color:#e2e8f0; margin:0;" x-text="transfer?.currency ?? '—'"></p>
                        </div>
                    </div>

                    {{-- Amount + Net Amount --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                        <div style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:12px 14px;">
                            <p style="font-size:10px; font-weight:600; color:#d97706; text-transform:uppercase; letter-spacing:0.07em; margin:0 0 5px;">{{ __('message.Amount') }}</p>
                            <p style="font-size:20px; font-weight:800; color:#fbbf24; margin:0; letter-spacing:-0.02em;" x-text="transfer?.amount ?? '—'"></p>
                        </div>
                        <div style="background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); border-radius:10px; padding:12px 14px;">
                            <p style="font-size:10px; font-weight:600; color:#4ade80; text-transform:uppercase; letter-spacing:0.07em; margin:0 0 5px;">{{ __('message.Net Amount') }}</p>
                            <p style="font-size:20px; font-weight:800; color:#4ade80; margin:0; letter-spacing:-0.02em;" x-text="transfer?.net_amount ?? '—'"></p>
                        </div>
                    </div>

                    {{-- Timestamp --}}
                    <p style="font-size:11px; color:#334155; text-align:center; margin:0 0 16px;" x-text="'Received at: ' + (transfer?.created_at ?? '')"></p>

                    {{-- Divider --}}
                    <div style="height:1px; background:rgba(255,255,255,0.06); margin-bottom:16px;"></div>

                    {{-- Action Buttons --}}
                    <div class="transfer-btn-row">

                        {{-- Accept --}}
                        <button
                            wire:click="acceptTransfer({{ $latestTransfer['id'] ?? 0 }})"
                            wire:loading.attr="disabled"
                            style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; padding:13px 16px; background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.35); border-radius:12px; color:#4ade80; font-size:13px; font-weight:700; cursor:pointer; letter-spacing:0.01em; transition:all 0.15s; white-space:nowrap;"
                            onmouseover="this.style.background='rgba(34,197,94,0.22)'; this.style.borderColor='rgba(34,197,94,0.55)'; this.style.transform='translateY(-1px)';"
                            onmouseout="this.style.background='rgba(34,197,94,0.12)'; this.style.borderColor='rgba(34,197,94,0.35)'; this.style.transform='translateY(0)';"
                        >
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('message.Accept') }}
                        </button>

                        {{-- Reject --}}
                        <button
                            wire:click="rejectTransfer({{ $latestTransfer['id'] ?? 0 }})"
                            wire:loading.attr="disabled"
                            style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; padding:13px 16px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:12px; color:#f87171; font-size:13px; font-weight:700; cursor:pointer; letter-spacing:0.01em; transition:all 0.15s; white-space:nowrap;"
                            onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.borderColor='rgba(239,68,68,0.5)'; this.style.transform='translateY(-1px)';"
                            onmouseout="this.style.background='rgba(239,68,68,0.1)'; this.style.borderColor='rgba(239,68,68,0.3)'; this.style.transform='translateY(0)';"
                        >
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('message.Reject') }}
                        </button>

                    </div>

                </div>
            </div>
        </div>
    </div>

    @once
    @push('styles')
    <style>
    @keyframes shimmer {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.35; }
    }
    @keyframes pulse-border {
        0%, 100% { box-shadow: 0 0 0 0   rgba(245,158,11,0.3); }
        50%       { box-shadow: 0 0 0 6px rgba(245,158,11,0); }
    }
    @keyframes banner-ping {
        0%        { transform: scale(1);    opacity: 0.45; }
        70%, 100% { transform: scale(1.4); opacity: 0; }
    }
    .transfer-btn-row {
        display:        flex !important;
        flex-direction: row !important;
        align-items:    stretch !important;
        gap:            10px !important;
        width:          100% !important;
    }
    </style>
    @endpush
    @endonce

</div>
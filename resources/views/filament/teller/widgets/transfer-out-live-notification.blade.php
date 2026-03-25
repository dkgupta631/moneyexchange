{{--
    transfer-out-live-notification.blade.php
    ─────────────────────────────────────────
    FIX SUMMARY:
    • Removed wire:poll from the div — the widget PHP class handles polling
      via $pollingInterval = '5s'. Having both causes double-polling bugs.
    • Removed @teller-notify.window — in Filament v3/Livewire 3, $dispatch()
      does NOT emit browser events. Instead, the PHP widget calls $this->js()
      which executes tellerReceive() directly in the browser.
    • window.tellerReceive is registered inside boot() so the PHP widget can
      call it reliably after every poll response.
    • Uses x-init with $nextTick to ensure Alpine is fully ready before
      processing initial records passed from PHP via @js().
--}}

<div
    x-data="tellerNotify(@js($latestAccepted), @js($latestCompleted), {{ (int) $pendingCount }})"
    x-init="$nextTick(() => boot())"
>

{{-- ══════════════════════════════════════════════════════════
     PENDING BANNER
══════════════════════════════════════════════════════════ --}}
<div x-show="pending > 0" x-cloak style="margin-bottom:20px;">
    <div style="
        background: linear-gradient(135deg,#1a1000,#251800,#1a1000);
        border: 1px solid rgba(251,191,36,0.28);
        border-radius: 14px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 0 0 1px rgba(251,191,36,0.06), 0 6px 30px rgba(0,0,0,0.55);
        position: relative;
        overflow: hidden;
    ">
        <div style="position:absolute;inset:0;pointer-events:none;
            background:radial-gradient(ellipse 55% 80% at 8% 50%,rgba(251,191,36,0.07) 0%,transparent 70%);"></div>

        <div style="display:flex;align-items:center;gap:14px;position:relative;">
            <div style="position:relative;width:42px;height:42px;flex-shrink:0;">
                <div style="position:absolute;inset:0;border-radius:50%;
                    background:rgba(251,191,36,0.2);
                    animation:tlrPing 1.6s cubic-bezier(0,0,.2,1) infinite;"></div>
                <div style="position:relative;width:42px;height:42px;border-radius:50%;
                    background:linear-gradient(135deg,#f59e0b,#d97706);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 0 20px rgba(245,158,11,.5);">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                    <span style="font-size:14px;font-weight:700;color:#fde68a;">
                        <span x-text="pending"></span>&nbsp;{{ __('message.Transfer-OUT Requests') }}
                    </span>
                    <span style="background:rgba(251,191,36,0.18);border:1px solid rgba(251,191,36,0.38);
                        color:#fbbf24;font-size:10px;font-weight:700;padding:1px 9px;
                        border-radius:999px;text-transform:uppercase;letter-spacing:.06em;">
                        {{ __('message.Pending') }}
                    </span>
                </div>
                <p style="font-size:11px;color:rgba(253,230,138,.48);margin:0;">
                    {{ __('message.Transfer Requests Pendingddd') }}
                </p>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:6px;position:relative;">
            <div style="width:7px;height:7px;border-radius:50%;background:#22c55e;
                box-shadow:0 0 8px #22c55e;animation:tlrPulse 1.2s ease-in-out infinite;"></div>
            <span style="font-size:10px;color:rgba(253,230,138,.38);letter-spacing:.04em;">LIVE · ↻ 5s</span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     POPUP OVERLAY
══════════════════════════════════════════════════════════ --}}
<template x-if="showPopup && currentRecord">
    <div
        style="position:fixed;inset:0;z-index:99999;
            display:flex;align-items:center;justify-content:center;padding:16px;
            background:rgba(0,0,0,0.78);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="dismiss()"
    >
        <div
            :style="cardStyle()"
            style="position:relative;width:100%;max-width:440px;border-radius:22px;overflow:hidden;
                box-shadow:0 40px 100px rgba(0,0,0,0.8),0 0 0 1px rgba(255,255,255,0.07);"
            x-transition:enter="transition ease-out duration-280"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            @click.stop
        >
            {{-- animated accent stripe --}}
            <div :style="accentBar()" style="height:3px;width:100%;"></div>

            {{-- ── HEADER ──────────────────────────────────────────── --}}
            <div style="padding:20px 22px 14px;position:relative;">
                <div :style="headerGlow()" style="position:absolute;inset:0;pointer-events:none;border-radius:22px 22px 0 0;"></div>
                <div style="position:relative;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div style="display:flex;align-items:center;gap:14px;">

                        {{-- icon --}}
                        <div :style="iconBox()" style="width:50px;height:50px;border-radius:15px;flex-shrink:0;
                            display:flex;align-items:center;justify-content:center;">
                            <template x-if="currentRecord.popup_type === 'accepted'">
                                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.9">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </template>
                            <template x-if="currentRecord.popup_type === 'completed'">
                                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                        </div>

                        <div>
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                <span :style="titleColor()" style="font-size:15px;font-weight:800;letter-spacing:.01em;"
                                    x-text="popupTitle()"></span>
                                <span style="background:rgba(239,68,68,.18);border:1px solid rgba(239,68,68,.45);
                                    color:#f87171;font-size:9px;font-weight:700;
                                    padding:1px 7px;border-radius:999px;letter-spacing:.08em;">LIVE</span>
                            </div>
                            <p style="font-size:11px;color:rgba(255,255,255,.40);margin:0;" x-text="popupSubtitle()"></p>
                        </div>
                    </div>

                    {{-- X close --}}
                    <button @click="dismiss()"
                        style="width:32px;height:32px;border-radius:10px;border:none;cursor:pointer;
                            background:rgba(255,255,255,.07);display:flex;align-items:center;
                            justify-content:center;flex-shrink:0;margin-top:2px;transition:background .15s;"
                        onmouseover="this.style.background='rgba(255,255,255,.16)'"
                        onmouseout="this.style.background='rgba(255,255,255,.07)'"
                        title="{{ __('message.Close') }}">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                            stroke="rgba(255,255,255,.55)" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- separator --}}
            <div style="height:1px;background:rgba(255,255,255,.07);margin:0 22px;"></div>

            {{-- ── INVOICE + STATUS ─────────────────────────────────── --}}
            <div style="padding:13px 22px 10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <div :style="invoiceBadge()"
                    style="border-radius:8px;padding:4px 13px;font-size:11px;font-weight:700;letter-spacing:.04em;">
                    #<span x-text="currentRecord.invoice_number"></span>
                </div>
                <div :style="statusBadge()"
                    style="border-radius:999px;padding:3px 12px;font-size:10px;font-weight:700;
                        letter-spacing:.06em;text-transform:uppercase;"
                    x-text="statusLabel()">
                </div>
                <span style="font-size:10px;color:rgba(255,255,255,.22);margin-left:auto;">
                    {{ __('message.Received at') }}:&nbsp;<span x-text="nowTime()"></span>
                </span>
            </div>

            {{-- ── DETAILS GRID ──────────────────────────────────────── --}}
            <div style="padding:2px 22px 16px;display:grid;grid-template-columns:1fr 1fr;gap:9px;">

                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:12px 14px;">
                    <div style="font-size:9.5px;color:rgba(255,255,255,.36);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">
                        {{ __('message.Invoice Number') }}
                    </div>
                    <div style="font-size:12px;font-weight:700;color:#a78bfa;font-family:monospace;letter-spacing:.04em;"
                        x-text="currentRecord.invoice_number"></div>
                </div>

                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:12px 14px;">
                    <div style="font-size:9.5px;color:rgba(255,255,255,.36);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">
                        {{ __('message.popup_account_name') }}
                    </div>
                    <div style="font-size:13px;font-weight:600;color:#fff;" x-text="currentRecord.acc_name || '—'"></div>
                </div>

                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:12px 14px;">
                    <div style="font-size:9.5px;color:rgba(255,255,255,.36);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">
                        {{ __('message.popup_bank') }}
                    </div>
                    <div style="font-size:13px;font-weight:600;color:#fff;" x-text="currentRecord.bank_name || '—'"></div>
                </div>

                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:12px 14px;">
                    <div style="font-size:9.5px;color:rgba(255,255,255,.36);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">
                        {{ __('message.popup_account_number') }}
                    </div>
                    <div style="font-size:13px;font-weight:700;color:#93c5fd;font-family:monospace;letter-spacing:.06em;"
                        x-text="currentRecord.acc_number || '—'"></div>
                </div>

                {{-- Amount --}}
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:13px 14px;">
                    <div style="font-size:9.5px;color:rgba(255,255,255,.36);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">
                        {{ __('message.popup_amount') }}
                    </div>
                    <div style="font-size:20px;font-weight:800;color:#fff;line-height:1;">
                        <span x-text="currentRecord.currency"></span>&nbsp;<span x-text="fmt(currentRecord.entered_amount)"></span>
                    </div>
                </div>

                {{-- Net Amount --}}
                <div :style="netCard()" style="border-radius:12px;padding:13px 14px;">
                    <div style="font-size:9.5px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">
                        {{ __('message.popup_net_amount') }}
                    </div>
                    <div :style="netText()" style="font-size:20px;font-weight:800;line-height:1;">
                        <span x-text="currentRecord.currency"></span>&nbsp;<span x-text="fmt(currentRecord.net_amount)"></span>
                    </div>
                </div>
            </div>

            {{-- ── DISMISS BUTTON ──────────────────────────────────── --}}
            <div style="padding:0 22px 22px;">
                <button @click="dismiss()" :style="dismissBtn()"
                    style="width:100%;border:none;cursor:pointer;border-radius:13px;padding:13px;
                        font-size:13px;font-weight:700;letter-spacing:.04em;color:#fff;
                        transition:opacity .15s,transform .12s;"
                    onmouseover="this.style.opacity='.80'"
                    onmouseout="this.style.opacity='1'"
                    onmousedown="this.style.transform='scale(.975)'"
                    onmouseup="this.style.transform='scale(1)'">
                    {{ __('message.Dismiss') }}
                </button>
            </div>

        </div>{{-- /card --}}
    </div>{{-- /overlay --}}
</template>

</div>{{-- /x-data --}}

@push('scripts')
<style>
@keyframes tlrPing  { 75%,100%{transform:scale(2);opacity:0} }
@keyframes tlrPulse { 0%,100%{opacity:1} 50%{opacity:.3} }
@keyframes tlrSlide { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
[x-cloak] { display:none !important; }
</style>
<script>
function tellerNotify(initAccepted, initCompleted, initPending) {
    return {
        // ── state ────────────────────────────────────────────────────
        accepted:      Array.isArray(initAccepted)  ? initAccepted  : [],
        completed:     Array.isArray(initCompleted) ? initCompleted : [],
        pending:       parseInt(initPending) || 0,
        showPopup:     false,
        currentRecord: null,
        queue:         [],
        SEEN:          'tlr_seen_v4',   // bump version = fresh start

        // ── localStorage ─────────────────────────────────────────────
        getSeenSet() {
            try { return new Set(JSON.parse(localStorage.getItem(this.SEEN) || '[]')); }
            catch { return new Set(); }
        },
        saveSeenSet(s) {
            let a = [...s];
            if (a.length > 800) a = a.slice(-800);
            try { localStorage.setItem(this.SEEN, JSON.stringify(a)); } catch {}
        },
        isSeen(id) { return this.getSeenSet().has(String(id)); },
        markSeen(id) {
            const s = this.getSeenSet();
            s.add(String(id));
            this.saveSeenSet(s);
        },

        // ── voice ────────────────────────────────────────────────────
        speak(rec) {
            try {
                if (!('speechSynthesis' in window)) return;
                window.speechSynthesis.cancel();
                let msg = rec.popup_type === 'accepted'
                    ? `{{ __('message.Have new Transfer-OUT') }}. Invoice ${rec.invoice_number}. Account ${rec.acc_name}. Net amount ${rec.currency} ${this.fmt(rec.net_amount)}.`
                    : `Transfer-OUT {{ __('message.Completed') }}. Invoice ${rec.invoice_number}. Customer ${rec.customer_name}. Net amount ${rec.currency} ${this.fmt(rec.net_amount)}.`;
                const u = new SpeechSynthesisUtterance(msg);
                u.lang   = document.documentElement.lang || 'en-US';
                u.rate   = 0.88;
                u.volume = 1;
                window.speechSynthesis.speak(u);
            } catch(e) { console.warn('[teller] speak error', e); }
        },

        // ── format number ─────────────────────────────────────────────
        fmt(n) {
            return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        nowTime() {
            return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },

        // ── queue ────────────────────────────────────────────────────
        enqueue(accepted, completed) {
            const all = [...(accepted || this.accepted), ...(completed || this.completed)];
            const newRecs = all.filter(r => !this.isSeen(r.id));
            // Mark immediately so next poll won't re-add
            newRecs.forEach(r => this.markSeen(r.id));
            if (newRecs.length > 0) {
                this.queue.push(...newRecs);
                this.next();
            }
        },
        next() {
            if (this.showPopup || this.queue.length === 0) return;
            this.currentRecord = this.queue.shift();
            this.showPopup     = true;
            this.$nextTick(() => this.speak(this.currentRecord));
        },
        dismiss() {
            this.showPopup     = false;
            this.currentRecord = null;
            setTimeout(() => this.next(), 350);
        },

        // ── *** THE KEY FIX ***
        // PHP widget calls: $this->js("window.tellerReceive({...})")
        // We register this function on window so PHP can call it directly.
        boot() {
            // Register the global receiver that PHP's $this->js() will call
            window.tellerReceive = (data) => {
                if (!data) return;
                this.accepted  = data.accepted  || [];
                this.completed = data.completed || [];
                this.pending   = parseInt(data.pendingCount) || 0;
                this.enqueue(this.accepted, this.completed);
            };

            // Process records already present on page load
            this.enqueue();

            console.log('[TellerNotify] Booted. Accepted:', this.accepted.length, 'Completed:', this.completed.length);
        },

        // ── theme: accepted = amber/orange, completed = emerald ───────
        isC() { return this.currentRecord?.popup_type === 'completed'; },
        cardStyle()    { return this.isC()
            ? 'background:linear-gradient(155deg,#021a0e 0%,#062015 55%,#021a0e 100%);'
            : 'background:linear-gradient(155deg,#1a1000 0%,#211600 55%,#1a1000 100%);'; },
        accentBar()    { return this.isC()
            ? 'background:linear-gradient(90deg,#059669,#10b981,#34d399,#10b981,#059669);background-size:300% 100%;animation:tlrSlide 2.5s linear infinite;'
            : 'background:linear-gradient(90deg,#b45309,#f59e0b,#fde68a,#f59e0b,#b45309);background-size:300% 100%;animation:tlrSlide 2.5s linear infinite;'; },
        headerGlow()   { return this.isC()
            ? 'background:radial-gradient(ellipse 80% 100% at 0 0,rgba(16,185,129,.10) 0%,transparent 65%);'
            : 'background:radial-gradient(ellipse 80% 100% at 0 0,rgba(245,158,11,.10) 0%,transparent 65%);'; },
        iconBox()      { return this.isC()
            ? 'background:linear-gradient(135deg,#059669,#10b981);box-shadow:0 8px 28px rgba(16,185,129,.50);'
            : 'background:linear-gradient(135deg,#d97706,#f59e0b);box-shadow:0 8px 28px rgba(245,158,11,.50);'; },
        titleColor()   { return this.isC() ? 'color:#6ee7b7;' : 'color:#fde68a;'; },
        invoiceBadge() { return this.isC()
            ? 'background:rgba(16,185,129,.14);border:1px solid rgba(16,185,129,.40);color:#34d399;'
            : 'background:rgba(245,158,11,.14);border:1px solid rgba(245,158,11,.40);color:#fbbf24;'; },
        statusBadge()  { return this.isC()
            ? 'background:rgba(16,185,129,.17);border:1px solid rgba(16,185,129,.45);color:#34d399;'
            : 'background:rgba(99,179,237,.17);border:1px solid rgba(99,179,237,.45);color:#93c5fd;'; },
        netCard()      { return this.isC()
            ? 'background:rgba(16,185,129,.09);border:1px solid rgba(16,185,129,.30);'
            : 'background:rgba(245,158,11,.09);border:1px solid rgba(245,158,11,.30);'; },
        netText()      { return this.isC() ? 'color:#34d399;' : 'color:#fbbf24;'; },
        dismissBtn()   { return this.isC()
            ? 'background:linear-gradient(135deg,#059669,#10b981);'
            : 'background:linear-gradient(135deg,#d97706,#f59e0b);'; },
        popupTitle()   { return this.isC()
            ? '{{ __('message.Completed') }} Transfer-OUT ✓'
            : '{{ __('message.popup_title') }}'; },
        popupSubtitle(){ return this.isC()
            ? '{{ __('message.Transfer-OUT') }} {{ __('message.Completed') }} — {{ __('message.Account Details') }}'
            : '{{ __('message.popup_subtitle') }}'; },
        statusLabel()  { return this.isC()
            ? '{{ __('message.Completed') }}'
            : '{{ __('message.Accepted') }}'; },
    };
}
</script>
@endpush
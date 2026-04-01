{{-- resources/views/filament/teller/widgets/transfer-in-live-banner.blade.php --}}
<x-filament-widgets::widget>
    <div wire:poll.8000ms="refresh">
        @if ($pendingCount > 0 || $acceptedCount > 0)
            <div class="rounded-2xl overflow-hidden shadow-xl"
                 style="background: linear-gradient(135deg, #78350f 0%, #92400e 50%, #78350f 100%);
                        border: 1px solid rgba(251,191,36,0.3);">
                <div class="flex items-center gap-4 px-6 py-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl flex-shrink-0"
                         style="background:rgba(251,191,36,0.2); border:1px solid rgba(251,191,36,0.3);">
                        <svg class="w-6 h-6" style="color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white text-lg">
                            {{ $pendingCount + $acceptedCount }}
                            {{ __('message.Transfer-IN') }} {{ __('message.Requests') }}
                        </p>
                        <p class="text-sm" style="color:rgba(255,255,255,0.6);">
                            {{ __('message.Transfer_Requests') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 flex items-center gap-2">
                        @if($pendingCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                                  style="background:rgba(251,191,36,0.2); color:#fbbf24; border:1px solid rgba(251,191,36,0.3);">
                                {{ strtoupper(__('message.Pending')) }}
                            </span>
                        @endif
                        @if($acceptedCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                                  style="background:rgba(59,130,246,0.2); color:#93c5fd; border:1px solid rgba(59,130,246,0.3);">
                                {{ strtoupper(__('message.Accepted')) }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 text-xs" style="color:rgba(255,255,255,0.5);">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse inline-block"></span>
                            LIVE dddddd· {{ $lastChecked }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
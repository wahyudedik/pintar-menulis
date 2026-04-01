    @php
        $sub = auth()->user()->currentSubscription();
    @endphp
    @if(!$sub || !$sub->isValid())
    <div class="mb-5 bg-yellow-50 border border-yellow-300 rounded-xl p-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⚡</span>
            <div>
                <p class="font-semibold text-yellow-900 text-sm">Belum ada langganan aktif</p>
                <p class="text-xs text-yellow-700 mt-0.5">Mulai trial gratis 30 hari untuk menggunakan AI Generator.</p>
            </div>
        </div>
        <a href="{{ route('pricing') }}"
           class="flex-shrink-0 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold rounded-lg transition">
            Mulai Trial Gratis
        </a>
    </div>
    @elseif($sub->isOnTrial())
    <div class="mb-5 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🎉</span>
            <div>
                <p class="font-semibold text-blue-900 text-sm">Trial aktif — {{ $sub->days_remaining }} hari tersisa</p>
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-32 bg-blue-200 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ $sub->trial_progress }}%"></div>
                    </div>
                    <span class="text-xs text-blue-600">Kuota: {{ $sub->remaining_quota }}/{{ $sub->ai_quota_limit }}
                        @if($sub->package && $sub->package->price == 0)
                            · Maks 5/hari
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('pricing') }}"
           class="flex-shrink-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition">
            Upgrade Paket
        </a>
    </div>
    @elseif($sub->isActive())
    @php $quotaPct = $sub->quota_percentage; @endphp
    @if($quotaPct >= 80)
    <div class="mb-5 bg-orange-50 border border-orange-200 rounded-xl p-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">{{ $quotaPct >= 100 ? '🚫' : '⚠️' }}</span>
            <div>
                <p class="font-semibold text-orange-900 text-sm">
                    {{ $quotaPct >= 100 ? 'Kuota habis!' : 'Kuota hampir habis' }}
                </p>
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-32 bg-orange-200 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $quotaPct >= 100 ? 'bg-red-500' : 'bg-orange-500' }}"
                             style="width: {{ min(100, $quotaPct) }}%"></div>
                    </div>
                    <span class="text-xs text-orange-700">{{ $sub->remaining_quota }} generate tersisa</span>
                </div>
            </div>
        </div>
        <a href="{{ route('pricing') }}"
           class="flex-shrink-0 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg transition">
            Upgrade
        </a>
    </div>
    @endif
    @endif

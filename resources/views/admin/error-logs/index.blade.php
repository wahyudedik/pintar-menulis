@extends('layouts.admin-nav')

@section('title', 'Error Logs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="errorLogs()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">🪵 Error Logs</h1>
            <p class="text-sm text-gray-500 mt-1">
                File: <code class="bg-gray-100 px-1 rounded text-xs">storage/logs/laravel.log</code>
                &nbsp;·&nbsp; Ukuran: <span class="font-medium">{{ $stats['file_size'] }}</span>
                &nbsp;·&nbsp; Terakhir diubah: <span class="font-medium">{{ $stats['last_modified'] }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.error-logs.download') }}"
               class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
            <form method="POST" action="{{ route('admin.error-logs.clear') }}"
                  onsubmit="return confirm('Yakin ingin mengosongkan semua log?')">
                @csrf
                <button type="submit"
                        class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Kosongkan
                </button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @php
        $statCards = [
            ['label'=>'Total',    'key'=>'total',    'color'=>'gray',   'icon'=>'📋'],
            ['label'=>'Error',    'key'=>'error',    'color'=>'red',    'icon'=>'🔴'],
            ['label'=>'Critical', 'key'=>'critical', 'color'=>'purple', 'icon'=>'🚨'],
            ['label'=>'Warning',  'key'=>'warning',  'color'=>'yellow', 'icon'=>'🟡'],
            ['label'=>'Info',     'key'=>'info',     'color'=>'blue',   'icon'=>'🔵'],
            ['label'=>'Debug',    'key'=>'debug',    'color'=>'green',  'icon'=>'🟢'],
        ];
        $colorMap = [
            'gray'   => 'bg-gray-50 border-gray-200 text-gray-700',
            'red'    => 'bg-red-50 border-red-200 text-red-700',
            'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
            'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
            'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
            'green'  => 'bg-green-50 border-green-200 text-green-700',
        ];
        @endphp
        @foreach($statCards as $card)
        <a href="{{ route('admin.error-logs.index', array_merge(request()->query(), ['level' => $card['key'] === 'total' ? 'all' : $card['key'], 'page' => 1])) }}"
           class="border rounded-lg p-3 text-center {{ $colorMap[$card['color']] }} hover:opacity-80 transition">
            <div class="text-xl">{{ $card['icon'] }}</div>
            <div class="text-2xl font-bold">{{ $stats[$card['key']] ?? 0 }}</div>
            <div class="text-xs font-medium">{{ $card['label'] }}</div>
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.error-logs.index') }}" class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari pesan</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari error, class, file..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Level</label>
                <select name="level" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach(['all'=>'Semua Level','error'=>'Error','critical'=>'Critical','warning'=>'Warning','info'=>'Info','debug'=>'Debug'] as $val => $lbl)
                    <option value="{{ $val }}" {{ $level === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Per halaman</label>
                <select name="per_page" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach([25, 50, 100, 200] as $n)
                    <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.error-logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Reset</a>
        </div>
    </form>

    {{-- Results info --}}
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium">{{ count($entries) }}</span> dari <span class="font-medium">{{ $total }}</span> entri
            @if($search) &nbsp;·&nbsp; pencarian: "<span class="font-medium text-blue-600">{{ $search }}</span>" @endif
        </p>
        {{-- Pagination --}}
        @if($totalPages > 1)
        <div class="flex items-center gap-1">
            @if($page > 1)
            <a href="{{ route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">← Prev</a>
            @endif
            <span class="px-3 py-1 text-sm text-gray-600">{{ $page }} / {{ $totalPages }}</span>
            @if($page < $totalPages)
            <a href="{{ route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">Next →</a>
            @endif
        </div>
        @endif
    </div>

    {{-- Log Entries --}}
    @if(count($entries) === 0)
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
        <div class="text-4xl mb-3">✅</div>
        <p class="text-gray-500">Tidak ada log yang ditemukan.</p>
    </div>
    @else
    <div class="space-y-2">
        @foreach($entries as $i => $entry)
        @php
        $levelColors = [
            'error'     => 'border-red-300 bg-red-50',
            'critical'  => 'border-purple-400 bg-purple-50',
            'emergency' => 'border-red-600 bg-red-100',
            'alert'     => 'border-orange-400 bg-orange-50',
            'warning'   => 'border-yellow-300 bg-yellow-50',
            'notice'    => 'border-blue-200 bg-blue-50',
            'info'      => 'border-blue-200 bg-blue-50',
            'debug'     => 'border-gray-200 bg-gray-50',
        ];
        $badgeColors = [
            'error'     => 'bg-red-100 text-red-700',
            'critical'  => 'bg-purple-100 text-purple-700',
            'emergency' => 'bg-red-200 text-red-800',
            'alert'     => 'bg-orange-100 text-orange-700',
            'warning'   => 'bg-yellow-100 text-yellow-700',
            'notice'    => 'bg-blue-100 text-blue-700',
            'info'      => 'bg-blue-100 text-blue-700',
            'debug'     => 'bg-gray-100 text-gray-600',
        ];
        $borderClass = $levelColors[$entry['level']] ?? 'border-gray-200 bg-white';
        $badgeClass  = $badgeColors[$entry['level']] ?? 'bg-gray-100 text-gray-600';
        $entryId     = 'entry-' . $i;
        @endphp
        <div class="border rounded-lg {{ $borderClass }} overflow-hidden" x-data="{ open: false }">
            {{-- Summary row --}}
            <div class="flex items-start gap-3 p-3 cursor-pointer select-none" @click="open = !open">
                <span class="flex-shrink-0 px-2 py-0.5 text-xs font-bold rounded uppercase {{ $badgeClass }}">
                    {{ $entry['level'] }}
                </span>
                <span class="flex-shrink-0 text-xs text-gray-500 whitespace-nowrap pt-0.5">
                    {{ $entry['datetime'] }}
                </span>
                <span class="flex-1 text-sm text-gray-800 font-mono leading-snug break-all">
                    {{ $entry['message'] }}
                </span>
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            {{-- Detail panel --}}
            <div x-show="open" x-cloak class="border-t border-gray-200 bg-white">
                {{-- Context --}}
                @if(!empty($entry['context']))
                <div class="p-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Context</p>
                    @if(is_array($entry['context']))
                        @foreach($entry['context'] as $key => $val)
                        @if($key !== 'exception')
                        <div class="flex gap-2 text-xs mb-1">
                            <span class="font-medium text-gray-600 min-w-24">{{ $key }}</span>
                            <span class="text-gray-800 font-mono break-all">{{ is_array($val) ? json_encode($val) : $val }}</span>
                        </div>
                        @endif
                        @endforeach
                    @else
                        <p class="text-xs font-mono text-gray-700 break-all">{{ $entry['context'] }}</p>
                    @endif
                </div>
                @endif

                {{-- Stacktrace --}}
                @if(!empty($entry['stacktrace']))
                <div class="p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Stack Trace</p>
                    <pre class="text-xs font-mono text-gray-700 bg-gray-900 text-green-300 p-3 rounded overflow-x-auto whitespace-pre-wrap leading-relaxed max-h-64 overflow-y-auto">{{ $entry['stacktrace'] }}</pre>
                </div>
                @endif

                {{-- Raw --}}
                <div class="p-3 border-t border-gray-100">
                    <details>
                        <summary class="text-xs font-semibold text-gray-400 cursor-pointer hover:text-gray-600">Raw log entry</summary>
                        <pre class="mt-2 text-xs font-mono text-gray-600 bg-gray-50 p-2 rounded overflow-x-auto whitespace-pre-wrap max-h-48 overflow-y-auto">{{ $entry['full'] }}</pre>
                    </details>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Bottom pagination --}}
    @if($totalPages > 1)
    <div class="flex justify-center gap-2 mt-6">
        @if($page > 1)
        <a href="{{ route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
           class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50">← Sebelumnya</a>
        @endif
        <span class="px-4 py-2 text-sm text-gray-600">Halaman {{ $page }} dari {{ $totalPages }}</span>
        @if($page < $totalPages)
        <a href="{{ route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
           class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Selanjutnya →</a>
        @endif
    </div>
    @endif
    @endif

</div>

@push('scripts')
<script>
function errorLogs() {
    return {};
}
</script>
@endpush
@endsection

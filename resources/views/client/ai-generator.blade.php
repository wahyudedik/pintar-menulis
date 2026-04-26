@extends('layouts.client')

@section('title', 'AI Generator')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="p-3 sm:p-6" x-data="aiGenerator" x-init="init()">

    {{-- ── Toast Notification ── --}}
    <div x-show="notificationVisible" x-cloak x-transition
         :class="notificationType === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed bottom-6 right-6 z-50 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium max-w-xs"
         x-text="notificationMessage">
    </div>

    {{-- ── Subscription / Quota Banner ── --}}
    @include('client.partials.ai-generator.subscription-banner')

    {{-- ── Header + Navigation ── --}}
    @include('client.partials.ai-generator.header-navigation')

    <!-- Text Mode Toggle -->
    <div class="mb-6 flex justify-center" x-show="generatorType === 'text'" x-cloak>
        <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50">
            <button @click="mode = 'simple'"
                    :class="mode === 'simple' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                🎯 Mode Simpel
            </button>
            <button @click="mode = 'advanced'"
                    :class="mode === 'advanced' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                ⚙️ Mode Lengkap
            </button>
        </div>
    </div>

    <!-- Content Area -->
    <div class="w-full">

        {{-- ── Form Input ── --}}
        <div class="w-full">
            <div class="bg-white rounded-lg border border-gray-200 p-6"
                 x-show="generatorType === 'text' || generatorType === 'image' || generatorType === 'video'" x-cloak>
                @include('client.partials.ai-generator.form-text-simple')
                @include('client.partials.ai-generator.form-text-advanced')
                @include('client.partials.ai-generator.form-image-caption')
                @include('client.partials.ai-generator.form-video')
            </div>

            {{-- ── History Placeholder ── --}}
            @include('client.partials.ai-generator.placeholder-and-predictor')
        </div>

        {{-- ── Result Section ── --}}
        @include('client.partials.ai-generator.result-section')

        {{-- ── ML Upgrade Modal ── --}}
        @include('client.partials.ml-upgrade-modal')

    </div>
</div>

@include('client.partials.ai-generator._script')

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

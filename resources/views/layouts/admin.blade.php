@extends('layouts.app-layout')

@section('sidebar-menu')
<!-- Dashboard -->
<a href="{{ route('dashboard') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Dashboard">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
</a>

<!-- Users -->
<a href="{{ route('admin.users') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Users">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
</a>

<!-- Packages -->
<a href="{{ route('admin.packages') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.packages*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Packages">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
    </svg>
</a>

<!-- Subscriptions / Payments -->
@php $pendingSubsCount = \App\Models\UserSubscription::where('status', 'pending_payment')->count(); @endphp
<a href="{{ route('admin.subscriptions') }}" 
   class="tooltip relative flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.subscriptions*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Subscriptions & Payments">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
    </svg>
    @if($pendingSubsCount > 0)
    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
        {{ $pendingSubsCount > 9 ? '9+' : $pendingSubsCount }}
    </span>
    @endif
</a>

<!-- Withdrawals -->
<a href="{{ route('admin.withdrawals') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.withdrawals*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Withdrawals">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
</a>

<!-- Reports -->
<a href="{{ route('admin.reports') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.reports') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Reports">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
</a>

<!-- AI Usage Analytics -->
<a href="{{ route('admin.ai-usage.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.ai-usage.*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="AI Usage">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
</a>

<!-- ML Analytics -->
<a href="{{ route('admin.ml-analytics.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.ml-analytics.*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="ML Analytics">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
    </svg>
</a>

<!-- Settings -->
<a href="{{ route('admin.payment-settings') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.payment-settings*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Settings">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
    </svg>
</a>

<!-- WhatsApp Analytics -->
<a href="{{ route('admin.whatsapp-analytics.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.whatsapp-analytics.*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="WhatsApp Analytics">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
</a>

<!-- Banner Information -->
<a href="{{ route('admin.banner-information.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.banner-information*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Banner Information">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
    </svg>
</a>

<!-- Feedback Management -->
<a href="{{ route('admin.feedback') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.feedback*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Feedback & Support">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
</a>

<!-- Ad Placements -->
<a href="{{ route('admin.ad-placements.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.ad-placements*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Ad Placements">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
    </svg>
</a>

<!-- Guru Monitor -->
<a href="{{ route('admin.guru-monitor.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.guru-monitor*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Guru Monitor">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
    </svg>
</a>

<!-- Referral Monitor -->
<a href="{{ route('admin.referrals.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('admin.referrals*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Referral Monitor">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
</a>
@endsection

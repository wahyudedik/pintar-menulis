@extends('layouts.app-layout')

@section('sidebar-menu')
<!-- Dashboard -->
<a href="{{ route('dashboard') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Dashboard">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
</a>

<!-- AI Generator -->
<a href="{{ route('ai.generator') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('ai.generator') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="AI Generator">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
</a>

<!-- Analytics -->
<a href="{{ route('analytics.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('analytics.*') && !request()->routeIs('caption-history.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Analytics">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
</a>

<!-- Caption History (ML) -->
<a href="{{ route('caption-history.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('caption-history.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Caption History">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
</a>

<!-- Browse Operators -->
<a href="{{ route('browse.operators') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('browse.operators') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Cari Operator">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
</a>

<!-- My Orders -->
<a href="{{ route('orders.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('orders.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Order Saya">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
</a>

<!-- Projects -->
<a href="{{ route('projects.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('projects.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Project">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
    </svg>
</a>

<!-- Feedback & Support -->
<a href="{{ route('feedback.index') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('feedback.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Feedback & Support">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
</a>
@endsection

@extends('layouts.app-layout')

@section('sidebar-menu')
<!-- Dashboard -->
<a href="{{ route('dashboard') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Dashboard">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
</a>

<!-- Order Queue -->
<a href="{{ route('operator.queue') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('operator.queue') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Order Queue">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
    </svg>
</a>

<!-- Workspace -->
@if(request()->routeIs('operator.workspace'))
<a href="{{ route('operator.workspace', request()->route('order')) }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg bg-green-50 text-green-600 transition"
   data-tooltip="Workspace">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
    </svg>
</a>
@endif

<!-- Earnings -->
<a href="{{ route('operator.earnings') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('operator.earnings') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Penghasilan">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
</a>

<!-- Profile -->
<a href="{{ route('operator.profile.edit') }}" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition {{ request()->routeIs('operator.profile.*') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-100' }}"
   data-tooltip="Profile">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
    </svg>
</a>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @php
            /** @var \App\Models\User $user */
            $user = Auth::user();
        @endphp
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Selamat Datang -->
                    <div class="md:col-span-3">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang, {{ $user->name }}!</h3>
                        <p class="text-gray-600">Mulai buat konten promosi untuk bisnis Anda hari ini.</p>
                    </div>

                    <!-- Statistik / Pintasan -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                        <h4 class="font-bold text-blue-800 mb-2">Pesanan Aktif</h4>
                        <p class="text-3xl font-bold text-blue-900">
                            {{ $user->orders()->where('status', 'active')->count() }}</p>
                        <a href="{{ route('orders.index') }}"
                            class="mt-4 inline-block text-sm font-bold text-blue-600 hover:text-blue-800">Lihat Semua
                            →</a>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                        <h4 class="font-bold text-green-800 mb-2">Proyek Bisnis</h4>
                        <p class="text-3xl font-bold text-green-900">{{ $user->projects()->count() }}</p>
                        <a href="{{ route('projects.index') }}"
                            class="mt-4 inline-block text-sm font-bold text-green-600 hover:text-green-800">Kelola
                            Proyek →</a>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
                        <h4 class="font-bold text-purple-800 mb-2">Request Konten</h4>
                        <p class="text-3xl font-bold text-purple-900">{{ $user->copywritingRequests()->count() }}</p>
                        <a href="{{ route('copywriting.index') }}"
                            class="mt-4 inline-block text-sm font-bold text-purple-600 hover:text-purple-800">Lihat
                            Request →</a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mt-12">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Aktivitas Terakhir</h3>
                    @php
                        $recentRequests = $user->copywritingRequests()->latest()->take(5)->get();
                    @endphp

                    @if ($recentRequests->isEmpty())
                        <p class="text-gray-500 text-sm italic">Belum ada aktivitas terbaru.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($recentRequests as $request)
                                <div class="py-4 flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ ucfirst($request->type) }} untuk
                                            {{ ucfirst($request->platform) }}</p>
                                        <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

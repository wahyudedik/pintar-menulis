<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pesanan #{{ $order->id }} - {{ $order->package->name }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Utama -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Berlangganan</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Mulai</p>
                                    <p class="font-medium">{{ $order->start_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Berakhir</p>
                                    <p class="font-medium">{{ $order->end_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Total Harga</p>
                                    <p class="font-medium">Rp {{ number_format($order->package->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Permintaan Copywriting</h3>
                                <a href="{{ route('copywriting.create', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Request Baru
                                </a>
                            </div>
                            
                            @if($order->copywritingRequests->isEmpty())
                                <p class="text-gray-500 text-sm text-center py-8 italic">Belum ada permintaan copywriting.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($order->copywritingRequests as $request)
                                        <div class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-bold text-gray-900">{{ ucfirst($request->type) }} - {{ ucfirst($request->platform) }}</h4>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->format('d M Y H:i') }}</p>
                                                </div>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <a href="{{ route('copywriting.show', $request) }}" class="text-sm text-blue-600 hover:text-blue-900">Detail & Hasil</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar Quota -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Sisa Kuota</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Caption</span>
                                        <span class="font-bold text-gray-900">{{ $order->remaining_caption_quota }} / {{ $order->package->caption_quota }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($order->remaining_caption_quota / $order->package->caption_quota) * 100 }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Deskripsi Produk</span>
                                        <span class="font-bold text-gray-900">{{ $order->remaining_product_description_quota }} / {{ $order->package->product_description_quota }}</span>
                                    </div>
                                    @if($order->package->product_description_quota > 0)
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($order->remaining_product_description_quota / $order->package->product_description_quota) * 100 }}%"></div>
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-400 italic">Tidak termasuk dalam paket ini.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

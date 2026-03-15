<x-public-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-3">Pilih Paket yang Sesuai</h2>
            <p class="text-xl text-gray-600">Copywriting berkualitas, harga pelajar, hasil profesional</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach($packages as $package)
            <div class="bg-white rounded-lg border-2 overflow-hidden transition {{ $package->badge_text ? 'border-blue-600' : 'border-gray-200 hover:border-gray-300' }}">
                @if($package->badge_text)
                <div class="text-white text-center py-2 text-sm font-semibold" style="background-color: {{ $package->badge_color ?? '#2563eb' }}">
                    {{ strtoupper($package->badge_text) }}
                </div>
                @endif
                
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>
                    <p class="text-sm text-gray-600 mb-6">{{ $package->description }}</p>
                    
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <span class="text-4xl font-bold text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        <span class="text-gray-600">/bulan</span>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $package->caption_quota }} caption media sosial</span>
                        </li>
                        @if($package->product_description_quota > 0)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $package->product_description_quota }} deskripsi produk</span>
                        </li>
                        @endif
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Revisi {{ $package->revision_limit }}x</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Response time {{ $package->response_time_hours }} jam</span>
                        </li>
                        @if($package->consultation_included)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Konsultasi strategi konten</span>
                        </li>
                        @endif
                    </ul>

                    <a href="{{ route('orders.create', $package) }}" class="block text-center w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                        Pilih Paket
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-public-layout>

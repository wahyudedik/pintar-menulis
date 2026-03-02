<x-public-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Pilih Paket yang Sesuai</h2>
            <p class="text-xl text-gray-600">Copywriting berkualitas, harga pelajar, hasil profesional</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($packages as $package)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition {{ $package->name === 'Professional' ? 'ring-2 ring-blue-600' : '' }}">
                @if($package->name === 'Professional')
                <div class="bg-blue-600 text-white text-center py-2 text-sm font-semibold">
                    PALING POPULER
                </div>
                @endif
                
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>
                    <p class="text-gray-600 mb-6">{{ $package->description }}</p>
                    
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        <span class="text-gray-600">/bulan</span>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $package->caption_quota }} caption media sosial</span>
                        </li>
                        @if($package->product_description_quota > 0)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $package->product_description_quota }} deskripsi produk</span>
                        </li>
                        @endif
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Revisi {{ $package->revision_limit }}x</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Response time {{ $package->response_time_hours }} jam</span>
                        </li>
                        @if($package->consultation_included)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Konsultasi strategi konten</span>
                        </li>
                        @endif
                    </ul>

                    <a href="{{ route('orders.create', $package) }}" class="block text-center w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                        Pilih Paket
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-public-layout>

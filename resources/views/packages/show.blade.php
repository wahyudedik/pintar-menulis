<x-public-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('packages.index') }}" class="hover:text-blue-600">Paket</a>
            <span>/</span>
            <span class="text-gray-900">{{ $package->name }}</span>
        </div>

        <div class="bg-white rounded-2xl border-2 {{ $package->name === 'Professional' ? 'border-blue-600' : 'border-gray-200' }} overflow-hidden">
            @if($package->name === 'Professional')
            <div class="bg-blue-600 text-white text-center py-2 text-sm font-semibold">
                PALING POPULER
            </div>
            @endif

            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $package->name }}</h1>
                        <p class="text-gray-600 mt-2">{{ $package->description }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-bold text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</div>
                        <div class="text-gray-500 text-sm">/bulan</div>
                        @if($package->yearly_price)
                        <div class="text-sm text-green-600 mt-1">Rp {{ number_format($package->yearly_price, 0, ',', '.') }}/tahun</div>
                        @endif
                    </div>
                </div>

                <!-- Features -->
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4">Yang Kamu Dapatkan</h2>
                    <ul class="space-y-3">
                        @if($package->ai_quota_monthly)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">{{ number_format($package->ai_quota_monthly) }} AI generation per bulan</span>
                        </li>
                        @endif
                        @if($package->caption_quota)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">{{ $package->caption_quota }} caption media sosial</span>
                        </li>
                        @endif
                        @if($package->product_description_quota)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">{{ $package->product_description_quota }} deskripsi produk</span>
                        </li>
                        @endif
                        @if(isset($package->revision_limit))
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Revisi {{ $package->revision_limit }}x</span>
                        </li>
                        @endif
                        @if(isset($package->response_time_hours))
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Response time {{ $package->response_time_hours }} jam</span>
                        </li>
                        @endif
                        @if(!empty($package->consultation_included))
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Konsultasi strategi konten</span>
                        </li>
                        @endif
                    </ul>
                </div>

                <!-- CTA -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('pricing') }}"
                       class="flex-1 text-center bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                        Pilih Paket Ini
                    </a>
                    <a href="{{ route('packages.index') }}"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition text-sm">
                        Lihat Semua Paket
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Konfirmasi Pesanan - {{ $package->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium text-gray-900">Ringkasan Paket</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Detail paket layanan yang Anda pilih.
                            </p>
                            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                <h4 class="font-bold text-blue-800 text-lg">{{ $package->name }}</h4>
                                <p class="text-blue-600 font-bold mt-2">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                                <ul class="mt-4 space-y-2 text-sm text-blue-700">
                                    <li>{{ $package->caption_quota }} Caption Media Sosial</li>
                                    <li>{{ $package->product_description_quota }} Deskripsi Produk</li>
                                    <li>Revisi {{ $package->revision_limit }}x</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form action="{{ route('orders.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6">
                                        <p class="text-sm text-gray-600 mb-4">
                                            Dengan menekan tombol konfirmasi, Anda setuju untuk berlangganan paket {{ $package->name }} selama 1 bulan. Tagihan akan dikirimkan ke email Anda.
                                        </p>
                                    </div>

                                    <!-- Future: Add project selection or creation here -->
                                </div>

                                <div class="mt-6 flex items-center justify-end">
                                    <a href="{{ route('packages.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                                    <x-primary-button>
                                        Konfirmasi Berlangganan
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Request Copywriting Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('copywriting.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri: Detail Request -->
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="type" :value="__('Jenis Konten')" />
                                    <select name="type" id="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="caption">Caption Media Sosial</option>
                                        <option value="product_description">Deskripsi Produk</option>
                                        <option value="headline">Headline / Judul</option>
                                        <option value="cta">Call to Action (CTA)</option>
                                        <option value="email">Email Marketing</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="platform" :value="__('Platform')" />
                                    <select name="platform" id="platform" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="instagram">Instagram</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="tiktok">TikTok</option>
                                        <option value="shopee">Shopee / Marketplace</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="website">Website</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('platform')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="tone" :value="__('Gaya Bahasa (Tone)')" />
                                    <select name="tone" id="tone" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="casual">Santai & Akrab (Casual)</option>
                                        <option value="formal">Profesional & Formal</option>
                                        <option value="persuasive">Sangat Persuasif / Menjual</option>
                                        <option value="funny">Lucu / Menghibur</option>
                                        <option value="emotional">Emosional / Menyentuh</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('tone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="keywords" :value="__('Kata Kunci (Opsional)')" />
                                    <x-text-input id="keywords" name="keywords" type="text" class="mt-1 block w-full" placeholder="Diskon, limited edition, free ongkir..." />
                                    <x-input-error :messages="$errors->get('keywords')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Kolom Kanan: Brief & Gambar -->
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="brief" :value="__('Brief / Detail Produk')" />
                                    <textarea id="brief" name="brief" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Jelaskan produk Anda, apa yang ingin ditonjolkan, target audience, dll..." required></textarea>
                                    <x-input-error :messages="$errors->get('brief')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="product_images" :value="__('Gambar Produk (Opsional)')" />
                                    <input type="file" name="product_images[]" id="product_images" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    <p class="mt-1 text-xs text-gray-500 italic">Maksimal 2MB per gambar.</p>
                                    <x-input-error :messages="$errors->get('product_images')" class="mt-2" />
                                </div>

                                <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 text-sm">
                                    <p class="font-bold mb-1">💡 Tips:</p>
                                    <p>Semakin detail brief yang Anda berikan, semakin akurat AI dan tim kami dalam menghasilkan konten untuk Anda.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end items-center">
                            <a href="{{ route('orders.show', $order) }}" class="text-gray-600 hover:text-gray-900 mr-4">Kembali</a>
                            <x-primary-button>
                                Buat Request & Generate AI
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

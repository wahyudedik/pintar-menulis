<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Proyek Bisnis Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="business_name" :value="__('Nama Bisnis / Proyek')" />
                                    <x-text-input id="business_name" name="business_name" type="text"
                                        class="mt-1 block w-full" placeholder="Contoh: Kopi Susu Pak De" required />
                                    <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="business_type" :value="__('Bidang Industri')" />
                                    <x-text-input id="business_type" name="business_type" type="text"
                                        class="mt-1 block w-full" placeholder="Contoh: Kuliner, Fashion..." required />
                                    <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="target_audience" :value="__('Target Audience')" />
                                    <x-text-input id="target_audience" name="target_audience" type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Contoh: Anak muda 18-25 tahun, pekerja kantoran..." />
                                    <x-input-error :messages="$errors->get('target_audience')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="business_description" :value="__('Deskripsi Bisnis')" />
                                    <textarea id="business_description" name="business_description" rows="5"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Jelaskan apa yang Anda jual, apa keunikan bisnis Anda, dll..." required></textarea>
                                    <x-input-error :messages="$errors->get('business_description')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="brand_tone" :value="__('Tone Merek (Opsional)')" />
                                    <x-text-input id="brand_tone" name="brand_tone" type="text"
                                        class="mt-1 block w-full" placeholder="Contoh: Ceria, Berwibawa, Akrab..." />
                                    <x-input-error :messages="$errors->get('brand_tone')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('projects.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4 self-center">Batal</a>
                            <x-primary-button>
                                Simpan Proyek
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

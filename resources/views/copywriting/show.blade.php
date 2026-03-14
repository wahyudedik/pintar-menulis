<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permintaan Copywriting #{{ $request->id }}
            </h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ ucfirst($request->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Utama -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Hasil AI</h3>
                            <div
                                class="bg-blue-50 p-6 rounded-lg border border-blue-100 whitespace-pre-wrap leading-relaxed text-blue-900 italic">
                                {{ $request->ai_generated_content ?? 'AI sedang memproses atau gagal menghasilkan konten. Coba lagi nanti.' }}
                            </div>

                            @if ($request->ai_generated_content)
                                <div class="mt-4 flex justify-end">
                                    <button
                                        onclick="navigator.clipboard.writeText('{{ addslashes($request->ai_generated_content) }}'); alert('Teks berhasil disalin!')"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                        Salin Hasil
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($request->final_content)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Hasil Akhir (Diedit Tim Kami)</h3>
                                <div
                                    class="bg-green-50 p-6 rounded-lg border border-green-100 whitespace-pre-wrap leading-relaxed text-green-900">
                                    {{ $request->final_content }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Form Update untuk Operator -->
                    @if (Auth::user()->id === $request->assigned_to && $request->status !== 'completed')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Panel Operator: Update Konten</h3>
                                <form action="{{ route('operator.update', $request) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4">
                                        <x-input-label for="final_content" :value="__('Hasil Copywriting Final')" />
                                        <textarea id="final_content" name="final_content" rows="10"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required>{{ $request->final_content ?? $request->ai_generated_content }}</textarea>
                                    </div>

                                    <div class="mb-6">
                                        <x-input-label for="status" :value="__('Update Status')" />
                                        <select name="status" id="status"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="in_progress"
                                                {{ $request->status === 'in_progress' ? 'selected' : '' }}>Dalam Proses
                                            </option>
                                            <option value="review"
                                                {{ $request->status === 'review' ? 'selected' : '' }}>Review Klien
                                            </option>
                                            <option value="completed"
                                                {{ $request->status === 'completed' ? 'selected' : '' }}>Selesai
                                                (Publish)</option>
                                        </select>
                                    </div>

                                    <div class="flex justify-end">
                                        <x-primary-button>
                                            Simpan Perubahan
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar Brief -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Detail Request</h3>
                            <div class="space-y-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Tipe & Platform</p>
                                    <p class="font-medium capitalize">{{ $request->type }} - {{ $request->platform }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Tone</p>
                                    <p class="font-medium capitalize">{{ $request->tone }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Brief</p>
                                    <p class="text-gray-700 mt-1 italic">{{ $request->brief }}</p>
                                </div>
                                @if ($request->keywords)
                                    <div>
                                        <p class="text-gray-500">Keywords</p>
                                        <p class="font-medium">{{ $request->keywords }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-gray-500">Tanggal</p>
                                    <p class="font-medium">{{ $request->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

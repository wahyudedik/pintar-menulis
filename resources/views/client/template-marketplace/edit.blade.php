@extends('layouts.client')

@section('title', 'Edit Template - ' . $template->title)

@section('content')
<div class="p-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('templates.index') }}" class="hover:text-blue-600">Template Marketplace</a>
        <span>/</span>
        <a href="{{ route('templates.show', $template->id) }}" class="hover:text-blue-600">{{ $template->title }}</a>
        <span>/</span>
        <span class="text-gray-900">Edit</span>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-6">Edit Template</h1>

            <form id="edit-form">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Template</label>
                        <input type="text" name="title" value="{{ $template->title }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">{{ $template->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                @foreach(['viral_clickbait','trend_fresh_ideas','event_promo','hr_recruitment','branding_tagline','education','monetization','video_monetization','freelance','digital_products'] as $cat)
                                <option value="{{ $cat }}" {{ $template->category === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                            <select name="platform" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                @foreach(['instagram','tiktok','facebook','twitter','youtube','linkedin','whatsapp','marketplace','all'] as $p)
                                <option value="{{ $p }}" {{ $template->platform === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tone</label>
                            <select name="tone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                @foreach(['casual','professional','friendly','exciting','persuasive','educational','humorous'] as $t)
                                <option value="{{ $t }}" {{ $template->tone === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten Template</label>
                        <textarea name="template_content" rows="8" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono">{{ $template->template_content }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Gunakan [VARIABEL] untuk bagian yang perlu diisi pengguna</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Petunjuk Penggunaan (opsional)</label>
                        <textarea name="format_instructions" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">{{ $template->format_instructions }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lisensi</label>
                            <select name="license_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                @foreach(['free','personal','commercial','extended'] as $l)
                                <option value="{{ $l }}" {{ $template->license_type === $l ? 'selected' : '' }}>{{ ucfirst($l) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (jika premium)</label>
                            <input type="number" name="price" value="{{ $template->price ?? 0 }}" min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_public" value="1" {{ $template->is_public ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Publik (tampil di marketplace)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_premium" value="1" {{ $template->is_premium ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Template Premium</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 pt-6 border-t border-gray-100">
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('templates.show', $template->id) }}"
                       class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.is_public = formData.has('is_public') ? true : false;
    data.is_premium = formData.has('is_premium') ? true : false;

    fetch(`/templates/{{ $template->id }}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-HTTP-Method-Override': 'PUT' },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(d => {
        if (d.success) {
            window.location.href = '{{ route("templates.show", $template->id) }}';
        } else {
            alert(d.message || 'Gagal menyimpan perubahan');
        }
    }).catch(() => alert('Terjadi kesalahan'));
});
</script>
@endsection

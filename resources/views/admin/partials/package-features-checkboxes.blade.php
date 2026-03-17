{{-- $featureGroups, $featureLabels, $activeFeatures (array of enabled keys) --}}
<h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-1 pt-4 border-t border-gray-100">
    Fitur AI yang Diizinkan
</h2>
<p class="text-xs text-gray-400 mb-4">
    Kosongkan semua = gunakan default tier berdasarkan harga paket.
    Centang manual untuk override.
</p>

<div class="space-y-5 mb-6">
    @foreach($featureGroups as $groupName => $keys)
    <div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ $groupName }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($keys as $key)
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox"
                       name="allowed_features[]"
                       value="{{ $key }}"
                       {{ in_array($key, $activeFeatures) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                <span class="text-sm text-gray-700 group-hover:text-gray-900">
                    {{ $featureLabels[$key] ?? $key }}
                </span>
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<div class="flex gap-3 mb-6">
    <button type="button" onclick="document.querySelectorAll('[name=\'allowed_features[]\']').forEach(c=>c.checked=true)"
        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
        Centang Semua
    </button>
    <button type="button" onclick="document.querySelectorAll('[name=\'allowed_features[]\']').forEach(c=>c.checked=false)"
        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
        Hapus Semua
    </button>
</div>


<div x-show="generatorType === 'google-ads'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">

    
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white text-xl">🎯</div>
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Google Ads Campaign Generator</h3>
                <p class="text-sm text-gray-500">Isi sekali, semua kebutuhan kampanye Google Ads langsung jadi & siap copy-paste</p>
            </div>
        </div>
        <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200 flex items-start gap-2">
            <span class="text-blue-600 mt-0.5">💡</span>
            <p class="text-sm text-blue-800">AI akan generate: <strong>Headlines, Descriptions, Sitelinks, Keywords (Exact/Phrase/Broad/Negative), Targeting, Budget Analysis, Campaign Score & Review</strong> — semua siap copy-paste ke Google Ads.</p>
        </div>
    </div>

    
    <div x-show="!adsResult">
        <form @submit.prevent="generateGoogleAds()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bisnis <span class="text-red-500">*</span></label>
                    <input type="text" x-model="adsForm.business_name" required maxlength="100"
                           placeholder="Contoh: Toko Baju Cantik Bu Sari"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produk / Layanan <span class="text-red-500">*</span></label>
                    <input type="text" x-model="adsForm.product_service" required maxlength="500"
                           placeholder="Contoh: Baju gamis wanita modern, harga 150rb-300rb"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience <span class="text-red-500">*</span></label>
                    <input type="text" x-model="adsForm.target_audience" required maxlength="300"
                           placeholder="Contoh: Ibu rumah tangga 25-45 tahun, suka fashion muslim"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Target <span class="text-red-500">*</span></label>
                    <input type="text" x-model="adsForm.location" required maxlength="100"
                           placeholder="Contoh: Jakarta, Jawa Barat, Indonesia"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Kampanye <span class="text-red-500">*</span></label>
                    <select x-model="adsForm.campaign_goal" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih tujuan...</option>
                        <option value="sales">💰 Penjualan / Konversi</option>
                        <option value="leads">📋 Leads / Prospek</option>
                        <option value="traffic">🌐 Traffic Website</option>
                        <option value="brand_awareness">📢 Brand Awareness</option>
                        <option value="app_installs">📱 Install Aplikasi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kampanye <span class="text-red-500">*</span></label>
                    <select x-model="adsForm.campaign_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih tipe...</option>
                        <option value="search">🔍 Search Ads</option>
                        <option value="display">🖼️ Display Ads</option>
                        <option value="shopping">🛒 Shopping Ads</option>
                        <option value="video">🎬 Video Ads (YouTube)</option>
                        <option value="performance_max">⚡ Performance Max</option>
                    </select>
                    <div x-show="adsForm.campaign_type" class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600">
                        <span x-show="adsForm.campaign_type === 'search'">✅ Muncul saat orang ketik keyword di Google. Terbaik untuk bisnis yang produknya aktif dicari.</span>
                        <span x-show="adsForm.campaign_type === 'display'">✅ Banner visual di jutaan website. Terbaik untuk brand awareness & remarketing.</span>
                        <span x-show="adsForm.campaign_type === 'shopping'">✅ Tampilkan foto + harga di Google. Terbaik untuk toko online produk fisik.</span>
                        <span x-show="adsForm.campaign_type === 'video'">✅ Iklan video di YouTube. Terbaik untuk brand storytelling & awareness.</span>
                        <span x-show="adsForm.campaign_type === 'performance_max'">✅ AI Google otomatis optimasi di semua channel. Terbaik untuk maksimalkan konversi.</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Budget Harian (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-sm text-gray-500">Rp</span>
                        <input type="number" x-model="adsForm.daily_budget" required min="10000" step="5000"
                               placeholder="50000"
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1" x-show="adsForm.daily_budget > 0">
                        ≈ Rp <span x-text="(parseInt(adsForm.daily_budget) * 30).toLocaleString('id-ID')"></span> / bulan
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Landing Page URL</label>
                    <input type="url" x-model="adsForm.landing_page_url" maxlength="500"
                           placeholder="https://tokobajucantik.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci Awal <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
                    <input type="text" x-model="adsForm.keywords_hint" maxlength="500"
                           placeholder="Contoh: baju gamis, gamis modern, baju muslim wanita"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma. AI akan expand jadi keyword lengkap.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keunggulan Produk (USP) <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
                    <input type="text" x-model="adsForm.usp" maxlength="300"
                           placeholder="Contoh: Gratis ongkir, bahan premium, garansi 30 hari"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <button type="submit" :disabled="adsLoading"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2">
                <span x-show="!adsLoading">🚀 Generate Kampanye Google Ads</span>
                <span x-show="adsLoading" class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    AI sedang menyusun kampanye... (30-60 detik)
                </span>
            </button>
        </form>
    </div>

    
    <div x-show="adsResult">
        
        <div class="mb-6 p-5 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-blue-200 text-sm mb-1">Campaign Score</p>
                    <div class="flex items-end gap-2">
                        <span class="text-5xl font-bold" x-text="adsResult?.campaign_summary?.campaign_score ?? '—'"></span>
                        <span class="text-blue-200 text-lg mb-1">/100</span>
                    </div>
                    <p class="text-blue-100 text-sm mt-1" x-text="adsResult?.campaign_summary?.name"></p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-white/10 rounded-xl p-3 text-center">
                        <p class="text-blue-200 text-xs">Est. Klik/Hari</p>
                        <p class="font-bold" x-text="adsResult?.campaign_summary?.estimated_daily_clicks ?? '—'"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 text-center">
                        <p class="text-blue-200 text-xs">Est. CPC</p>
                        <p class="font-bold" x-text="adsResult?.campaign_summary?.estimated_cpc_idr ?? '—'"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 text-center">
                        <p class="text-blue-200 text-xs">Quality Score</p>
                        <p class="font-bold" x-text="(adsResult?.campaign_summary?.quality_score_prediction ?? '—') + '/10'"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 text-center">
                        <p class="text-blue-200 text-xs">Bid Strategy</p>
                        <p class="font-bold text-xs leading-tight" x-text="adsResult?.campaign_summary?.recommended_bid_strategy ?? '—'"></p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex flex-wrap gap-1 mb-5 border-b border-gray-200">
            <template x-for="tab in adsTabs" :key="tab.id">
                <button @click="adsTab = tab.id"
                        :class="adsTab === tab.id ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 text-sm font-medium rounded-t-lg transition"
                        x-text="tab.label">
                </button>
            </template>
        </div>

        
        <div x-show="adsTab === 'copy'">
            <template x-for="(group, gi) in (adsResult?.ad_groups ?? [])" :key="gi">
                <div class="mb-6 border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-gray-900" x-text="'Ad Group ' + (gi+1) + ': ' + group.name"></span>
                            <span class="ml-2 text-xs text-gray-500" x-text="group.theme"></span>
                        </div>
                        <button @click="copyAdGroup(group)"
                                class="text-xs bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition">
                            📋 Copy Semua
                        </button>
                    </div>
                    <div class="p-4">
                        <template x-for="(ad, ai) in (group.ads ?? [])" :key="ai">
                            <div>
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="text-sm font-semibold text-gray-700">📝 Headlines (maks 30 karakter)</h5>
                                        <button @click="copyText((ad.headlines ?? []).join('\n'))" class="text-xs text-blue-600 hover:underline">Copy semua</button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        <template x-for="(hl, hi) in (ad.headlines ?? [])" :key="hi">
                                            <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 group cursor-pointer hover:border-blue-300" @click="copyText(hl)">
                                                <span class="text-sm text-gray-800 flex-1" x-text="hl"></span>
                                                <span class="text-xs ml-2 flex-shrink-0" :class="hl.length > 30 ? 'text-red-500 font-bold' : 'text-gray-400'" x-text="hl.length + '/30'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="text-sm font-semibold text-gray-700">📄 Descriptions (maks 90 karakter)</h5>
                                        <button @click="copyText((ad.descriptions ?? []).join('\n'))" class="text-xs text-blue-600 hover:underline">Copy semua</button>
                                    </div>
                                    <div class="space-y-2">
                                        <template x-for="(desc, di) in (ad.descriptions ?? [])" :key="di">
                                            <div class="flex items-start justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 group cursor-pointer hover:border-blue-300" @click="copyText(desc)">
                                                <span class="text-sm text-gray-800 flex-1" x-text="desc"></span>
                                                <span class="text-xs ml-2 flex-shrink-0" :class="desc.length > 90 ? 'text-red-500 font-bold' : 'text-gray-400'" x-text="desc.length + '/90'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="ad.display_url" class="text-xs text-gray-500">
                                    🔗 Display URL: <span class="font-mono text-green-700" x-text="ad.display_url"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        
        <div x-show="adsTab === 'keywords'">
            <template x-for="(group, gi) in (adsResult?.ad_groups ?? [])" :key="gi">
                <div class="mb-6 border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
                        <span class="font-semibold text-gray-900" x-text="'Ad Group ' + (gi+1) + ': ' + group.name"></span>
                        <button @click="copyAllKeywords(group)" class="text-xs bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition">📋 Copy Semua Keywords</button>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="text-sm font-semibold text-blue-700">🎯 Exact Match</h5>
                                <button @click="copyText((group.keywords?.exact_match ?? []).join('\n'))" class="text-xs text-blue-600 hover:underline">Copy</button>
                            </div>
                            <div class="space-y-1">
                                <template x-for="kw in (group.keywords?.exact_match ?? [])" :key="kw">
                                    <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded px-3 py-1.5 cursor-pointer hover:bg-blue-100" @click="copyText(kw)">
                                        <span class="text-sm font-mono text-blue-800" x-text="kw"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="text-sm font-semibold text-green-700">💬 Phrase Match</h5>
                                <button @click="copyText((group.keywords?.phrase_match ?? []).join('\n'))" class="text-xs text-blue-600 hover:underline">Copy</button>
                            </div>
                            <div class="space-y-1">
                                <template x-for="kw in (group.keywords?.phrase_match ?? [])" :key="kw">
                                    <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded px-3 py-1.5 cursor-pointer hover:bg-green-100" @click="copyText(kw)">
                                        <span class="text-sm font-mono text-green-800" x-text="kw"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="text-sm font-semibold text-yellow-700">🌐 Broad Match</h5>
                                <button @click="copyText((group.keywords?.broad_match_modifier ?? []).join('\n'))" class="text-xs text-blue-600 hover:underline">Copy</button>
                            </div>
                            <div class="space-y-1">
                                <template x-for="kw in (group.keywords?.broad_match_modifier ?? [])" :key="kw">
                                    <div class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded px-3 py-1.5 cursor-pointer hover:bg-yellow-100" @click="copyText(kw)">
                                        <span class="text-sm font-mono text-yellow-800" x-text="kw"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="text-sm font-semibold text-red-700">🚫 Negative Keywords</h5>
                                <button @click="copyText((group.keywords?.negative_keywords ?? []).join('\n'))" class="text-xs text-blue-600 hover:underline">Copy</button>
                            </div>
                            <div class="space-y-1">
                                <template x-for="kw in (group.keywords?.negative_keywords ?? [])" :key="kw">
                                    <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded px-3 py-1.5 cursor-pointer hover:bg-red-100" @click="copyText(kw)">
                                        <span class="text-sm font-mono text-red-700" x-text="kw"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        
        <div x-show="adsTab === 'extensions'">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">🔗 Sitelinks</h4>
                    <button @click="copyAllSitelinks()" class="text-xs bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition">📋 Copy Semua</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <template x-for="(sl, si) in (adsResult?.ad_extensions?.sitelinks ?? [])" :key="si">
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-300 cursor-pointer transition" @click="copySitelink(sl)">
                            <div class="flex items-start justify-between mb-1">
                                <span class="font-medium text-blue-700 text-sm" x-text="sl.title"></span>
                                <span class="text-xs text-gray-400 ml-2" :class="sl.title?.length > 25 ? 'text-red-500' : ''" x-text="(sl.title?.length ?? 0) + '/25'"></span>
                            </div>
                            <p class="text-xs text-gray-600" x-text="sl.description1"></p>
                            <p class="text-xs text-gray-600" x-text="sl.description2"></p>
                            <p class="text-xs text-green-600 mt-1 font-mono truncate" x-text="sl.url"></p>
                        </div>
                    </template>
                </div>
            </div>
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">📢 Callout Extensions</h4>
                    <button @click="copyText((adsResult?.ad_extensions?.callouts ?? []).join('\n'))" class="text-xs bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition">📋 Copy Semua</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="(callout, ci) in (adsResult?.ad_extensions?.callouts ?? [])" :key="ci">
                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 cursor-pointer hover:border-blue-300 transition" @click="copyText(callout)">
                            <span class="text-sm text-gray-800" x-text="callout"></span>
                            <span class="text-xs" :class="callout.length > 25 ? 'text-red-500 font-bold' : 'text-gray-400'" x-text="callout.length + '/25'"></span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="mb-6" x-show="adsResult?.ad_extensions?.structured_snippets">
                <h4 class="font-semibold text-gray-900 mb-3">📋 Structured Snippets</h4>
                <div class="border border-gray-200 rounded-xl p-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Header: <span class="text-blue-700" x-text="adsResult?.ad_extensions?.structured_snippets?.header"></span></p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="val in (adsResult?.ad_extensions?.structured_snippets?.values ?? [])" :key="val">
                            <span class="bg-blue-50 border border-blue-200 text-blue-800 text-sm px-3 py-1 rounded-lg cursor-pointer hover:bg-blue-100" @click="copyText(val)" x-text="val"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="adsTab === 'targeting'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-gray-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">📍 Lokasi & Bahasa</h4>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Lokasi Target</p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="loc in (adsResult?.targeting_settings?.locations ?? [])" :key="loc">
                                    <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded" x-text="loc"></span>
                                </template>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Bahasa</p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="lang in (adsResult?.targeting_settings?.languages ?? [])" :key="lang">
                                    <span class="bg-green-50 text-green-700 text-xs px-2 py-1 rounded" x-text="lang"></span>
                                </template>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Device</p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="dev in (adsResult?.targeting_settings?.devices ?? [])" :key="dev">
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded" x-text="dev"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">👥 Audience Targeting</h4>
                    <div class="space-y-2">
                        <template x-for="(aud, ai) in (adsResult?.targeting_settings?.audiences ?? [])" :key="ai">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded" x-text="aud.type"></span>
                                <p class="text-sm text-gray-700 mt-1" x-text="aud.segment || aud.description"></p>
                                <div x-show="aud.keywords" class="flex flex-wrap gap-1 mt-1">
                                    <template x-for="kw in (aud.keywords ?? [])" :key="kw">
                                        <span class="text-xs bg-white border border-gray-200 px-2 py-0.5 rounded" x-text="kw"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">⏰ Ad Schedule</h4>
                    <p class="text-sm text-gray-700" x-text="adsResult?.targeting_settings?.ad_schedule?.recommended_hours"></p>
                    <p class="text-xs text-gray-500 mt-1">Peak: <span x-text="adsResult?.targeting_settings?.ad_schedule?.peak_hours"></span></p>
                </div>
                <div class="border border-gray-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">👤 Demografi</h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="text-gray-500">Usia:</span> <span class="text-gray-800" x-text="adsResult?.targeting_settings?.demographic?.age"></span></p>
                        <p><span class="text-gray-500">Gender:</span> <span class="text-gray-800" x-text="adsResult?.targeting_settings?.demographic?.gender"></span></p>
                        <p><span class="text-gray-500">Income:</span> <span class="text-gray-800" x-text="adsResult?.targeting_settings?.demographic?.household_income"></span></p>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="adsTab === 'budget'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="border border-gray-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">💰 Estimasi Hasil Bulanan</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Klik / Bulan</span>
                            <span class="font-semibold text-gray-900" x-text="adsResult?.budget_allocation?.estimated_results?.monthly_clicks ?? '—'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Impresi / Bulan</span>
                            <span class="font-semibold text-gray-900" x-text="adsResult?.budget_allocation?.estimated_results?.monthly_impressions ?? '—'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Est. Konversi</span>
                            <span class="font-semibold text-green-700" x-text="adsResult?.budget_allocation?.estimated_results?.estimated_conversions ?? '—'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Est. CPA</span>
                            <span class="font-semibold text-gray-900" x-text="adsResult?.budget_allocation?.estimated_results?.estimated_cpa_idr ?? '—'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Est. ROAS</span>
                            <span class="font-semibold text-blue-700" x-text="adsResult?.budget_allocation?.estimated_results?.estimated_roas ?? '—'"></span>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">⚙️ Bid Adjustments</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">📱 Mobile</span>
                            <span class="font-medium text-gray-900" x-text="adsResult?.budget_allocation?.bid_adjustments?.mobile ?? '—'"></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">📍 Top Locations</span>
                            <span class="font-medium text-gray-900" x-text="adsResult?.budget_allocation?.bid_adjustments?.top_performing_locations ?? '—'"></span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">⏰ Peak Hours</span>
                            <span class="font-medium text-gray-900" x-text="adsResult?.budget_allocation?.bid_adjustments?.peak_hours ?? '—'"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-xl p-4" x-show="adsResult?.conversion_tracking">
                <h4 class="font-semibold text-gray-900 mb-3">📊 Conversion Tracking</h4>
                <div class="space-y-2 mb-3">
                    <template x-for="(conv, ci) in (adsResult?.conversion_tracking?.recommended_conversions ?? [])" :key="ci">
                        <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-3 py-2">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded" x-text="conv.type"></span>
                            <span class="text-sm text-gray-800" x-text="conv.name"></span>
                            <span class="text-xs text-gray-500 ml-auto" x-text="conv.value"></span>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-600 bg-yellow-50 border border-yellow-200 rounded p-2" x-text="adsResult?.conversion_tracking?.setup_instructions"></p>
            </div>
        </div>

        
        <div x-show="adsTab === 'review'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="border border-green-200 bg-green-50 rounded-xl p-4">
                    <h4 class="font-semibold text-green-800 mb-3">✅ Kelebihan Kampanye</h4>
                    <ul class="space-y-2">
                        <template x-for="(s, si) in (adsResult?.campaign_analysis?.strengths ?? [])" :key="si">
                            <li class="flex items-start gap-2 text-sm text-green-800">
                                <span class="text-green-500 mt-0.5 flex-shrink-0">✓</span>
                                <span x-text="s"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="border border-red-200 bg-red-50 rounded-xl p-4">
                    <h4 class="font-semibold text-red-800 mb-3">⚠️ Kelemahan & Risiko</h4>
                    <ul class="space-y-2">
                        <template x-for="(w, wi) in (adsResult?.campaign_analysis?.weaknesses ?? [])" :key="wi">
                            <li class="flex items-start gap-2 text-sm text-red-800">
                                <span class="text-red-500 mt-0.5 flex-shrink-0">!</span>
                                <span x-text="w"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="border border-blue-200 bg-blue-50 rounded-xl p-4">
                    <h4 class="font-semibold text-blue-800 mb-3">🚀 Peluang Optimasi</h4>
                    <ul class="space-y-2">
                        <template x-for="(o, oi) in (adsResult?.campaign_analysis?.opportunities ?? [])" :key="oi">
                            <li class="flex items-start gap-2 text-sm text-blue-800">
                                <span class="text-blue-500 mt-0.5 flex-shrink-0">→</span>
                                <span x-text="o"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="border border-purple-200 bg-purple-50 rounded-xl p-4">
                    <h4 class="font-semibold text-purple-800 mb-3">💡 Rekomendasi</h4>
                    <ul class="space-y-2">
                        <template x-for="(r, ri) in (adsResult?.campaign_analysis?.recommendations ?? [])" :key="ri">
                            <li class="flex items-start gap-2 text-sm text-purple-800">
                                <span class="text-purple-500 mt-0.5 flex-shrink-0">★</span>
                                <span x-text="r"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            
            <div class="border border-gray-200 rounded-xl p-4 mb-4" x-show="adsResult?.campaign_analysis?.campaign_type_comparison">
                <h4 class="font-semibold text-gray-900 mb-2">📊 Perbandingan Tipe Kampanye</h4>
                <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-800">Tipe yang dipilih: <span x-text="adsResult?.campaign_analysis?.campaign_type_comparison?.chosen_type"></span></p>
                    <p class="text-sm text-blue-700 mt-1" x-text="adsResult?.campaign_analysis?.campaign_type_comparison?.why_suitable"></p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <template x-for="(alt, ai) in (adsResult?.campaign_analysis?.campaign_type_comparison?.alternative_types ?? [])" :key="ai">
                        <div class="border border-gray-200 rounded-lg p-3">
                            <p class="font-medium text-gray-900 text-sm mb-2" x-text="alt.type"></p>
                            <p class="text-xs text-green-700 mb-1">✅ <span x-text="alt.pros"></span></p>
                            <p class="text-xs text-red-600 mb-1">❌ <span x-text="alt.cons"></span></p>
                            <p class="text-xs text-gray-500">📌 <span x-text="alt.when_to_use"></span></p>
                        </div>
                    </template>
                </div>
            </div>

            
            <div class="border border-gray-200 rounded-xl p-4 mb-4" x-show="(adsResult?.optimization_checklist ?? []).length > 0">
                <h4 class="font-semibold text-gray-900 mb-3">✅ Optimization Checklist</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <template x-for="(item, ii) in (adsResult?.optimization_checklist ?? [])" :key="ii">
                        <div class="flex items-start gap-2 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                            <span x-text="item"></span>
                        </div>
                    </template>
                </div>
            </div>

            
            <div class="border border-gray-200 rounded-xl p-4" x-show="(adsResult?.ab_test_suggestions ?? []).length > 0">
                <h4 class="font-semibold text-gray-900 mb-3">🧪 A/B Test Suggestions</h4>
                <div class="space-y-3">
                    <template x-for="(ab, ai) in (adsResult?.ab_test_suggestions ?? [])" :key="ai">
                        <div class="border border-gray-200 rounded-lg p-3">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Test: <span class="text-gray-800" x-text="ab.element"></span></p>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <div class="bg-blue-50 rounded p-2 text-xs"><span class="font-medium text-blue-700">Versi A:</span> <span x-text="ab.variant_a"></span></div>
                                <div class="bg-green-50 rounded p-2 text-xs"><span class="font-medium text-green-700">Versi B:</span> <span x-text="ab.variant_b"></span></div>
                            </div>
                            <p class="text-xs text-gray-500">💡 <span x-text="ab.hypothesis"></span></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        
        <div class="mt-6 flex flex-wrap gap-3 pt-4 border-t border-gray-200">
            <button @click="copyFullCampaign()"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 transition text-sm font-medium flex items-center gap-2">
                📋 Copy Semua Kampanye
            </button>
            <button @click="adsResult = null; adsTab = 'copy'"
                    class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 transition text-sm font-medium">
                🔄 Generate Ulang
            </button>
        </div>
    </div>

    
    <div x-show="adsCopied" x-transition
         class="fixed bottom-6 right-6 bg-gray-900 text-white px-4 py-2 rounded-xl text-sm shadow-lg z-50">
        ✅ Disalin ke clipboard!
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/google-ads-generator.blade.php ENDPATH**/ ?>
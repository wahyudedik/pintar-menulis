@extends('layouts.client')

@section('title', 'AI Analisis Dokumen')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
@endpush

@section('content')
<div class="p-6" x-data="aiAnalysis()" x-init="init()">

    {{-- Toast Notification --}}
    <div x-show="notificationVisible" x-cloak x-transition
         :class="notificationType === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed bottom-6 right-6 z-50 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium max-w-xs"
         x-text="notificationMessage">
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">AI Analisis Dokumen</h1>
            <p class="text-sm text-gray-500 mt-1">Analisis keuangan, ebook, dan tren pembaca dengan AI</p>
        </div>
        <a href="{{ route('ai.generator') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
            ← Kembali ke AI Generator
        </a>
    </div>

    {{-- Tab Selector --}}
    <div class="mb-6 flex justify-center">
        <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50 gap-1">
            <button @click="activeTab = 'financial'"
                    :class="activeTab === 'financial' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-5 py-2.5 rounded-md text-sm font-medium transition">
                📊 Analisis Keuangan
            </button>
            <button @click="activeTab = 'ebook'"
                    :class="activeTab === 'ebook' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-5 py-2.5 rounded-md text-sm font-medium transition">
                📚 Analisis Ebook
            </button>
            <button @click="activeTab = 'reader_trend'"
                    :class="activeTab === 'reader_trend' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-5 py-2.5 rounded-md text-sm font-medium transition">
                📖 Tren Pembaca
            </button>
        </div>
    </div>

    {{-- 📊 Analisis Keuangan --}}
    <div x-show="activeTab === 'financial'" x-cloak class="space-y-6">
        @include('client.partials.financial-analysis')
    </div>

    {{-- 📚 Analisis Ebook --}}
    <div x-show="activeTab === 'ebook'" x-cloak class="space-y-6">
        @include('client.partials.ebook-analysis')
    </div>

    {{-- 📖 Tren Pembaca --}}
    <div x-show="activeTab === 'reader_trend'" x-cloak class="space-y-6">
        @include('client.partials.reader-trend')
    </div>

</div>

<script>
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }
        return new Promise((resolve, reject) => {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); resolve(); }
            catch (e) { reject(e); }
            finally { document.body.removeChild(ta); }
        });
    }

    function handleFeatureLocked(data) {
        if (data && data.feature_locked) {
            alert(data.message || 'Fitur ini tidak tersedia di paket kamu. Silakan upgrade.');
            return true;
        }
        return false;
    }

    function aiAnalysis() {
        return {
            activeTab: 'financial',
            notificationVisible: false,
            notificationType: 'success',
            notificationMessage: '',

            // 📊 Financial Analysis state
            financialForm: { analysis_type: 'stock_chart', context: '' },
            financialImages: [],
            financialDocs: [],
            financialLoading: false,
            financialResult: null,
            financialError: null,
            financialCopied: false,

            // 📚 Ebook Analysis state
            ebookForm: { analysis_type: 'full', context: '' },
            ebookDocs: [],
            ebookLoading: false,
            ebookResult: null,
            ebookError: null,
            ebookCopied: false,

            // 📖 Reader Trend state
            readerTrendForm: { genre: '', platform: '', context: '' },
            readerTrendLoading: false,
            readerTrendResult: null,
            readerTrendError: null,
            readerTrendCopied: false,

            init() {},

            showNotification(message, type = 'success') {
                this.notificationMessage = message;
                this.notificationType = type;
                this.notificationVisible = true;
                setTimeout(() => { this.notificationVisible = false; }, 3000);
            },

            // ─── Financial Analysis ──────────────────────────────────────
            async analyzeFinancial() {
                if (this.financialImages.length === 0 && this.financialDocs.length === 0) {
                    this.showNotification('❌ Upload minimal satu file (chart atau PDF)', 'error');
                    return;
                }
                this.financialLoading = true;
                this.financialResult = null;
                this.financialError = null;

                const formData = new FormData();
                formData.append('analysis_type', this.financialForm.analysis_type);
                formData.append('context', this.financialForm.context);
                this.financialImages.forEach((f, i) => formData.append(`images[${i}]`, f));
                this.financialDocs.forEach((f, i) => formData.append(`documents[${i}]`, f));

                try {
                    const response = await fetch('/api/ai/analyze-financial', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData,
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) {
                        this.financialResult = data.analysis;
                        this.showNotification('✅ Analisis selesai!', 'success');
                    } else {
                        this.financialError = data.message || 'Terjadi kesalahan';
                        this.showNotification('❌ ' + this.financialError, 'error');
                    }
                } catch (e) {
                    this.financialError = 'Terjadi kesalahan jaringan';
                    this.showNotification('❌ Terjadi kesalahan jaringan', 'error');
                } finally { this.financialLoading = false; }
            },
            financialAddImages(event) { this.financialImages = [...this.financialImages, ...Array.from(event.target.files)].slice(0, 5); },
            financialAddDocs(event) { this.financialDocs = [...this.financialDocs, ...Array.from(event.target.files)].slice(0, 5); },
            financialRemoveImage(idx) { this.financialImages.splice(idx, 1); },
            financialRemoveDoc(idx) { this.financialDocs.splice(idx, 1); },
            financialCopyResult() {
                copyToClipboard(this.financialResult || '').then(() => {
                    this.financialCopied = true;
                    setTimeout(() => { this.financialCopied = false; }, 2000);
                });
            },

            // ─── Ebook Analysis ──────────────────────────────────────────
            ebookAddDocs(event) { this.ebookDocs = [...this.ebookDocs, ...Array.from(event.target.files)].slice(0, 3); },
            ebookRemoveDoc(idx) { this.ebookDocs.splice(idx, 1); },
            async analyzeEbook() {
                if (this.ebookDocs.length === 0) {
                    this.showNotification('⚠️ Upload minimal satu file PDF', 'error');
                    return;
                }
                this.ebookLoading = true;
                this.ebookResult = null;
                this.ebookError = null;

                const formData = new FormData();
                formData.append('analysis_type', this.ebookForm.analysis_type);
                formData.append('context', this.ebookForm.context || '');
                this.ebookDocs.forEach(f => formData.append('documents[]', f));

                try {
                    const resp = await fetch('/api/ai/analyze-ebook', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData,
                    });
                    const data = await resp.json();
                    if (!resp.ok) {
                        if (handleFeatureLocked(data)) return;
                        this.ebookError = data.message || 'Terjadi kesalahan';
                        this.showNotification('❌ ' + this.ebookError, 'error');
                    } else if (data.success) {
                        this.ebookResult = data.analysis;
                        this.showNotification('✅ Analisis ebook selesai!', 'success');
                    } else {
                        this.ebookError = data.message || 'Terjadi kesalahan';
                        this.showNotification('❌ ' + this.ebookError, 'error');
                    }
                } catch (e) {
                    this.ebookError = 'Terjadi kesalahan jaringan';
                    this.showNotification('❌ Terjadi kesalahan jaringan', 'error');
                } finally { this.ebookLoading = false; }
            },
            ebookCopyResult() {
                copyToClipboard(this.ebookResult || '').then(() => {
                    this.ebookCopied = true;
                    setTimeout(() => { this.ebookCopied = false; }, 2000);
                });
            },

            // ─── Reader Trend Analysis ───────────────────────────────────
            async analyzeReaderTrend() {
                this.readerTrendLoading = true;
                this.readerTrendResult = null;
                this.readerTrendError = null;

                try {
                    const resp = await fetch('/api/ai/analyze-reader-trend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            genre: this.readerTrendForm.genre,
                            platform: this.readerTrendForm.platform,
                            context: this.readerTrendForm.context,
                        }),
                    });
                    const data = await resp.json();
                    if (!resp.ok) {
                        if (handleFeatureLocked(data)) return;
                        this.readerTrendError = data.message || 'Terjadi kesalahan';
                        this.showNotification('❌ ' + this.readerTrendError, 'error');
                    } else if (data.success) {
                        this.readerTrendResult = data.analysis;
                        this.showNotification('✅ Analisis tren selesai!', 'success');
                    } else {
                        this.readerTrendError = data.message || 'Terjadi kesalahan';
                        this.showNotification('❌ ' + this.readerTrendError, 'error');
                    }
                } catch (e) {
                    this.readerTrendError = 'Terjadi kesalahan jaringan';
                    this.showNotification('❌ Terjadi kesalahan jaringan', 'error');
                } finally { this.readerTrendLoading = false; }
            },
            readerTrendCopyResult() {
                copyToClipboard(this.readerTrendResult || '').then(() => {
                    this.readerTrendCopied = true;
                    setTimeout(() => { this.readerTrendCopied = false; }, 2000);
                });
            },
        }
    }
</script>
@endsection

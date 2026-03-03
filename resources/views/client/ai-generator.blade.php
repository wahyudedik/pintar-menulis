@extends('layouts.client')

@section('title', 'AI Generator')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="p-6" x-data="aiGenerator()">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">AI Copywriting Generator</h1>
        <p class="text-sm text-gray-500 mt-1">Generate copywriting berkualitas dengan Google Gemini AI</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Form Input -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <form @submit.prevent="generateCopywriting">
                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-600">*</span>
                        </label>
                        <select x-model="form.category" @change="updateSubcategories" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Kategori</option>
                            <option value="website_landing">Website & Landing Page</option>
                            <option value="ads">Iklan (Ads)</option>
                            <option value="social_media">Social Media Content</option>
                            <option value="marketplace">Marketplace</option>
                            <option value="email_whatsapp">Email & WhatsApp Marketing</option>
                            <option value="proposal_company">Proposal & Company Profile</option>
                            <option value="personal_branding">Personal Branding</option>
                            <option value="ux_writing">UX Writing</option>
                        </select>
                    </div>

                    <!-- Subcategory -->
                    <div class="mb-4" x-show="subcategories.length > 0" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Konten <span class="text-red-600">*</span>
                        </label>
                        <select x-model="form.subcategory" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Jenis Konten</option>
                            <template x-for="sub in subcategories" :key="sub.value">
                                <option :value="sub.value" x-text="sub.label"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Platform -->
                    <div class="mb-4" x-show="form.category === 'social_media' || form.category === 'ads'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                        <select x-model="form.platform"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="tiktok">TikTok</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="twitter">Twitter/X</option>
                        </select>
                    </div>

                    <!-- Brief -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Brief / Deskripsi <span class="text-red-600">*</span>
                        </label>
                        <textarea x-model="form.brief" required rows="5"
                                  placeholder="Jelaskan produk/jasa, target audience, dan keunikan Anda..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Semakin detail, semakin baik hasilnya</p>
                    </div>

                    <!-- Tone -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tone / Gaya Bahasa</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'casual' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="casual" class="mr-2">
                                <span class="text-sm">Casual</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'formal' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="formal" class="mr-2">
                                <span class="text-sm">Formal</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'persuasive' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="persuasive" class="mr-2">
                                <span class="text-sm">Persuasive</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'funny' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="funny" class="mr-2">
                                <span class="text-sm">Funny</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'emotional' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="emotional" class="mr-2">
                                <span class="text-sm">Emotional</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'educational' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="educational" class="mr-2">
                                <span class="text-sm">Educational</span>
                            </label>
                        </div>
                    </div>

                    <!-- Keywords -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keywords (Optional)</label>
                        <input type="text" x-model="form.keywords"
                               placeholder="Pisahkan dengan koma"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Generate Button -->
                    <button type="submit" :disabled="loading"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!loading">Generate dengan AI</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Result -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Generate</h3>
                
                <div x-show="!result && !loading" class="text-center py-12 text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm">Hasil akan muncul di sini</p>
                </div>

                <div x-show="loading" x-cloak class="text-center py-12">
                    <svg class="animate-spin h-10 w-10 mx-auto text-blue-600 mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-gray-600">AI sedang bekerja...</p>
                </div>

                <div x-show="result && !loading" x-cloak>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 max-h-96 overflow-y-auto">
                        <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result"></pre>
                    </div>
                    
                    <div class="space-y-2">
                        <button @click="copyToClipboard" 
                                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-sm">
                            <span x-show="!copied">Copy to Clipboard</span>
                            <span x-show="copied">Copied!</span>
                        </button>
                        <button @click="reset"
                                class="w-full border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                            Generate Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function aiGenerator() {
        return {
            form: {
                category: '',
                subcategory: '',
                platform: 'instagram',
                brief: '',
                tone: 'casual',
                keywords: ''
            },
            subcategories: [],
            loading: false,
            result: '',
            copied: false,
            
            subcategoryOptions: {
                website_landing: [
                    {value: 'headline', label: 'Headline Halaman Utama'},
                    {value: 'subheadline', label: 'Subheadline'},
                    {value: 'service_description', label: 'Deskripsi Layanan'},
                    {value: 'about_us', label: 'Tentang Kami'},
                    {value: 'cta', label: 'Call To Action'},
                    {value: 'faq', label: 'FAQ'},
                    {value: 'pricing_page', label: 'Halaman Pricing'},
                    {value: 'product_description', label: 'Deskripsi Produk Digital'}
                ],
                ads: [
                    {value: 'headline', label: 'Headline Iklan'},
                    {value: 'body_text', label: 'Body Text'},
                    {value: 'hook_3sec', label: 'Hook 3 Detik Pertama'},
                    {value: 'video_script', label: 'Script Video Promosi'},
                    {value: 'caption_promo', label: 'Caption Promosi'}
                ],
                social_media: [
                    {value: 'instagram_caption', label: 'Caption Instagram'},
                    {value: 'thread_edukasi', label: 'Thread Edukasi'},
                    {value: 'storytelling', label: 'Konten Storytelling'},
                    {value: 'soft_selling', label: 'Soft Selling'},
                    {value: 'hard_selling', label: 'Hard Selling'},
                    {value: 'reels_tiktok_script', label: 'Script Reels/TikTok'},
                    {value: 'educational_content', label: 'Konten Edukasi'}
                ],
                marketplace: [
                    {value: 'product_title', label: 'Judul Produk'},
                    {value: 'product_description', label: 'Deskripsi Produk'},
                    {value: 'bullet_benefits', label: 'Bullet Benefit'},
                    {value: 'faq', label: 'FAQ Produk'},
                    {value: 'auto_reply', label: 'Auto-Reply Chat'}
                ],
                email_whatsapp: [
                    {value: 'broadcast_promo', label: 'Broadcast Promo'},
                    {value: 'follow_up', label: 'Follow Up Calon Client'},
                    {value: 'partnership_offer', label: 'Penawaran Kerja Sama'},
                    {value: 'payment_reminder', label: 'Reminder Pembayaran'},
                    {value: 'closing_script', label: 'Script Closing'},
                    {value: 'welcome_email', label: 'Welcome Email'},
                    {value: 'abandoned_cart', label: 'Abandoned Cart'}
                ],
                proposal_company: [
                    {value: 'project_proposal', label: 'Proposal Proyek'},
                    {value: 'company_profile', label: 'Company Profile'},
                    {value: 'service_offer', label: 'Penawaran Jasa'},
                    {value: 'pitch_deck', label: 'Pitch Deck SaaS'},
                    {value: 'investor_presentation', label: 'Presentasi Investor'}
                ],
                personal_branding: [
                    {value: 'instagram_bio', label: 'Bio Instagram'},
                    {value: 'linkedin_summary', label: 'LinkedIn Summary'},
                    {value: 'freelance_profile', label: 'Deskripsi Profil Freelance'},
                    {value: 'portfolio', label: 'Portofolio'},
                    {value: 'about_me', label: 'About Me'}
                ],
                ux_writing: [
                    {value: 'feature_name', label: 'Nama Fitur'},
                    {value: 'feature_description', label: 'Deskripsi Fitur'},
                    {value: 'onboarding_message', label: 'Onboarding Message'},
                    {value: 'notification', label: 'Notifikasi Dalam Aplikasi'},
                    {value: 'error_message', label: 'Error Message'},
                    {value: 'empty_state', label: 'Empty State'},
                    {value: 'success_message', label: 'Success Message'},
                    {value: 'button_copy', label: 'Button Copy'}
                ]
            },
            
            updateSubcategories() {
                this.subcategories = this.subcategoryOptions[this.form.category] || [];
                this.form.subcategory = '';
            },
            
            async generateCopywriting() {
                this.loading = true;
                this.result = '';
                
                try {
                    console.log('Sending request with data:', this.form);
                    
                    const response = await fetch('/api/ai/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    
                    console.log('Response status:', response.status);
                    
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        this.result = data.result;
                        console.log('Success! Result:', this.result);
                    } else {
                        const errorMessage = data.message || 'Terjadi kesalahan saat generate konten';
                        console.error('API returned error:', errorMessage);
                        
                        // Show user-friendly error
                        if (errorMessage.includes('API key') || errorMessage.includes('tidak valid') || errorMessage.includes('expired')) {
                            alert('⚠️ API Key Gemini Tidak Valid\n\n' + 
                                  'API key sudah expired atau tidak valid.\n\n' +
                                  'Solusi:\n' +
                                  '1. Generate API key baru di:\n' +
                                  '   https://aistudio.google.com/app/apikey\n\n' +
                                  '2. Update di file .env:\n' +
                                  '   GEMINI_API_KEY=your_new_key\n\n' +
                                  '3. Clear cache: php artisan config:clear\n\n' +
                                  'Hubungi administrator untuk bantuan.');
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                        
                        console.error('API Error:', data);
                    }
                } catch (error) {
                    console.error('Generate Error:', error);
                    console.error('Error stack:', error.stack);
                    alert('Terjadi kesalahan: ' + (error.message || 'Tidak dapat terhubung ke server'));
                } finally {
                    this.loading = false;
                }
            },
            
            copyToClipboard() {
                try {
                    // Modern method
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(this.result)
                            .then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            })
                            .catch(err => {
                                console.error('Clipboard error:', err);
                                this.fallbackCopy();
                            });
                    } else {
                        // Fallback method
                        this.fallbackCopy();
                    }
                } catch (err) {
                    console.error('Copy error:', err);
                    this.fallbackCopy();
                }
            },
            
            fallbackCopy() {
                try {
                    // Create temporary textarea
                    const textarea = document.createElement('textarea');
                    textarea.value = this.result;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    
                    const successful = document.execCommand('copy');
                    document.body.removeChild(textarea);
                    
                    if (successful) {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } else {
                        alert('Gagal copy. Silakan copy manual.');
                    }
                } catch (err) {
                    console.error('Fallback copy error:', err);
                    alert('Gagal copy. Silakan copy manual dengan Ctrl+C');
                }
            },
            
            reset() {
                this.result = '';
                this.form.brief = '';
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

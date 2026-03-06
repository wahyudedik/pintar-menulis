@extends('layouts.client')

@section('title', 'Browse Operators')

@section('content')
<div class="p-6" x-data="browseOperators()">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Browse Operators</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih operator terbaik untuk project Anda</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Kategori</label>
                <select x-model="filters.category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="social_media">Social Media</option>
                    <option value="landing_page">Landing Page</option>
                    <option value="proposal">Proposal</option>
                    <option value="ux_writing">UX Writing</option>
                    <option value="content_writing">Content Writing</option>
                    <option value="ads">Ads</option>
                    <option value="marketplace">Marketplace</option>
                    <option value="company_profile">Company Profile</option>
                    <option value="website">Website</option>
                    <option value="email_marketing">Email Marketing</option>
                    <option value="personal_branding">Personal Branding</option>
                    <option value="product_description">Product Description</option>
                    <option value="seo">SEO</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Rating</label>
                <select x-model="filters.rating" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="4.5">4.5+</option>
                    <option value="4.0">4.0+</option>
                    <option value="3.5">3.5+</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Harga</label>
                <select x-model="filters.price" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="low">< Rp 100k</option>
                    <option value="medium">Rp 100k - 500k</option>
                    <option value="high">> Rp 500k</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Urutkan</label>
                <select x-model="filters.sort" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="rating">Rating Tertinggi</option>
                    <option value="orders">Paling Banyak Order</option>
                    <option value="price_low">Harga Terendah</option>
                    <option value="price_high">Harga Tertinggi</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Operators Grid -->
    <div class="grid md:grid-cols-3 gap-4">
        <template x-for="operator in filteredOperators" :key="operator.id">
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:border-blue-500 transition">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold">
                            <span x-text="operator.name.charAt(0)"></span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-gray-900" x-text="operator.name"></h3>
                            <div class="flex items-center mt-1">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="text-xs font-semibold ml-1" x-text="Number(operator.rating || 0).toFixed(1)"></span>
                                <span class="text-xs text-gray-500 ml-1">(<span x-text="operator.reviews || 0"></span>)</span>
                            </div>
                        </div>
                    </div>
                    <span x-show="operator.available" class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Available</span>
                </div>

                <p x-show="operator.bio" class="text-xs text-gray-600 mb-3 line-clamp-2" x-text="operator.bio"></p>

                <div x-show="operator.specializations && operator.specializations.length > 0" class="mb-3">
                    <div class="flex flex-wrap gap-1">
                        <template x-for="spec in (operator.specializations || []).slice(0, 3)" :key="spec">
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded" x-text="spec"></span>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <div>
                        <div class="text-xs text-gray-500">Mulai dari</div>
                        <div class="text-sm font-semibold text-gray-900">
                            Rp <span x-text="(operator.base_price || 50000).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                    <button @click="requestOrder(operator.id)" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        Request Order
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div x-show="filteredOperators.length === 0" class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p class="text-sm text-gray-500">Tidak ada operator yang sesuai dengan filter</p>
    </div>

    <!-- Request Order Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showModal = false">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Request Order</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitOrder">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select x-model="orderForm.category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            <option value="social_media">Social Media Content</option>
                            <option value="landing_page">Landing Page</option>
                            <option value="proposal">Proposal</option>
                            <option value="ux_writing">UX Writing</option>
                            <option value="content_writing">Content Writing</option>
                            <option value="ads">Iklan (Ads)</option>
                            <option value="marketplace">Marketplace</option>
                            <option value="company_profile">Company Profile</option>
                            <option value="website">Website</option>
                            <option value="email_marketing">Email Marketing</option>
                            <option value="personal_branding">Personal Branding</option>
                            <option value="product_description">Product Description</option>
                            <option value="seo">SEO</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Brief</label>
                        <textarea x-model="orderForm.brief" required rows="4" 
                                  placeholder="Jelaskan kebutuhan Anda..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Budget (Rp)</label>
                        <input type="number" x-model="orderForm.budget" required min="50000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                        <input type="date" x-model="orderForm.deadline" required :min="minDate"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                        <span x-text="errorMessage"></span>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" :disabled="submitting" 
                                class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                            <span x-show="!submitting">Kirim Request</span>
                            <span x-show="submitting">Mengirim...</span>
                        </button>
                        <button type="button" @click="showModal = false" 
                                class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function browseOperators() {
    return {
        operators: @json($operators),
        filters: {
            category: '',
            rating: '',
            price: '',
            sort: 'rating'
        },
        showModal: false,
        selectedOperator: null,
        submitting: false,
        errorMessage: '',
        orderForm: {
            category: '',
            brief: '',
            budget: '',
            deadline: ''
        },
        
        get minDate() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            return tomorrow.toISOString().split('T')[0];
        },
        
        get filteredOperators() {
            let result = [...this.operators];
            
            // Filter by category
            if (this.filters.category) {
                result = result.filter(op => {
                    if (!op.specializations || !Array.isArray(op.specializations)) return false;
                    
                    // Map filter values to possible specialization names (case insensitive)
                    const categoryMap = {
                        'social_media': ['Social Media', 'Instagram', 'LinkedIn', 'Bio Instagram'],
                        'landing_page': ['Landing Page'],
                        'proposal': ['Proposal'],
                        'ux_writing': ['UX Writing'],
                        'content_writing': ['Content Writing'],
                        'ads': ['Ads', 'Iklan'],
                        'marketplace': ['Marketplace'],
                        'company_profile': ['Company Profile'],
                        'website': ['Website'],
                        'email_marketing': ['Email Marketing'],
                        'personal_branding': ['Personal Branding'],
                        'product_description': ['Product Description'],
                        'seo': ['SEO']
                    };
                    
                    const searchTerms = categoryMap[this.filters.category] || [this.filters.category];
                    
                    // Check if any specialization matches any search term (case insensitive)
                    return op.specializations.some(spec => 
                        searchTerms.some(term => 
                            spec.toLowerCase().trim() === term.toLowerCase().trim()
                        )
                    );
                });
            }
            
            // Filter by rating
            if (this.filters.rating) {
                const minRating = parseFloat(this.filters.rating);
                result = result.filter(op => (Number(op.rating) || 0) >= minRating);
            }
            
            // Filter by price
            if (this.filters.price) {
                result = result.filter(op => {
                    const price = op.base_price || 0;
                    if (this.filters.price === 'low') return price < 100000;
                    if (this.filters.price === 'medium') return price >= 100000 && price <= 500000;
                    if (this.filters.price === 'high') return price > 500000;
                    return true;
                });
            }
            
            // Sort
            result.sort((a, b) => {
                if (this.filters.sort === 'rating') {
                    return (Number(b.rating) || 0) - (Number(a.rating) || 0);
                } else if (this.filters.sort === 'orders') {
                    return (b.completed_orders || 0) - (a.completed_orders || 0);
                } else if (this.filters.sort === 'price_low') {
                    return (a.base_price || 0) - (b.base_price || 0);
                } else if (this.filters.sort === 'price_high') {
                    return (b.base_price || 0) - (a.base_price || 0);
                }
                return 0;
            });
            
            return result;
        },
        
        requestOrder(operatorId) {
            this.selectedOperator = operatorId;
            this.showModal = true;
            this.errorMessage = '';
            this.orderForm = {
                category: '',
                brief: '',
                budget: '',
                deadline: ''
            };
        },
        
        async submitOrder() {
            this.submitting = true;
            this.errorMessage = '';
            
            try {
                const formData = new FormData();
                formData.append('operator_id', this.selectedOperator);
                formData.append('category', this.orderForm.category);
                formData.append('brief', this.orderForm.brief);
                formData.append('budget', this.orderForm.budget);
                formData.append('deadline', this.orderForm.deadline);
                
                const response = await fetch('{{ route('request.order') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // ESCROW: Redirect to payment page instead of orders list
                    window.location.href = data.redirect_url;
                } else {
                    this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                }
            } catch (error) {
                this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                console.error('Error:', error);
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

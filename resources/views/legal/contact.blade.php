@extends('legal.layout')

@section('title', 'Contact Us')
@section('description', 'Hubungi Noteds - Customer Support & Bantuan')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">Contact Us</h1>
<p class="text-gray-600 mb-8">Kami siap membantu Anda! Hubungi kami melalui channel di bawah ini.</p>

<div class="space-y-8">
    <!-- Customer Support -->
    <div class="bg-blue-50 rounded-lg p-6 border-2 border-blue-200">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Customer Support</h2>
                <p class="text-gray-700 mb-4">Untuk bantuan teknis, pertanyaan umum, atau keluhan</p>
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <a href="mailto:info@noteds.com" class="text-blue-600 hover:underline">info@noteds.com</a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <a href="https://wa.me/6281654932383?text=Halo,%20saya%20butuh%20bantuan" target="_blank" class="text-blue-600 hover:underline">+62 816-5493-2383</a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Senin-Jumat, 09:00-17:00 WIB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadi Operator -->
    <div class="bg-green-50 rounded-lg p-6 border-2 border-green-200">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Daftar Jadi Operator Freelance</h2>
                <p class="text-gray-700 mb-4">Punya skill copywriting? Gabung jadi operator caption freelance dan dapatkan penghasilan tambahan!</p>
                <div class="space-y-3">
                    <div class="bg-white rounded-lg p-4 border border-green-300">
                        <h3 class="font-semibold text-gray-900 mb-2">Keuntungan Jadi Operator:</h3>
                        <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                            <li>Kerja remote, fleksibel waktu</li>
                            <li>Penghasilan 90% dari setiap order (platform komisi 10%)</li>
                            <li>Sistem escrow yang aman</li>
                            <li>Akses ke client UMKM Indonesia</li>
                            <li>Pelatihan dan support dari tim</li>
                        </ul>
                    </div>
                    <a href="https://wa.me/6281654932383?text=Halo,%20saya%20ingin%20daftar%20jadi%20operator%20caption%20freelance" 
                       target="_blank"
                       class="inline-flex items-center space-x-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>Daftar via WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales & Partnership -->
    <div class="bg-purple-50 rounded-lg p-6 border-2 border-purple-200">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Sales & Partnership</h2>
                <p class="text-gray-700 mb-4">Untuk kerjasama bisnis, partnership, atau paket enterprise</p>
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <a href="mailto:info@noteds.com" class="text-blue-600 hover:underline">info@noteds.com</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund & Billing -->
    <div class="bg-yellow-50 rounded-lg p-6 border-2 border-yellow-200">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Refund & Billing</h2>
                <p class="text-gray-700 mb-4">Untuk pertanyaan tentang pembayaran, invoice, atau refund</p>
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <a href="mailto:info@noteds.com" class="text-blue-600 hover:underline">info@noteds.com</a>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        Lihat <a href="{{ route('refund-policy') }}" class="text-blue-600 hover:underline">Refund Policy</a> untuk informasi lengkap
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Office Address -->
    <div class="bg-gray-50 rounded-lg p-6 border-2 border-gray-200">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Alamat</h2>
                <p class="text-gray-700">
                    Indonesia<br>
                    <span class="text-sm text-gray-600">Remote-first company</span>
                </p>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="bg-white rounded-lg p-6 border-2 border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Frequently Asked Questions</h2>
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">Berapa lama response time?</h3>
                <p class="text-sm text-gray-700">Kami berusaha merespon dalam 1-24 jam di hari kerja (Senin-Jumat).</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">Apakah ada live chat?</h3>
                <p class="text-sm text-gray-700">Saat ini kami menggunakan WhatsApp untuk komunikasi real-time. Lebih cepat dan personal!</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">Bagaimana cara melaporkan bug?</h3>
                <p class="text-sm text-gray-700">Kirim email ke info@noteds.com dengan detail bug (screenshot, langkah reproduksi, dll).</p>
            </div>
        </div>
    </div>
</div>
@endsection

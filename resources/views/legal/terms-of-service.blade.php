@extends('legal.layout')

@section('title', 'Terms of Service')
@section('description', 'Syarat dan Ketentuan Layanan Smart Copy SMK')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">Terms of Service</h1>
<p class="text-sm text-gray-500 mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>

<div class="prose prose-blue max-w-none">
    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">1. Penerimaan Syarat</h2>
    <p class="text-gray-700 mb-4">
        Dengan mengakses dan menggunakan Smart Copy SMK ("Platform", "Layanan", "kami"), Anda setuju untuk terikat oleh Syarat dan Ketentuan ini. Jika Anda tidak setuju dengan syarat ini, mohon untuk tidak menggunakan layanan kami.
    </p>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">2. Deskripsi Layanan</h2>
    <p class="text-gray-700 mb-4">
        Smart Copy SMK adalah platform AI Caption Generator yang menyediakan:
    </p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Generate caption otomatis menggunakan AI (Google Gemini)</li>
        <li>Template caption untuk berbagai industri UMKM</li>
        <li>Layanan operator freelance untuk pembuatan caption custom</li>
        <li>Analytics dan tracking performa caption</li>
        <li>Brand voice management</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">3. Akun Pengguna</h2>
    
    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.1 Pendaftaran</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Anda harus berusia minimal 17 tahun untuk menggunakan layanan ini</li>
        <li>Informasi yang Anda berikan harus akurat dan lengkap</li>
        <li>Anda bertanggung jawab untuk menjaga kerahasiaan password</li>
        <li>Satu akun hanya untuk satu pengguna (tidak boleh berbagi akun)</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.2 Keamanan Akun</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Anda bertanggung jawab atas semua aktivitas yang terjadi di akun Anda</li>
        <li>Segera laporkan jika ada penggunaan tidak sah</li>
        <li>Kami tidak bertanggung jawab atas kerugian akibat kelalaian Anda menjaga keamanan akun</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">4. Penggunaan Layanan</h2>
    
    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.1 Penggunaan yang Diizinkan</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Menggunakan layanan untuk tujuan bisnis yang sah</li>
        <li>Generate caption untuk produk/jasa Anda sendiri</li>
        <li>Menyimpan dan menggunakan caption yang dihasilkan</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.2 Penggunaan yang Dilarang</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Menggunakan layanan untuk konten ilegal, menyesatkan, atau melanggar hukum</li>
        <li>Generate caption untuk produk/jasa yang melanggar hukum (narkoba, judi, pornografi, dll)</li>
        <li>Menyalahgunakan atau memanipulasi sistem (spam, bot, scraping)</li>
        <li>Menjual kembali atau mendistribusikan layanan tanpa izin</li>
        <li>Reverse engineering atau mencoba mengakses source code</li>
        <li>Mengganggu atau merusak infrastruktur platform</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">5. Paket dan Pembayaran</h2>
    
    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">5.1 Paket Gratis</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>5 variasi caption per hari</li>
        <li>Akses ke semua template</li>
        <li>Tidak ada biaya tersembunyi</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">5.2 Paket Premium</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Harga dan fitur sesuai dengan paket yang dipilih</li>
        <li>Pembayaran dilakukan di muka (prepaid)</li>
        <li>Tidak ada auto-renewal (harus perpanjang manual)</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">5.3 Layanan Operator Freelance</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Pembayaran dilakukan sebelum operator mulai mengerjakan</li>
        <li>Uang akan di-hold oleh platform (escrow system)</li>
        <li>Uang akan dirilis ke operator setelah client approve hasil pekerjaan</li>
        <li>Platform mengambil komisi 10% dari setiap transaksi</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">6. Refund dan Pembatalan</h2>
    <p class="text-gray-700 mb-4">
        Lihat <a href="{{ route('refund-policy') }}" class="text-blue-600 hover:underline">Refund Policy</a> untuk detail lengkap tentang kebijakan pengembalian dana.
    </p>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">7. Hak Kekayaan Intelektual</h2>
    
    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">7.1 Konten Platform</h3>
    <p class="text-gray-700 mb-4">
        Semua konten di platform (logo, design, template, code) adalah milik Smart Copy SMK dan dilindungi oleh hak cipta.
    </p>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">7.2 Konten yang Anda Buat</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Caption yang dihasilkan menjadi milik Anda</li>
        <li>Anda bebas menggunakan, memodifikasi, dan mempublikasikan caption tersebut</li>
        <li>Anda bertanggung jawab atas penggunaan caption (pastikan tidak melanggar hak cipta pihak lain)</li>
        <li>Kami dapat menggunakan caption Anda (tanpa identitas) untuk meningkatkan AI model</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">8. Disclaimer</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Layanan disediakan "sebagaimana adanya" tanpa jaminan apapun</li>
        <li>Kami tidak menjamin caption akan menghasilkan penjualan atau engagement tertentu</li>
        <li>Kami tidak bertanggung jawab atas kerugian bisnis akibat penggunaan caption</li>
        <li>AI dapat menghasilkan konten yang tidak sempurna - selalu review sebelum digunakan</li>
        <li>Kami tidak bertanggung jawab atas downtime atau gangguan layanan</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">9. Batasan Tanggung Jawab</h2>
    <p class="text-gray-700 mb-4">
        Dalam kondisi apapun, Smart Copy SMK tidak bertanggung jawab atas:
    </p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Kerugian tidak langsung, insidental, atau konsekuensial</li>
        <li>Kehilangan profit, data, atau goodwill</li>
        <li>Gangguan bisnis atau kerugian komersial lainnya</li>
    </ul>
    <p class="text-gray-700 mb-4">
        Tanggung jawab maksimal kami terbatas pada jumlah yang Anda bayarkan dalam 3 bulan terakhir.
    </p>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">10. Penangguhan dan Penghentian</h2>
    <p class="text-gray-700 mb-4">
        Kami berhak untuk menangguhkan atau menghentikan akun Anda jika:
    </p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Melanggar Syarat dan Ketentuan ini</li>
        <li>Menggunakan layanan untuk tujuan ilegal</li>
        <li>Menyalahgunakan sistem atau mengganggu pengguna lain</li>
        <li>Tidak membayar tagihan yang jatuh tempo</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">11. Perubahan Layanan</h2>
    <p class="text-gray-700 mb-4">
        Kami berhak untuk:
    </p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Mengubah, menangguhkan, atau menghentikan layanan kapan saja</li>
        <li>Mengubah harga dengan pemberitahuan 30 hari sebelumnya</li>
        <li>Menambah atau mengurangi fitur</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">12. Hukum yang Berlaku</h2>
    <p class="text-gray-700 mb-4">
        Syarat dan Ketentuan ini diatur oleh hukum Republik Indonesia. Setiap sengketa akan diselesaikan melalui pengadilan di Indonesia.
    </p>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">13. Kontak</h2>
    <p class="text-gray-700 mb-4">
        Jika Anda memiliki pertanyaan tentang Syarat dan Ketentuan ini:
    </p>
    <ul class="list-none text-gray-700 space-y-2 mb-4">
        <li><strong>Email:</strong> <a href="mailto:info@noteds.com" class="text-blue-600 hover:underline">info@noteds.com</a></li>
        <li><strong>WhatsApp:</strong> <a href="https://wa.me/6281654932383" class="text-blue-600 hover:underline" target="_blank">+62 816-5493-2383</a></li>
    </ul>

    <div class="bg-yellow-50 border-l-4 border-yellow-600 p-4 mt-8">
        <p class="text-sm text-gray-700">
            <strong>Penting:</strong> Dengan menggunakan layanan Smart Copy SMK, Anda menyatakan telah membaca, memahami, dan menyetujui Syarat dan Ketentuan ini.
        </p>
    </div>
</div>
@endsection

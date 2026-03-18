

<?php $__env->startSection('title', 'Refund Policy'); ?>
<?php $__env->startSection('description', 'Kebijakan Pengembalian Dana Noteds'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="text-3xl font-bold text-gray-900 mb-2">Refund Policy</h1>
<p class="text-sm text-gray-500 mb-8">Terakhir diperbarui: <?php echo e(date('d F Y')); ?></p>

<div class="prose prose-blue max-w-none">
    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">1. Kebijakan Umum</h2>
    <p class="text-gray-700 mb-4">
        Noteds berkomitmen untuk memberikan layanan terbaik. Kami memahami bahwa terkadang ada situasi yang memerlukan pengembalian dana. Kebijakan ini menjelaskan kondisi dan prosedur refund.
    </p>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">2. Paket Premium (AI Generator)</h2>
    
    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2.1 Refund Penuh (100%)</h3>
    <p class="text-gray-700 mb-4">Anda berhak mendapat refund penuh jika:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Layanan tidak dapat diakses sama sekali dalam 7 hari pertama</li>
        <li>Terjadi kesalahan teknis yang membuat layanan tidak bisa digunakan</li>
        <li>Kami gagal menyediakan fitur yang dijanjikan</li>
        <li>Request dilakukan dalam 7 hari pertama sejak pembelian</li>
        <li>Belum menggunakan lebih dari 20% kuota generate</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2.2 Refund Parsial (Prorata)</h3>
    <p class="text-gray-700 mb-4">Refund prorata diberikan jika:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Request dilakukan setelah 7 hari tapi sebelum 30 hari</li>
        <li>Sudah menggunakan kuota generate</li>
        <li>Perhitungan: (Sisa hari / Total hari) × Harga paket</li>
        <li>Minimal refund: Rp 50.000</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2.3 Tidak Ada Refund</h3>
    <p class="text-gray-700 mb-4">Refund tidak diberikan jika:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Request dilakukan setelah 30 hari pembelian</li>
        <li>Sudah menggunakan lebih dari 80% kuota generate</li>
        <li>Akun di-suspend karena melanggar Terms of Service</li>
        <li>Alasan subjektif ("tidak suka", "tidak sesuai ekspektasi")</li>
        <li>Sudah mendapat refund sebelumnya untuk paket yang sama</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">3. Layanan Operator Freelance</h2>
    
    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.1 Escrow System</h3>
    <p class="text-gray-700 mb-4">
        Pembayaran untuk layanan operator menggunakan sistem escrow:
    </p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Uang di-hold oleh platform setelah client bayar</li>
        <li>Operator mulai mengerjakan setelah pembayaran verified</li>
        <li>Client review hasil pekerjaan</li>
        <li>Jika approved: uang dirilis ke operator (90%), platform dapat komisi (10%)</li>
        <li>Jika dispute: mediasi oleh admin</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.2 Refund Penuh (100%)</h3>
    <p class="text-gray-700 mb-4">Client mendapat refund penuh jika:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Operator tidak mulai mengerjakan dalam 3 hari</li>
        <li>Operator membatalkan order sepihak</li>
        <li>Hasil pekerjaan tidak sesuai brief sama sekali</li>
        <li>Operator tidak merespon dalam 5 hari</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.3 Refund Parsial (50%)</h3>
    <p class="text-gray-700 mb-4">Refund 50% diberikan jika:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Operator sudah mengerjakan tapi hasil kurang memuaskan</li>
        <li>Revisi melebihi batas yang disepakati</li>
        <li>Client membatalkan setelah operator mulai mengerjakan</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.4 Tidak Ada Refund</h3>
    <p class="text-gray-700 mb-4">Refund tidak diberikan jika:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Client sudah approve hasil pekerjaan</li>
        <li>Client membatalkan setelah pekerjaan selesai</li>
        <li>Alasan subjektif setelah pekerjaan selesai</li>
        <li>Client tidak memberikan brief yang jelas</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">4. Prosedur Refund</h2>
    
    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.1 Cara Mengajukan Refund</h3>
    <ol class="list-decimal pl-6 text-gray-700 space-y-2 mb-4">
        <li>Login ke akun Anda</li>
        <li>Buka halaman "Order History" atau "Paket Saya"</li>
        <li>Klik "Request Refund" pada order yang ingin di-refund</li>
        <li>Isi form refund dengan alasan yang jelas</li>
        <li>Upload bukti jika diperlukan (screenshot, dll)</li>
        <li>Submit request</li>
    </ol>

    <p class="text-gray-700 mb-4">Atau hubungi kami via:</p>
    <ul class="list-none text-gray-700 space-y-2 mb-4">
        <li><strong>Email:</strong> <a href="mailto:info@noteds.com" class="text-blue-600 hover:underline">info@noteds.com</a></li>
        <li><strong>WhatsApp:</strong> <a href="https://wa.me/6281654932383" class="text-blue-600 hover:underline" target="_blank">+62 816-5493-2383</a></li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.2 Waktu Proses</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li><strong>Review Request:</strong> 1-3 hari kerja</li>
        <li><strong>Keputusan:</strong> Anda akan diberitahu via email/WhatsApp</li>
        <li><strong>Proses Refund:</strong> 5-7 hari kerja setelah approved</li>
        <li><strong>Metode Refund:</strong> Transfer bank atau e-wallet (sesuai metode pembayaran)</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.3 Informasi yang Diperlukan</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Order ID / Transaction ID</li>
        <li>Alasan refund yang jelas dan detail</li>
        <li>Bukti pendukung (screenshot, email, dll)</li>
        <li>Informasi rekening untuk transfer refund</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">5. Dispute Resolution</h2>
    <p class="text-gray-700 mb-4">
        Jika terjadi dispute antara client dan operator:
    </p>
    <ol class="list-decimal pl-6 text-gray-700 space-y-2 mb-4">
        <li>Client mengajukan dispute dengan alasan yang jelas</li>
        <li>Admin akan review brief, hasil pekerjaan, dan komunikasi</li>
        <li>Admin akan mediasi antara client dan operator</li>
        <li>Keputusan admin bersifat final dan mengikat</li>
        <li>Refund akan diproses sesuai keputusan admin</li>
    </ol>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">6. Biaya Refund</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Tidak ada biaya admin untuk refund</li>
        <li>Biaya transfer bank ditanggung oleh platform</li>
        <li>Refund akan dikurangi biaya payment gateway (jika ada)</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">7. Exceptions</h2>
    <p class="text-gray-700 mb-4">
        Kebijakan refund dapat dikecualikan dalam kondisi force majeure:
    </p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
        <li>Bencana alam</li>
        <li>Perang atau kerusuhan</li>
        <li>Gangguan internet/listrik skala nasional</li>
        <li>Perubahan regulasi pemerintah</li>
    </ul>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">8. Perubahan Kebijakan</h2>
    <p class="text-gray-700 mb-4">
        Kami berhak mengubah kebijakan refund ini kapan saja. Perubahan akan berlaku untuk transaksi baru setelah tanggal perubahan. Transaksi lama tetap mengikuti kebijakan saat pembelian.
    </p>

    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">9. Kontak</h2>
    <p class="text-gray-700 mb-4">
        Untuk pertanyaan tentang refund:
    </p>
    <ul class="list-none text-gray-700 space-y-2 mb-4">
        <li><strong>Email:</strong> <a href="mailto:info@noteds.com" class="text-blue-600 hover:underline">info@noteds.com</a></li>
        <li><strong>WhatsApp:</strong> <a href="https://wa.me/6281654932383" class="text-blue-600 hover:underline" target="_blank">+62 816-5493-2383</a></li>
        <li><strong>Jam Operasional:</strong> Senin-Jumat, 09:00-17:00 WIB</li>
    </ul>

    <div class="bg-green-50 border-l-4 border-green-600 p-4 mt-8">
        <p class="text-sm text-gray-700">
            <strong>Komitmen Kami:</strong> Kami berkomitmen untuk menangani setiap request refund dengan adil dan transparan. Kepuasan Anda adalah prioritas kami.
        </p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('legal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\legal\refund-policy.blade.php ENDPATH**/ ?>
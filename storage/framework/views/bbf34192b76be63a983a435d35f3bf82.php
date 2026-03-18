

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="{
    tab: '<?php echo e(session('_tab', 'manual')); ?>',
    addModal: false,
    midtransModal: false,
    xenditModal: false
}" x-cloak>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Payment Settings</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola metode pembayaran — aktifkan gateway yang ingin digunakan</p>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-3 gap-4 mb-6">
        
        <div class="bg-white rounded-xl border-2 p-4 cursor-pointer transition
            <?php echo e($manualBanks->where('is_active', true)->count() > 0 ? 'border-green-400' : 'border-gray-200'); ?>"
            @click="tab = 'manual'">
            <div class="flex items-center justify-between mb-2">
                <span class="text-2xl">🏦</span>
                <?php if($manualBanks->where('is_active', true)->count() > 0): ?>
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">AKTIF</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">Nonaktif</span>
                <?php endif; ?>
            </div>
            <p class="font-semibold text-gray-900 text-sm">Manual Transfer</p>
            <p class="text-xs text-gray-500 mt-0.5"><?php echo e($manualBanks->count()); ?> rekening terdaftar</p>
        </div>

        
        <div class="bg-white rounded-xl border-2 p-4 cursor-pointer transition
            <?php echo e($midtrans?->is_enabled ? 'border-purple-400' : 'border-gray-200'); ?>"
            @click="tab = 'midtrans'">
            <div class="flex items-center justify-between mb-2">
                <span class="text-2xl">💳</span>
                <?php if($midtrans?->is_enabled): ?>
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">AKTIF</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">Nonaktif</span>
                <?php endif; ?>
            </div>
            <p class="font-semibold text-gray-900 text-sm">Midtrans</p>
            <p class="text-xs text-gray-500 mt-0.5">
                <?php echo e($midtrans ? ucfirst($midtrans->getConfig('environment', 'sandbox')) : 'Belum dikonfigurasi'); ?>

            </p>
        </div>

        
        <div class="bg-white rounded-xl border-2 p-4 cursor-pointer transition
            <?php echo e($xendit?->is_enabled ? 'border-blue-400' : 'border-gray-200'); ?>"
            @click="tab = 'xendit'">
            <div class="flex items-center justify-between mb-2">
                <span class="text-2xl">⚡</span>
                <?php if($xendit?->is_enabled): ?>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">AKTIF</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">Nonaktif</span>
                <?php endif; ?>
            </div>
            <p class="font-semibold text-gray-900 text-sm">Xendit</p>
            <p class="text-xs text-gray-500 mt-0.5">
                <?php echo e($xendit ? ucfirst($xendit->getConfig('environment', 'sandbox')) : 'Belum dikonfigurasi'); ?>

            </p>
        </div>
    </div>

    
    <div class="flex gap-1 mb-4 bg-gray-100 rounded-xl p-1 w-fit">
        <button @click="tab = 'manual'"
                :class="tab === 'manual' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition">
            🏦 Manual Transfer
        </button>
        <button @click="tab = 'midtrans'"
                :class="tab === 'midtrans' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition">
            💳 Midtrans
        </button>
        <button @click="tab = 'xendit'"
                :class="tab === 'xendit' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition">
            ⚡ Xendit
        </button>
    </div>

    
    <div x-show="tab === 'manual'">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-900">Rekening Bank & E-Wallet</h3>
                    <p class="text-xs text-gray-500 mt-0.5">User upload bukti transfer, admin verifikasi manual</p>
                </div>
                <button @click="addModal = true"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    + Tambah Rekening
                </button>
            </div>

            <?php if($manualBanks->count() > 0): ?>
            <div class="p-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $manualBanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-xl p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo e($bank->bank_name); ?></p>
                            <p class="text-sm text-gray-700 font-mono mt-0.5"><?php echo e($bank->account_number); ?></p>
                            <p class="text-xs text-gray-500">a.n. <?php echo e($bank->account_name); ?></p>
                        </div>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                            <?php echo e($bank->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'); ?>">
                            <?php echo e($bank->is_active ? 'Aktif' : 'Nonaktif'); ?>

                        </span>
                    </div>
                    <?php if($bank->qr_code_path): ?>
                    <img src="<?php echo e(asset('storage/' . $bank->qr_code_path)); ?>" alt="QR"
                         class="w-24 h-24 object-contain border border-gray-100 rounded-lg mb-3">
                    <?php endif; ?>
                    <form action="<?php echo e(route('admin.payment-settings.destroy', $bank)); ?>" method="POST"
                          onsubmit="return confirm('Hapus rekening ini?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="w-full py-1.5 border border-red-300 text-red-600 hover:bg-red-50 text-xs font-medium rounded-lg transition">
                            Hapus
                        </button>
                    </form>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="p-12 text-center text-gray-400">
                <p class="text-4xl mb-3">🏦</p>
                <p class="text-sm">Belum ada rekening. Tambahkan rekening bank atau e-wallet.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div x-show="tab === 'midtrans'">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">💳</div>
                <div>
                    <h3 class="font-semibold text-gray-900">Midtrans Payment Gateway</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Terima pembayaran otomatis: Credit Card, GoPay, OVO, VA Bank, QRIS, dll.</p>
                    <a href="https://dashboard.midtrans.com" target="_blank"
                       class="text-xs text-purple-600 hover:underline mt-1 inline-block">
                        Buka Midtrans Dashboard →
                    </a>
                </div>
            </div>

            <form action="<?php echo e(route('admin.payment-settings.midtrans-update')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">Aktifkan Midtrans</p>
                        <p class="text-xs text-gray-500">Tampilkan opsi pembayaran Midtrans di halaman checkout</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="midtrans_enabled" value="1"
                               <?php echo e(($midtrans?->is_enabled ?? config('payment.midtrans.enabled')) ? 'checked' : ''); ?>

                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
                        <select name="midtrans_environment"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <?php $midtransEnv = $midtrans?->getConfig('environment') ?? config('payment.midtrans.environment', 'sandbox'); ?>
                            <option value="sandbox" <?php echo e($midtransEnv === 'sandbox' ? 'selected' : ''); ?>>Sandbox (Testing)</option>
                            <option value="production" <?php echo e($midtransEnv === 'production' ? 'selected' : ''); ?>>Production (Live)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Merchant ID</label>
                        <input type="text" name="midtrans_merchant_id"
                               value="<?php echo e($midtrans?->getConfig('merchant_id') ?? config('payment.midtrans.merchant_id')); ?>"
                               placeholder="G123456789"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client Key</label>
                        <input type="text" name="midtrans_client_key"
                               value="<?php echo e($midtrans?->getConfig('client_key') ?? config('payment.midtrans.client_key')); ?>"
                               placeholder="SB-Mid-client-xxxxx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Server Key</label>
                        <input type="password" name="midtrans_server_key"
                               value="<?php echo e($midtrans?->getConfig('server_key') ?? config('payment.midtrans.server_key')); ?>"
                               placeholder="SB-Mid-server-xxxxx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-400 mt-1">Jangan pernah share Server Key ke publik</p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
                    Setelah save, konfigurasi disimpan ke database dan file .env. Tidak perlu restart server.
                </div>

                <button type="submit"
                        class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition">
                    Simpan Konfigurasi Midtrans
                </button>
            </form>
        </div>
    </div>

    
    <div x-show="tab === 'xendit'">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">⚡</div>
                <div>
                    <h3 class="font-semibold text-gray-900">Xendit Payment Gateway</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Terima pembayaran: VA Bank, E-Wallet, QRIS, Kartu Kredit, Cicilan.</p>
                    <a href="https://dashboard.xendit.co" target="_blank"
                       class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                        Buka Xendit Dashboard →
                    </a>
                </div>
            </div>

            <form action="<?php echo e(route('admin.payment-settings.xendit-update')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">Aktifkan Xendit</p>
                        <p class="text-xs text-gray-500">Tampilkan opsi pembayaran Xendit di halaman checkout</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="xendit_enabled" value="1"
                               <?php echo e(($xendit?->is_enabled ?? config('payment.xendit.enabled')) ? 'checked' : ''); ?>

                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
                        <select name="xendit_environment"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php $xenditEnv = $xendit?->getConfig('environment') ?? config('payment.xendit.environment', 'sandbox'); ?>
                            <option value="sandbox" <?php echo e($xenditEnv === 'sandbox' ? 'selected' : ''); ?>>Sandbox (Testing)</option>
                            <option value="production" <?php echo e($xenditEnv === 'production' ? 'selected' : ''); ?>>Production (Live)</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key (API Key)</label>
                        <input type="password" name="xendit_secret_key"
                               value="<?php echo e($xendit?->getConfig('secret_key') ?? config('payment.xendit.secret_key')); ?>"
                               placeholder="xnd_production_xxxxx atau xnd_development_xxxxx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-400 mt-1">Digunakan untuk server-side API calls</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                        <input type="text" name="xendit_public_key"
                               value="<?php echo e($xendit?->getConfig('public_key') ?? config('payment.xendit.public_key')); ?>"
                               placeholder="xnd_public_xxxxx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Verification Token</label>
                        <input type="text" name="xendit_webhook_token"
                               value="<?php echo e($xendit?->getConfig('webhook_token') ?? config('payment.xendit.webhook_token')); ?>"
                               placeholder="Token dari Xendit Dashboard"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
                    Webhook URL untuk Xendit: <code class="font-mono bg-blue-100 px-1 rounded"><?php echo e(url('/webhook/xendit')); ?></code>
                    — Daftarkan URL ini di Xendit Dashboard → Settings → Webhooks.
                </div>

                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    Simpan Konfigurasi Xendit
                </button>
            </form>
        </div>
    </div>

    
    <div x-show="addModal" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         @click.self="addModal = false">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Rekening Bank / E-Wallet</h3>
            <form action="<?php echo e(route('admin.payment-settings.store')); ?>" method="POST" enctype="multipart/form-data"
                  class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank / E-Wallet</label>
                    <input type="text" name="bank_name" required placeholder="BCA, Mandiri, GoPay, DANA..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening / No. HP</label>
                    <input type="text" name="account_number" required placeholder="1234567890"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                    <input type="text" name="account_name" required placeholder="PT Noteds Indonesia"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">QR Code (opsional)</label>
                    <input type="file" name="qr_code" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                    <p class="text-xs text-gray-400 mt-1">Max 2MB, JPG/PNG</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                        Simpan
                    </button>
                    <button type="button" @click="addModal = false"
                            class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\payment-settings.blade.php ENDPATH**/ ?>
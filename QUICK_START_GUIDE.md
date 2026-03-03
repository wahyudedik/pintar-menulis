# 🚀 Quick Start Guide - Smart Copy SMK

## Langkah Cepat untuk Mulai Menggunakan Platform

### 1. Setup Payment System (Admin) - WAJIB!

**Login sebagai Admin**:
- Email: `admin@smartcopy.com`
- Password: `password`

**Tambah Rekening Bank**:
1. Klik menu "Settings" di navbar
2. Klik tombol "+ Add Bank Account"
3. Isi form:
   - Bank Name: `BCA` (atau bank lain)
   - Account Number: `1234567890`
   - Account Name: `PT Smart Copy SMK`
   - QR Code: Upload gambar QR code (optional)
4. Klik "Add Bank Account"
5. Ulangi untuk bank lain jika perlu

**Configure Midtrans (Optional)**:
1. Di halaman Settings, klik "⚙️ Configure" di bagian Midtrans
2. Centang "Enable Midtrans Payment Gateway" (jika ingin aktifkan)
3. Pilih Environment: `Sandbox` (untuk testing)
4. Isi API credentials dari Midtrans Dashboard
5. Klik "💾 Save Configuration"

### 2. Test Complete Workflow

#### A. Client Request Order
**Login sebagai Client**:
- Email: `client@smartcopy.com`
- Password: `password`

**Steps**:
1. Klik "Browse Operators" di navbar
2. Pilih operator yang tersedia
3. Klik "Request Order"
4. Isi form:
   - Category: Pilih kategori (Social Media, E-Commerce, dll)
   - Brief: Jelaskan kebutuhan copywriting
   - Budget: Masukkan budget (min Rp 50,000)
   - Deadline: Pilih tanggal
5. Submit order

#### B. Operator Accept & Work
**Login sebagai Operator**:
- Email: `operator@smartcopy.com`
- Password: `password`

**Steps**:
1. Lihat order di "Queue" (navbar)
2. Klik "Accept Order"
3. Klik "Go to Workspace"
4. Gunakan AI Assistant:
   - Klik "Generate with AI"
   - AI akan generate copywriting
5. Edit hasil jika perlu
6. Tambah catatan untuk client (optional)
7. Klik "Submit to Client"

#### C. Client Review & Pay
**Login kembali sebagai Client**:

**Steps**:
1. Klik "Orders" di navbar
2. Klik order yang sudah completed
3. Review hasil pekerjaan
4. Klik "Bayar Sekarang" (tombol hijau besar)
5. Pilih bank account
6. Transfer sesuai nominal
7. Upload bukti transfer
8. Submit

#### D. Admin Verify Payment
**Login sebagai Admin**:

**Steps**:
1. Klik "Payments" di navbar
2. Lihat pending payment
3. Klik gambar bukti transfer untuk melihat full size
4. Jika valid, klik "✓ Verify Payment"
5. Client & operator akan dapat notifikasi

#### E. Client Rate Operator
**Login kembali sebagai Client**:

**Steps**:
1. Klik order yang sudah dibayar
2. Klik "⭐ Beri Rating"
3. Pilih rating 1-5 bintang
4. Tulis review (optional)
5. Submit

### 3. Test Guru ML Training

**Login sebagai Guru**:
- Email: `guru@smartcopy.com`
- Password: `password`

**Steps**:
1. Klik "Training" di navbar
2. Lihat completed orders yang perlu review
3. Klik "Review"
4. Beri quality rating (Poor/Fair/Good/Excellent)
5. Tambah corrections jika perlu
6. Submit
7. Lihat analytics di menu "Analytics"

### 4. Test Withdrawal (Operator)

**Login sebagai Operator**:

**Steps**:
1. Klik "Earnings" di navbar
2. Lihat total penghasilan
3. Scroll ke "Request Withdrawal"
4. Masukkan amount (min Rp 50,000)
5. Pilih bank account
6. Submit request

**Admin Approve**:
1. Login sebagai admin
2. Klik "Withdrawals" di navbar
3. Klik "Approve" untuk withdrawal request
4. Setelah transfer, klik "Complete"

## Common Tasks

### Add New User (Admin)
1. Menu "Users" → "+ Add User"
2. Isi form (name, email, password, role)
3. Submit

### Verify Operator (Admin)
1. Menu "Users"
2. Find operator
3. Klik "Verify"

### Edit Package Pricing (Admin)
1. Menu "Packages"
2. Klik "Edit Package"
3. Update price, quotas, etc.
4. Submit

### View Financial Reports (Admin)
1. Menu "Reports"
2. Lihat charts & statistics
3. Top operators leaderboard

## Troubleshooting

### Payment Button Tidak Muncul
- Pastikan order status = "completed"
- Pastikan sudah ada bank account di settings

### AI Generator Tidak Jalan
- Check Gemini API key di .env
- Pastikan internet connection aktif

### Notification Tidak Muncul
- Refresh halaman
- Check browser console untuk errors

### Upload Gambar Gagal
- Max size 2MB
- Format: JPG, PNG
- Check folder permissions

## Tips & Tricks

### Untuk Admin:
- Setup minimal 2 bank accounts untuk pilihan
- Upload QR code untuk mempermudah client
- Verify payment secepat mungkin
- Monitor reports secara berkala

### Untuk Operator:
- Accept order sesuai kemampuan
- Gunakan AI assistant untuk efisiensi
- Tambahkan catatan untuk client
- Maintain rating tinggi

### Untuk Client:
- Berikan brief yang jelas
- Set budget yang reasonable
- Upload bukti transfer yang jelas
- Beri rating & review yang fair

### Untuk Guru:
- Review secara konsisten
- Berikan corrections yang konstruktif
- Monitor analytics untuk improvement

## Next Steps

1. ✅ Setup payment methods (bank accounts)
2. ✅ Test complete workflow
3. ✅ Configure Midtrans (optional)
4. ✅ Add real users
5. ✅ Monitor & maintain

## Support

Jika ada masalah:
1. Check documentation files
2. Check Laravel logs: `storage/logs/laravel.log`
3. Clear cache: `php artisan cache:clear`
4. Check database: `php artisan migrate:status`

---

**Happy Copywriting!** ✨

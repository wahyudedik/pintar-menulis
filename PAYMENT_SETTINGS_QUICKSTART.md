# 💳 Payment Settings - Quick Start

## 🎯 Akses Payment Settings

### Step 1: Login sebagai Admin
```
Email: admin@smartcopy.com
Password: password
```

### Step 2: Click Settings Icon
- Sidebar kiri → Icon ⚙️ (Settings)
- Atau langsung ke: `http://pintar-menulis.test/admin/payment-settings`

---

## ✅ Setup Sudah Selesai!

### Default Bank Accounts (Already Added)
```
✓ BCA
  Account: 1234567890
  Name: PT Smart Copy SMK
  Status: Active

✓ Mandiri
  Account: 1400012345678
  Name: PT Smart Copy SMK
  Status: Active

✓ BNI
  Account: 0987654321
  Name: PT Smart Copy SMK
  Status: Active
```

---

## 🔧 Cara Menambah Bank Account Baru

### Step 1: Click "+ Add Bank Account"
Button merah di kanan atas

### Step 2: Fill Form
```
Bank Name: [Nama Bank]
Account Number: [Nomor Rekening]
Account Name: [Nama Pemilik]
QR Code: [Upload QR Code - Optional]
```

### Step 3: Click "Add Bank Account"
Done! Bank account baru akan muncul di list

---

## 🎨 Cara Upload QR Code

### Step 1: Prepare QR Code
- Screenshot QR QRIS dari mobile banking
- Atau download dari merchant dashboard
- Format: JPG, PNG
- Max size: 2MB

### Step 2: Upload
- Click "Choose File" di form
- Select QR code image
- Preview akan muncul

### Step 3: Save
- QR code akan ditampilkan di payment page
- Client bisa scan langsung untuk bayar

---

## 🚀 Midtrans Integration (Optional)

### Kapan Perlu Midtrans?
- ✅ Mau auto-verify payment
- ✅ Mau terima credit card
- ✅ Mau terima e-wallet (OVO, GoPay, Dana)
- ✅ Transaction volume tinggi (>100/month)

### Kapan TIDAK Perlu Midtrans?
- ❌ Baru mulai (volume rendah)
- ❌ Budget terbatas (ada fee 2.9%)
- ❌ Manual transfer sudah cukup
- ❌ Target market prefer bank transfer

### Setup Midtrans (If Needed)

1. **Register Midtrans**
   - Go to: https://dashboard.midtrans.com
   - Sign up → Verify business
   - Get API credentials

2. **Configure di Smart Copy**
   - Click "Configure" di Midtrans card
   - Enable checkbox
   - Choose environment (Sandbox/Production)
   - Fill API keys:
     - Merchant ID
     - Client Key
     - Server Key
   - Click "Save Configuration"

3. **Test Payment**
   - Use sandbox mode first
   - Test with dummy cards
   - Verify webhook working
   - Switch to production when ready

---

## 💡 Tips & Best Practices

### Manual Transfer
- ✅ Gunakan rekening bisnis (bukan personal)
- ✅ Nama rekening harus jelas (PT/CV)
- ✅ Upload QR code untuk kemudahan
- ✅ Aktifkan minimal 2-3 bank (pilihan user)
- ✅ Update nomor rekening jika berubah

### QR Code
- ✅ Gunakan QRIS (universal)
- ✅ Test scan sebelum upload
- ✅ Pastikan QR code jelas & tidak blur
- ✅ Update jika expired

### Midtrans
- ✅ Start dengan Sandbox mode
- ✅ Test semua payment methods
- ✅ Setup webhook notification
- ✅ Monitor transaction dashboard
- ✅ Keep server key secret

---

## 🔍 Troubleshooting

### Bank Account Tidak Muncul di Payment Page
**Solution:**
- Check status: Harus "Active"
- Refresh page
- Clear cache: `php artisan cache:clear`

### QR Code Tidak Tampil
**Solution:**
- Check file size (max 2MB)
- Check format (JPG/PNG only)
- Re-upload dengan file lebih kecil
- Check storage permissions

### Midtrans Configuration Error
**Solution:**
- Verify API keys correct
- Check environment (Sandbox vs Production)
- Ensure .env file writable
- Run: `php artisan config:clear`

### Payment Tidak Auto-Verify (Midtrans)
**Solution:**
- Check webhook URL configured
- Verify webhook signature
- Check Midtrans dashboard logs
- Test with sandbox first

---

## 📊 Monitoring

### Check Payment Methods
```bash
php artisan tinker
>>> App\Models\PaymentSetting::all()
```

### Check Midtrans Config
```bash
php artisan tinker
>>> env('MIDTRANS_ENABLED')
>>> env('MIDTRANS_ENVIRONMENT')
```

### View Payment Logs
- Admin → Payments
- Check verification status
- Monitor pending payments

---

## 🎯 Next Steps

### After Setup
1. ✅ Test payment flow as client
2. ✅ Upload bukti transfer
3. ✅ Verify payment as admin
4. ✅ Check order processed

### Optional Enhancements
- [ ] Add more bank accounts
- [ ] Upload QR codes
- [ ] Setup Midtrans (if needed)
- [ ] Monitor payment statistics

---

## 📞 Need Help?

### Common Questions

**Q: Berapa biaya Midtrans?**
A: 2.9% + Rp 2,000 per transaksi (varies by payment method)

**Q: Apakah wajib pakai Midtrans?**
A: Tidak. Manual transfer sudah cukup untuk start.

**Q: Bisa pakai bank lain?**
A: Ya! Click "+ Add Bank Account" untuk tambah bank apapun.

**Q: QR Code wajib?**
A: Tidak wajib, tapi recommended untuk kemudahan user.

**Q: Bisa edit bank account?**
A: Currently: Delete & re-add. Edit feature coming soon.

---

## ✅ Checklist Setup

- [x] Login sebagai Admin
- [x] Access Payment Settings page
- [x] View default bank accounts (BCA, Mandiri, BNI)
- [ ] Add custom bank account (optional)
- [ ] Upload QR codes (optional)
- [ ] Configure Midtrans (optional)
- [ ] Test payment flow
- [ ] Verify payment as admin

---

**Status**: ✅ READY TO USE
**Default Banks**: 3 active (BCA, Mandiri, BNI)
**Midtrans**: Not configured (optional)

**You're all set!** 🎉

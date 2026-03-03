# Setup Gemini API Key

## Masalah
API key Gemini yang ada sudah **tidak valid atau expired**. Error yang muncul:
```
Model Gemini tidak tersedia. API key mungkin tidak valid atau expired.
```

## Solusi: Generate API Key Baru

### Langkah 1: Buka Google AI Studio
Kunjungi: https://aistudio.google.com/app/apikey

### Langkah 2: Login dengan Google Account
- Gunakan akun Google Anda
- Pastikan akun sudah terverifikasi

### Langkah 3: Create API Key
1. Klik tombol **"Create API Key"**
2. Pilih project Google Cloud (atau buat baru)
3. Copy API key yang dihasilkan

### Langkah 4: Update .env File
Buka file `.env` dan update baris berikut:
```env
GEMINI_API_KEY=YOUR_NEW_API_KEY_HERE
```

Ganti `YOUR_NEW_API_KEY_HERE` dengan API key baru yang sudah di-copy.

### Langkah 5: Clear Cache
Jalankan command berikut untuk clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Langkah 6: Test API
Test apakah API key sudah bekerja:
```bash
php artisan test:gemini
```

Jika berhasil, akan muncul:
```
Testing Gemini API...
SUCCESS!
Result: [hasil generate dari AI]
```

## Catatan Penting

### Free Tier Limits
- 60 requests per minute
- 1,500 requests per day
- Gratis untuk penggunaan personal

### Model yang Tersedia
Saat ini menggunakan model: `gemini-pro`
- Cocok untuk text generation
- Response cepat
- Gratis

### Troubleshooting

#### Error: API key not valid
- Pastikan API key sudah di-copy dengan benar (tidak ada spasi)
- Cek apakah API key sudah enabled di Google Cloud Console

#### Error: Quota exceeded
- Tunggu beberapa menit (rate limit)
- Atau upgrade ke paid plan

#### Error: Model not found
- API key mungkin restricted
- Generate API key baru dengan unrestricted access

## Testing di Browser

Setelah update API key, test di browser:
1. Login sebagai client
2. Buka: http://pintar-menulis.test/ai-generator
3. Isi form dan klik "Generate dengan AI"
4. Jika berhasil, hasil akan muncul di panel kanan

## Support

Jika masih ada masalah:
1. Cek log di `storage/logs/laravel.log`
2. Pastikan koneksi internet stabil
3. Coba generate API key baru

---

**Status Saat Ini**: ❌ API Key tidak valid - perlu generate baru
**Link Generate**: https://aistudio.google.com/app/apikey

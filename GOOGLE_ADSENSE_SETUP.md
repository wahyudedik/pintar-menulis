# Google AdSense Setup Guide

## Status Saat Ini
✅ Script AdSense sudah ditambahkan di `resources/views/layouts/public.blade.php`
✅ File `ads.txt` sudah dibuat di `public/ads.txt`
✅ Publisher ID: `ca-pub-2771325503977360`

## Langkah-Langkah Verifikasi

### 1. Upload File ads.txt ke VPS
File `public/ads.txt` harus bisa diakses di:
```
https://noteds.com/ads.txt
```

Pastikan file ini sudah di-upload ke VPS dan bisa diakses publik.

### 2. Verifikasi Domain di Google AdSense

1. Login ke [Google AdSense](https://www.google.com/adsense/)
2. Pergi ke **Sites** → **Add Site**
3. Masukkan domain: `noteds.com`
4. Google akan memberikan kode verifikasi (sudah ditambahkan di `<head>`)
5. Klik **Verify** dan tunggu proses verifikasi (bisa 24-48 jam)

### 3. Tunggu Approval

Setelah domain terverifikasi:
- Google akan review situs Anda
- Proses approval bisa memakan waktu beberapa hari hingga 2 minggu
- Pastikan situs memiliki konten berkualitas dan cukup artikel

### 4. Buat Ad Units

Setelah approved:
1. Pergi ke **Ads** → **By ad unit**
2. Buat ad unit baru (Display ads, In-feed ads, dll)
3. Copy kode iklan yang diberikan
4. Paste di admin panel: `/admin/ad-placements`

## Troubleshooting

### Error: "Tidak dapat memverifikasi situs Anda"

**Penyebab:**
- Domain belum diverifikasi di Google AdSense
- File ads.txt belum bisa diakses
- Script AdSense belum terdeteksi oleh crawler Google

**Solusi:**
1. Pastikan `https://noteds.com/ads.txt` bisa diakses
2. Tunggu 24-48 jam setelah menambahkan script
3. Verifikasi domain di Google AdSense dashboard
4. Pastikan tidak ada robots.txt yang memblokir crawler Google

### Cek File ads.txt

Buka browser dan akses:
```
https://noteds.com/ads.txt
```

Harus menampilkan:
```
google.com, pub-2771325503977360, DIRECT, f08c47fec0942fa0
```

### Cek Script AdSense

View source halaman artikel dan pastikan ada:
```html
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2771325503977360"
        crossorigin="anonymous"></script>
```

## Ad Placement Locations

Iklan bisa ditampilkan di 5 lokasi:

1. **Article List Top** - Atas halaman daftar artikel
2. **Article List Bottom** - Bawah halaman daftar artikel
3. **Article Detail Top** - Atas artikel
4. **Article Detail Middle** - Tengah artikel
5. **Article Detail Bottom** - Bawah artikel

Manage di: `/admin/ad-placements`

## Tips Agar Cepat Approved

1. **Konten Berkualitas**: Minimal 20-30 artikel original
2. **Traffic**: Ada visitor yang cukup
3. **Navigation**: Menu dan struktur situs jelas
4. **Privacy Policy**: Tambahkan halaman privacy policy
5. **About Us**: Tambahkan halaman about
6. **Contact**: Tambahkan halaman contact

## Catatan Penting

- Jangan klik iklan sendiri (bisa banned)
- Jangan minta orang lain klik iklan (invalid traffic)
- Iklan akan muncul setelah approved oleh Google
- Proses approval bisa memakan waktu 1-2 minggu
- Pastikan konten tidak melanggar kebijakan AdSense

## Resources

- [Google AdSense Help](https://support.google.com/adsense/)
- [AdSense Program Policies](https://support.google.com/adsense/answer/48182)
- [ads.txt Guide](https://support.google.com/adsense/answer/7532444)

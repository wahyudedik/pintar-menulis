# 🚀 Banner Information - Quick Start Guide

## Untuk Admin

### 1. Akses Banner Management
```
Login sebagai Admin → Dashboard
→ Klik icon 💬 "Banner Information" di sidebar (kiri)
atau
→ Klik "Banners" di top navigation (atas)

URL: http://pintar-menulis.test/admin/banner-information
```

### 2. Setup Banner

#### Landing Page Banner (untuk visitor)
```
Title: Selamat Datang di Smart Copy SMK! 🎉
Content: 
  Halo! Terima kasih sudah mengunjungi Smart Copy SMK.
  
  Fitur Unggulan:
  • 200+ Jenis Konten
  • 12 Industry Presets
  • Bahasa UMKM + Daerah
  
  Daftar sekarang dan dapatkan GRATIS 5 variasi caption!

Active: ✓ (centang)
```

#### Client Dashboard Banner
```
Title: Selamat Datang di Dashboard Client! 👋
Content:
  Halo Client! Selamat datang di dashboard Anda.
  
  Yang bisa Anda lakukan:
  • Generate caption dengan AI
  • Track performa di Analytics
  • Simpan Brand Voice
  
  Mulai generate caption pertama Anda sekarang!

Active: ✓ (centang)
```

#### Operator Dashboard Banner
```
Title: Selamat Datang di Dashboard Operator! ⚙️
Content:
  Halo Operator! Terima kasih sudah bergabung.
  
  Tugas Anda:
  • Ambil order dari queue
  • Buat copywriting berkualitas
  • Selesaikan sebelum deadline
  
  Semangat bekerja! 💪

Active: ✓ (centang)
```

#### Guru Dashboard Banner
```
Title: Selamat Datang di Dashboard Guru! 🎓
Content:
  Halo Guru! Terima kasih sudah membantu melatih AI.
  
  Tanggung Jawab Anda:
  • Review caption quality
  • Train AI dengan data berkualitas
  • Monitor ML analytics
  
  Mari kita buat AI yang lebih pintar! 🧠

Active: ✓ (centang)
```

### 3. Tips Content

#### Gunakan HTML untuk formatting:
```html
<strong>Bold text</strong>
<em>Italic text</em>
<br> untuk line break
<a href="url">Link</a>
<ul>
  <li>Bullet point 1</li>
  <li>Bullet point 2</li>
</ul>
```

#### Emoji untuk visual appeal:
```
🎉 🎯 ✨ 💾 📊 🚀 💪 🧠 ⚙️ 🎓
```

---

## Untuk User

### Kapan Banner Muncul?
- **Pertama kali** buka halaman
- **Setelah 500ms** (setengah detik)
- **Hanya sekali** per user

### Cara Close Banner:
1. Klik tombol **X** di pojok kanan atas
2. Atau klik tombol **"Got it!"**
3. Centang **"Don't show again"** jika tidak mau lihat lagi

### Banner Muncul Lagi?
- **Tidak centang** "Don't show again" → Muncul lagi di visit berikutnya
- **Centang** "Don't show again" → Tidak muncul lagi selamanya

### Reset Banner (untuk testing):
```javascript
// Buka Console (F12)
// Ketik:
localStorage.clear();
// Reload page
```

---

## Troubleshooting

### Banner tidak muncul?

#### Cek 1: Admin sudah activate?
```
Admin → Banner Information Management
→ Pastikan Active = ✓
→ Pastikan Title & Content terisi
```

#### Cek 2: User sudah pernah close?
```
Buka Console (F12)
Ketik: localStorage.getItem('banner_closed_landing')
Jika hasilnya 'true' → User sudah close banner
Solusi: localStorage.removeItem('banner_closed_landing')
```

#### Cek 3: API endpoint bekerja?
```
Buka: http://pintar-menulis.test/api/banner/landing
Harusnya return JSON dengan banner data
```

#### Cek 4: JavaScript error?
```
Buka Console (F12)
Lihat ada error merah?
Jika ada, screenshot dan report
```

### Banner muncul terus?
```
Pastikan checkbox "Don't show again" berfungsi
Cek localStorage setelah close
```

### Content tidak formatted?
```
Pastikan menggunakan HTML tags yang benar
Contoh: <strong>Bold</strong> bukan **Bold**
```

---

## Best Practices

### 1. Keep It Short
- Title: Max 50 karakter
- Content: Max 300 kata
- User attention span pendek!

### 2. Clear Call-to-Action
```
❌ "Silakan explore fitur kami"
✅ "Mulai generate caption pertama Anda sekarang!"
```

### 3. Use Visual Elements
- Emoji untuk menarik perhatian
- Bullet points untuk readability
- Bold untuk highlight penting

### 4. Update Regularly
- Announce new features
- Seasonal promotions
- Important updates

### 5. Test Before Activate
- Preview content
- Check typos
- Test on mobile

---

## Examples

### Announcement New Feature
```
Title: 🎉 Fitur Baru: Analytics Dashboard!
Content:
  Sekarang kamu bisa track performa caption dengan Analytics Dashboard!
  
  Fitur baru:
  • Real-time engagement tracking
  • Export data ke CSV/PDF
  • Insights & recommendations
  
  Coba sekarang di menu Analytics!
```

### Maintenance Notice
```
Title: ⚠️ Scheduled Maintenance
Content:
  Sistem akan maintenance pada:
  📅 Tanggal: 15 Maret 2026
  ⏰ Waktu: 01:00 - 03:00 WIB
  
  Mohon maaf atas ketidaknyamanannya.
  Terima kasih atas pengertiannya! 🙏
```

### Promotion
```
Title: 🔥 Promo Spesial Ramadan!
Content:
  Dapatkan DISKON 50% untuk semua paket!
  
  Periode: 1-30 Ramadan 1447H
  Kode: RAMADAN2026
  
  Buruan daftar sebelum kehabisan! 🚀
```

### Tips & Tricks
```
Title: 💡 Tips: Maksimalkan AI Generator
Content:
  Tips untuk hasil caption terbaik:
  
  1. Isi brief dengan detail
  2. Pilih tone yang sesuai brand
  3. Gunakan keywords relevan
  4. Generate 5-10 variasi
  5. Rate caption untuk improve AI
  
  Happy generating! ✨
```

---

## Quick Commands

### Clear specific banner:
```javascript
localStorage.removeItem('banner_closed_landing');
localStorage.removeItem('banner_closed_client');
localStorage.removeItem('banner_closed_operator');
localStorage.removeItem('banner_closed_guru');
```

### Clear all banners:
```javascript
localStorage.clear();
```

### Check banner status:
```javascript
console.log('Landing:', localStorage.getItem('banner_closed_landing'));
console.log('Client:', localStorage.getItem('banner_closed_client'));
console.log('Operator:', localStorage.getItem('banner_closed_operator'));
console.log('Guru:', localStorage.getItem('banner_closed_guru'));
```

### Test API:
```bash
curl http://pintar-menulis.test/api/banner/landing
curl http://pintar-menulis.test/api/banner/client
curl http://pintar-menulis.test/api/banner/operator
curl http://pintar-menulis.test/api/banner/guru
```

---

## 🎉 Done!

Banner Information feature siap digunakan! Admin tinggal setup content dan activate banner sesuai kebutuhan.

**Remember:**
- Banner muncul **sekali saja** per user
- User bisa **close** dan pilih "Don't show again"
- Admin bisa **update** content kapan saja
- Banner **auto-disable** jika title/content kosong

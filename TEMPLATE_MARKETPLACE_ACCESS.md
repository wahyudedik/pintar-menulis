# 📍 Cara Akses Template Marketplace

## 🚀 3 Cara Akses Template Marketplace

### 1️⃣ Via Sidebar Navigation (RECOMMENDED)
**Paling Mudah & Cepat!**

1. Login sebagai user dengan role **Client**
2. Lihat sidebar kiri (icon-icon vertikal)
3. Klik icon **📚 Template** (icon kotak/box)
4. Anda akan masuk ke halaman Template Marketplace

**Lokasi di Sidebar:**
```
Dashboard          🏠
AI Generator       ⚡
Keyword Research   🔍
Analytics          📊
📚 Template        ← KLIK INI!
Competitor         🔎
Browse Operators   👥
My Orders          📋
Projects           📁
Feedback           💬
```

---

### 2️⃣ Via URL Langsung
**Ketik di browser:**

```
http://pintar-menulis.test/templates
```

**Atau untuk create template baru:**
```
http://pintar-menulis.test/templates/create
```

---

### 3️⃣ Via AI Generator
**Akses dari dalam AI Generator:**

1. Buka AI Generator: `http://pintar-menulis.test/ai-generator`
2. Klik tab **"📚 Template Library"** di bagian atas
3. Browse templates (system + community)
4. Klik "Gunakan" untuk load template ke form
5. Atau klik link "Lihat Semua Template" untuk ke marketplace

---

## 📱 Tampilan Sidebar

Setelah update, sidebar client akan terlihat seperti ini:

```
┌─────────────────┐
│   🏠 Dashboard  │
│   ⚡ AI Gen     │
│   🔍 Keyword    │
│   📊 Analytics  │
│   📚 Template   │ ← BARU!
│   🔎 Competitor │
│   👥 Operators  │
│   📋 Orders     │
│   📁 Projects   │
│   💬 Feedback   │
└─────────────────┘
```

---

## 🎯 Fitur yang Tersedia

### Di Template Marketplace (`/templates`)
- ✅ Browse 500+ system templates + community templates
- ✅ Filter by category, platform, tone, type
- ✅ Search templates
- ✅ Sort by popular, rating, newest
- ✅ View stats (Total, System, Community, My Templates)
- ✅ Add to favorites
- ✅ View template details
- ✅ Rate & review templates
- ✅ Purchase premium templates
- ✅ Create new template (button di header)

### Di Create Template (`/templates/create`)
- ✅ Form lengkap untuk buat template
- ✅ Set public/private
- ✅ Set free/premium + pricing
- ✅ Add tags & instructions
- ✅ Choose license type
- ✅ Submit for approval

### Di Template Detail (`/templates/{id}`)
- ✅ View full template content
- ✅ See author info
- ✅ View ratings & reviews
- ✅ Rate template (1-5 stars)
- ✅ Add to favorites
- ✅ Use template in AI Generator
- ✅ Export template (JSON)
- ✅ Purchase (if premium)

---

## 🔐 Role & Permission

**Client Role:**
- ✅ Akses penuh ke Template Marketplace
- ✅ Bisa create, edit, delete template sendiri
- ✅ Bisa rate & review template orang lain
- ✅ Bisa favorite & purchase templates
- ✅ Bisa export/import templates

**Operator/Guru Role:**
- ⚠️ Belum ada akses (bisa ditambahkan jika perlu)

**Admin Role:**
- ✅ Approve/reject community templates
- ✅ Manage all templates
- ✅ View analytics

---

## 📊 Stats Dashboard

Di halaman marketplace, Anda akan melihat 4 cards:

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ Total        │ System       │ Community    │ My Templates │
│ Templates    │ Templates    │ Templates    │              │
│ 500+         │ 500+         │ 0            │ 0            │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

---

## 🎨 UI Preview

### Template Card
```
┌─────────────────────────────────────┐
│ Flash Sale Template          ❤️     │
│ event_promo • 🆓 Free               │
│                                     │
│ Template untuk promo flash sale     │
│ dengan urgency & scarcity...        │
│                                     │
│ ⭐ 4.5 (120)    1,250 kali digunakan│
│                                     │
│ [Lihat Detail]  [✏️]                │
└─────────────────────────────────────┘
```

### Filters
```
┌─────────────────────────────────────────────────────┐
│ [Search...]  [Category ▼]  [Type ▼]  [Sort ▼]      │
│                                                     │
│ [🔍 Filter]  [Reset]                                │
└─────────────────────────────────────────────────────┘
```

---

## 🚦 Quick Start

### Untuk Pertama Kali:

1. **Login** sebagai client
2. **Klik icon 📚** di sidebar kiri
3. **Browse** templates yang tersedia
4. **Klik "Buat Template Baru"** untuk create template pertama
5. **Isi form** dan submit
6. **Template tersimpan** sebagai draft
7. **Set public** untuk share ke community (perlu approval)

### Untuk Menggunakan Template:

1. **Browse** template marketplace
2. **Klik "Lihat Detail"** pada template
3. **Klik "Gunakan"** atau buka AI Generator
4. **Template auto-load** ke form
5. **Sesuaikan** dengan kebutuhan
6. **Generate** content!

---

## 🔧 Troubleshooting

### Tidak melihat icon Template di sidebar?
- Pastikan Anda login sebagai **Client**
- Refresh browser (Ctrl+F5)
- Clear cache browser
- Logout & login kembali

### Template tidak muncul di marketplace?
- Cek status template: harus **approved**
- Cek visibility: harus **public**
- Refresh halaman

### Tidak bisa create template?
- Pastikan Anda sudah login
- Pastikan role Anda adalah **Client**
- Cek koneksi database

---

## 📞 Support

Jika masih ada masalah:
1. Cek dokumentasi lengkap di `TEMPLATE_MARKETPLACE_QUICK_START.md`
2. Review implementation di `AI_TEMPLATE_MARKETPLACE_IMPLEMENTATION_COMPLETE.md`
3. Cek routes: `php artisan route:list --name=templates`
4. Cek logs: `storage/logs/laravel.log`

---

## ✅ Checklist Akses

- [x] Database migrations run
- [x] Routes registered
- [x] Controllers created
- [x] Views created
- [x] Sidebar link added
- [ ] Login sebagai client
- [ ] Klik icon Template di sidebar
- [ ] Browse templates
- [ ] Create template baru
- [ ] Test all features

---

**Selamat menggunakan Template Marketplace!** 🎉

Lokasi: **Sidebar Kiri → Icon 📚 Template**
URL: **http://pintar-menulis.test/templates**


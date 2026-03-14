# 🚀 Template Marketplace - Quick Start Guide

## Akses Fitur

### 1. Template Marketplace
**URL:** `http://pintar-menulis.test/templates`

**Fitur:**
- Browse 500+ system templates + community templates
- Filter by category, platform, tone, type (free/premium)
- Search templates
- Sort by popular, rating, newest
- View template details
- Rate & review templates (1-5 stars)
- Add to favorites
- Purchase premium templates

---

### 2. Buat Template Baru
**URL:** `http://pintar-menulis.test/templates/create`

**Steps:**
1. Isi judul template
2. Tulis deskripsi
3. Pilih kategori, platform, tone
4. Tulis konten template (gunakan [PLACEHOLDER] untuk variabel)
5. Tambahkan instruksi format (opsional)
6. Tambahkan tags
7. Pilih visibility:
   - ☑️ Public (akan masuk approval queue)
   - ☐ Private (hanya untuk Anda)
8. Pilih type:
   - ☐ Free (gratis untuk semua)
   - ☑️ Premium (berbayar)
9. Set harga (jika premium)
10. Pilih license type
11. Klik "Simpan Template"

---

### 3. Template Library di AI Generator
**URL:** `http://pintar-menulis.test/ai-generator`

**Cara Akses:**
1. Buka AI Generator
2. Klik tab "📚 Template Library"
3. Browse templates (system + community)
4. Filter & search
5. Klik "Gunakan" untuk load template ke form
6. Generate content dengan template

---

## Fitur Lengkap

### 📚 Community Template Library (156)
- ✅ 500+ system templates
- ✅ User-generated community templates
- ✅ Public/private visibility
- ✅ Approval workflow
- ✅ Author attribution
- ✅ Usage tracking

### ✍️ User Generated Template (157)
- ✅ Create custom templates
- ✅ Full CRUD (Create, Read, Update, Delete)
- ✅ Draft/pending/approved status
- ✅ Rich template editor
- ✅ Placeholder support [VARIABLE]
- ✅ Format instructions

### 💰 Template Marketplace (158)
- ✅ Free & Premium templates
- ✅ Pricing system (Rp)
- ✅ 4 License types:
  - Free - Gratis untuk semua
  - Personal - Penggunaan pribadi
  - Commercial - Penggunaan komersial
  - Extended - Unlimited usage
- ✅ Purchase tracking
- ✅ Revenue analytics
- ✅ Sales dashboard

### ⭐ Template Rating System (159)
- ✅ 5-star rating
- ✅ Review text
- ✅ Average rating calculation
- ✅ Total ratings count
- ✅ Sort by rating
- ✅ Can't rate own template

### 📥 Template Import / Export (160)
- ✅ Export single template (JSON)
- ✅ Export multiple templates
- ✅ Export all templates
- ✅ Import from JSON file
- ✅ Import from URL
- ✅ Validation & duplicate detection

---

## API Endpoints

### Template CRUD
```
GET    /templates              - List all templates
POST   /templates              - Create new template
GET    /templates/{id}         - Show template detail
PUT    /templates/{id}         - Update template
DELETE /templates/{id}         - Delete template
GET    /templates/create       - Create form
GET    /templates/{id}/edit    - Edit form
```

### Template Actions
```
POST   /templates/{id}/rate     - Rate template (1-5 stars)
POST   /templates/{id}/favorite - Toggle favorite
POST   /templates/{id}/use      - Increment usage count
```

### Import/Export
```
GET    /templates/{id}/export      - Export single template
POST   /templates/export-multiple  - Export selected templates
GET    /templates/export-all       - Export all user templates
POST   /templates/import           - Import from JSON file
POST   /templates/import-url       - Import from URL
```

### AI Generator Integration
```
GET    /api/templates/all - Get all templates (system + community)
```

---

## Database Tables

### user_templates
```sql
- id, user_id, title, description
- category, platform, tone
- template_content, format_instructions, tags
- is_public, is_approved, status
- is_premium, price, license_type
- usage_count, download_count, favorite_count
- rating_average, total_ratings
- total_sales, total_revenue
- timestamps, soft_deletes
```

### template_ratings
```sql
- id, template_id, user_id
- rating (1-5), review
- helpful_count
- timestamps
```

### template_favorites
```sql
- id, user_id, template_id
- timestamps
```

### template_purchases
```sql
- id, buyer_id, seller_id, template_id
- price_paid, license_type
- transaction_id, payment_status
- purchased_at, timestamps
```

---

## Contoh Template Format

### Template dengan Placeholder
```
🚨 FLASH SALE ALERT! 🚨

⏰ HANYA [DURASI]!
💥 DISKON [PERSENTASE]% untuk [PRODUK]
🔥 Stok terbatas: [JUMLAH] pcs saja!

✅ [BENEFIT_1]
✅ [BENEFIT_2]
✅ [BENEFIT_3]

⚡ Jangan sampai kehabisan!
📱 Order sekarang: [LINK/KONTAK]

#FlashSale #Diskon #[PRODUK]
```

### Export Format (JSON)
```json
{
  "template_export": {
    "version": "1.0",
    "exported_at": "2026-03-13T10:00:00Z",
    "exported_by": "user@example.com",
    "templates": [
      {
        "title": "Flash Sale Template",
        "description": "Template untuk promo flash sale",
        "category": "event_promo",
        "platform": "instagram",
        "tone": "persuasive",
        "template_content": "...",
        "format_instructions": "...",
        "tags": ["promo", "flash sale"],
        "license_type": "free"
      }
    ]
  }
}
```

---

## User Workflow

### Sebagai Creator (Pembuat Template)
1. Buat template baru
2. Set public/private
3. Set free/premium + harga
4. Submit untuk approval (jika public)
5. Tunggu approval dari admin
6. Template approved → muncul di marketplace
7. Track usage, rating, sales
8. Earn revenue dari premium templates

### Sebagai User (Pengguna Template)
1. Browse template marketplace
2. Filter & search template
3. View template detail
4. Rate & review template
5. Add to favorites
6. Purchase premium template (jika perlu)
7. Use template di AI Generator
8. Export template untuk backup

---

## Tips & Best Practices

### Membuat Template yang Baik
1. **Judul Jelas** - Deskripsikan kegunaan template
2. **Deskripsi Lengkap** - Jelaskan kapan menggunakan template ini
3. **Gunakan Placeholder** - [VARIABLE] untuk bagian yang bisa diisi
4. **Format Instructions** - Berikan panduan penggunaan
5. **Tags Relevan** - Untuk memudahkan pencarian
6. **Test Template** - Coba gunakan sebelum publish

### Monetization Strategy
1. **Free Templates** - Build reputation & followers
2. **Premium Templates** - Advanced/specialized templates
3. **Template Bundles** - Paket template dengan diskon
4. **Exclusive Content** - Premium-only templates
5. **Regular Updates** - Keep templates fresh & relevant

### Marketing Template
1. **Good Title** - SEO-friendly & descriptive
2. **Clear Description** - Benefits & use cases
3. **Screenshots** - Show template in action
4. **Testimonials** - Encourage users to rate
5. **Social Proof** - High usage count & ratings

---

## Troubleshooting

### Template tidak muncul di marketplace
- Cek status: harus "approved"
- Cek visibility: harus "public"
- Refresh page atau clear cache

### Tidak bisa rate template
- Tidak bisa rate template sendiri
- Harus login
- Satu user hanya bisa rate sekali

### Import gagal
- Cek format JSON (harus valid)
- Cek required fields (title, description, category, dll)
- Cek duplicate (template dengan judul sama sudah ada)

### Premium template tidak bisa diakses
- Harus purchase terlebih dahulu
- Cek payment status: harus "completed"
- Owner template bisa akses tanpa purchase

---

## Support & Documentation

**Dokumentasi Lengkap:**
- `AI_TEMPLATE_MARKETPLACE_FEATURES_ANALISIS.md`
- `AI_TEMPLATE_MARKETPLACE_IMPLEMENTATION_COMPLETE.md`

**Code Location:**
- Models: `app/Models/UserTemplate.php`, `TemplateRating.php`, dll
- Controllers: `app/Http/Controllers/Client/TemplateMarketplaceController.php`
- Views: `resources/views/client/template-marketplace/`
- Migrations: `database/migrations/2026_03_13_*`

**Need Help?**
- Check documentation files
- Review code comments
- Test with sample data
- Contact support team

---

## 🎉 Selamat!

Template Marketplace sudah siap digunakan! Mulai buat template pertama Anda dan bagikan ke community! 🚀


# 👥 DOKUMENTASI FITUR PER ROLE

## 📋 DAFTAR ISI

1. [Client (UMKM/Bisnis)](#client-umkmbisnis)
2. [Operator (Siswa SMK)](#operator-siswa-smk)
3. [Guru (Pembimbing)](#guru-pembimbing)
4. [Admin (Pengelola Platform)](#admin-pengelola-platform)

---

## 🛍️ CLIENT (UMKM/BISNIS)

**Role:** Pemilik usaha, UMKM, bisnis yang membutuhkan konten copywriting

### 🎯 Akses Menu:

#### 1. Dashboard
**Path:** `/client/dashboard`

**Fitur:**
- Overview statistik personal
- Total order yang dibuat
- Total spending
- Order status summary
- Quick actions

**Use Case:**
- Lihat ringkasan aktivitas
- Monitor order status
- Quick access ke fitur utama

---

#### 2. AI Generator ⭐ (FITUR UTAMA)
**Path:** `/client/ai-generator`

**Fitur Lengkap:**

##### A. Mode Simpel (Untuk Pemula)
**Input:**
1. Jenis usaha (12 pilihan)
2. Nama produk/jasa
3. Harga (optional)
4. Target market (5 pilihan)
5. Tujuan (4 pilihan: Closing/Branding/Engagement/Viral)
6. Platform (4 pilihan: Instagram/Facebook/TikTok/Shopee)

**Output:**
- Generate pertama: 5 variasi GRATIS 🎉
- Generate berikutnya: 1 caption terbaik

**Keunggulan:**
- Tidak perlu pengetahuan copywriting
- Cukup jawab 6 pertanyaan
- Hasil langsung pakai
- Perfect untuk UMKM pemula

##### B. Mode Advanced (Lengkap)
**Input:**
1. **Kategori** (15+ pilihan):
   - Quick Templates
   - Viral & Clickbait Content
   - Trend & Fresh Ideas
   - Industry Presets (UMKM)
   - Website & Landing Page
   - Iklan (Ads)
   - Social Media Content
   - Marketplace / Toko Online
   - Event & Promosi
   - HR & Recruitment
   - Branding & Tagline
   - Pendidikan & Lembaga
   - Email & WhatsApp Marketing
   - Proposal & Company Profile
   - Personal Branding
   - UX Writing
   - Monetisasi & Penghasilan

2. **Subcategory** (200+ pilihan berdasarkan kategori)

3. **Platform** (50+ pilihan):
   - Social Media: Instagram, Facebook, TikTok, LinkedIn, Twitter, YouTube
   - Marketplace: Shopee, Tokopedia, Bukalapak, Lazada, dll
   - Global: Amazon, eBay, Etsy, dll

4. **Brief/Deskripsi** (detail produk/jasa)

5. **Tone** (6 pilihan):
   - Casual - Santai, friendly
   - Formal - Profesional, resmi
   - Persuasive - Meyakinkan, selling
   - Funny - Humor, entertaining
   - Emotional - Menyentuh, emosional
   - Educational - Edukatif, informatif

6. **Keywords** (optional)

7. **Generate Variations**:
   - 1 caption: GRATIS
   - 5 captions: Rp 5.000
   - 10 captions: Rp 9.000 (hemat 10%)
   - 15 captions: Rp 12.000 (hemat 20%)
   - 20 captions: Rp 15.000 (hemat 25%)

8. **Auto Hashtag** (on/off)
   - Generate hashtag trending Indonesia
   - Niche-specific hashtags
   - Platform-optimized

9. **Bahasa Daerah** (optional):
   - Jawa (Halus/Ngoko)
   - Sunda
   - Betawi
   - Minang
   - Batak

**Output:**
- 1-20 variasi caption sesuai pilihan
- Auto hashtag (jika diaktifkan)
- Platform-optimized format
- Ready to use

**Use Case:**
- Konten promosi harian
- Campaign marketing
- Multiple platform posting
- A/B testing
- Content calendar planning

---

#### 3. Brand Voice Management
**Path:** `/client/brand-voices`

**Fitur:**
- **Create Brand Voice:**
  - Nama brand voice
  - Deskripsi brand
  - Target audience
  - Tone preference
  - Keywords favorit
  - Set as default

- **Load Brand Voice:**
  - Quick load saat generate
  - Auto-fill form dengan preferensi
  - Save time untuk generate berulang

- **Manage Multiple Brands:**
  - Simpan multiple brand voices
  - Switch antar brand dengan mudah
  - Perfect untuk agency/multiple business

**Use Case:**
- Agency dengan multiple clients
- Bisnis dengan multiple products
- Konsistensi brand voice
- Efisiensi waktu

---

#### 4. Caption History
**Path:** `/client/caption-history`

**Fitur:**
- **View History:**
  - Semua caption yang pernah digenerate
  - Filter by date
  - Filter by category
  - Filter by rating
  - Search caption

- **Rate Caption:**
  - Rating 1-5 stars
  - Track performance
  - Learn what works

- **Reuse Caption:**
  - Copy caption yang sukses
  - Edit & customize
  - Generate similar

- **Analytics:**
  - Most used categories
  - Best performing captions
  - Usage statistics

**Use Case:**
- Track caption performance
- Reuse successful captions
- Data-driven decisions
- Content optimization

---

#### 5. Order Request (Human Copywriter)
**Path:** `/client/orders`

**Fitur:**
- **Create Order:**
  - Pilih kategori copywriting
  - Isi brief detail
  - Set deadline
  - Choose operator (optional)
  - Submit order

- **Order Management:**
  - View all orders
  - Track order status:
    - Pending (menunggu operator)
    - In Progress (dikerjakan)
    - Revision (revisi)
    - Completed (selesai)
    - Cancelled (dibatalkan)
  - Download hasil
  - Request revision
  - Rate operator

- **Communication:**
  - Chat dengan operator
  - Upload reference files
  - Feedback & notes

**Use Case:**
- Butuh human touch
- Konten kompleks
- Custom request
- Quality assurance

---

#### 6. Analytics
**Path:** `/client/analytics`

**Fitur:**
- **AI Usage Analytics:**
  - Total generate
  - Category breakdown
  - Platform distribution
  - Spending summary
  - Usage trends

- **Caption Performance:**
  - Top rated captions
  - Most used categories
  - Success rate
  - ROI tracking

- **Recommendations:**
  - Best time to post
  - Best performing categories
  - Optimization tips

**Use Case:**
- Monitor usage
- Optimize strategy
- Budget planning
- Performance tracking

---

#### 7. Profile & Settings
**Path:** `/client/profile`

**Fitur:**
- **Profile Management:**
  - Update profile info
  - Change password
  - Email verification
  - Notification settings

- **Business Info:**
  - Business name
  - Business type
  - Target market
  - Social media links

- **Preferences:**
  - Default tone
  - Default platform
  - Auto hashtag preference
  - Language preference

**Use Case:**
- Personalization
- Account management
- Preference settings

---

#### 8. Notifications
**Path:** `/client/notifications`

**Fitur:**
- Order status updates
- Operator messages
- System announcements
- Promo & tips
- Mark as read
- Delete notifications

**Use Case:**
- Stay updated
- Quick response
- Important alerts

---

### 💰 Pricing untuk Client:

**AI Generator:**
- Generate pertama: 5 variasi GRATIS
- 1 caption: GRATIS
- 5 captions: Rp 5.000
- 10 captions: Rp 9.000
- 15 captions: Rp 12.000
- 20 captions: Rp 15.000

**Human Copywriter (Order):**
- Tergantung kompleksitas
- Negotiable dengan operator
- Platform fee: 20%

---

### 📊 Dashboard Metrics untuk Client:

- Total AI Generates
- Total Orders
- Total Spending
- Active Orders
- Completed Orders
- Favorite Categories
- Usage This Month
- Credits/Balance

---

### ✅ Keunggulan untuk Client:

1. **Self-Service AI Generator** - Generate sendiri kapan saja
2. **Multiple Variations** - Pilih yang terbaik
3. **Brand Voice** - Konsistensi brand
4. **Caption History** - Track & reuse
5. **Human Backup** - Order ke operator jika perlu
6. **Analytics** - Data-driven decisions
7. **Affordable** - Pricing terjangkau
8. **Time-Saving** - Generate dalam detik

---

### 🎯 User Journey Client:

**First Time User:**
1. Register → Email verification
2. Complete profile
3. Tutorial AI Generator (Mode Simpel)
4. Generate pertama (5 variasi GRATIS)
5. Rate caption
6. Explore Mode Advanced

**Regular User:**
1. Login
2. Load Brand Voice (jika ada)
3. Generate caption (Mode Simpel/Advanced)
4. Copy & use
5. Rate caption
6. Track analytics

**Power User:**
1. Multiple brand voices
2. Bulk generate untuk content calendar
3. A/B testing dengan variations
4. Analytics untuk optimization
5. Order ke operator untuk konten kompleks

---

### 📱 Mobile Responsive:

Semua fitur client fully responsive untuk:
- Desktop
- Tablet
- Mobile phone

**Optimized untuk:**
- Quick generate on-the-go
- Easy copy-paste
- Mobile-friendly interface

---

### 🔔 Notifications untuk Client:

- Order accepted by operator
- Order in progress
- Order completed
- Revision requested
- New message from operator
- Payment confirmation
- Promo & tips
- System updates

---

### 💡 Tips untuk Client:

**Maximize AI Generator:**
1. Gunakan Mode Simpel untuk quick content
2. Gunakan Mode Advanced untuk customization
3. Save Brand Voice untuk efisiensi
4. Generate multiple variations untuk A/B testing
5. Rate caption untuk track performance
6. Gunakan auto hashtag untuk reach
7. Explore different categories
8. Try bahasa daerah untuk local market

**When to Order Human Copywriter:**
1. Konten sangat kompleks
2. Butuh research mendalam
3. Long-form content (artikel, blog)
4. Butuh multiple revisions
5. Custom request khusus
6. Quality assurance penting

---

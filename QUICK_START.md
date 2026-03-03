# Quick Start Guide - Smart Copy SMK

## 🚀 Setup dalam 5 Menit

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database
```bash
# Database sudah menggunakan SQLite (sudah ada)
php artisan migrate:fresh --seed
```

### 4. (Optional) Setup OpenAI API
Edit file `.env`:
```env
OPENAI_API_KEY=sk-your-openai-api-key-here
```

Dapatkan API key gratis di: https://platform.openai.com/api-keys

### 5. Run Development Server
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite (assets)
npm run dev
```

### 6. Akses Aplikasi
Buka browser: **http://localhost:8000**

## 👥 Test Users

Setelah seeding, gunakan kredensial berikut:

### Client Account
```
Email: client@test.com
Password: password
```
**Akses**: Dashboard client, subscribe paket, submit request

### Operator Account
```
Email: operator@test.com
Password: password
```
**Akses**: Dashboard operator, queue, assign & complete request

### Admin Account
```
Email: admin@test.com
Password: password
```
**Akses**: Dashboard admin, full analytics, user management

## 🎯 Testing Flow

### Scenario 1: Client Subscribe & Request Copywriting

1. **Login sebagai Client**
   - Email: client@test.com
   - Password: password

2. **Browse Packages**
   - Klik menu "Packages" atau akses `/packages`
   - Lihat 3 paket: Basic (Rp 50k), Professional (Rp 150k), Enterprise (Rp 300k)

3. **Subscribe Paket**
   - Klik "Pilih Paket" pada paket Basic
   - Submit order
   - Redirect ke dashboard → Lihat paket aktif

4. **Submit Copywriting Request**
   - Dari dashboard, klik "Request Copywriting"
   - Isi form:
     - Type: Caption
     - Platform: Instagram
     - Brief: "Promosi menu baru warung makan saya"
     - Tone: Casual
     - Keywords: "enak, murah, dekat kampus"
   - Submit
   - Lihat AI-generated content (jika API key valid)

5. **Check Status**
   - Kembali ke dashboard
   - Lihat request di "Request Terbaru"
   - Status: Pending (menunggu operator)

### Scenario 2: Operator Handle Request

1. **Login sebagai Operator**
   - Logout dari client
   - Login dengan operator@test.com / password

2. **View Queue**
   - Dashboard operator → Lihat "Antrian Request Pending"
   - Atau akses `/operator/queue`

3. **Assign Request**
   - Klik "Ambil" pada request yang ada
   - Request pindah ke "Assigned to Me"
   - Status berubah: In Progress

4. **Complete Request**
   - Lihat AI-generated content
   - Edit/improve content
   - Klik "Kerjakan"
   - Isi final content
   - Submit
   - Status: Completed

### Scenario 3: Client Review & Rate

1. **Login kembali sebagai Client**
   - Logout dari operator
   - Login dengan client@test.com / password

2. **Review Result**
   - Dashboard → Lihat request completed
   - Klik untuk lihat detail
   - Review final content

3. **Request Revision (Optional)**
   - Jika perlu perbaikan
   - Klik "Request Revision"
   - Isi notes: "Tolong tambahkan emoji"
   - Submit

4. **Give Rating**
   - Setelah puas dengan hasil
   - Klik "Beri Rating"
   - Pilih 5 bintang
   - Isi feedback: "Bagus sekali!"
   - Submit

### Scenario 4: Admin Monitor

1. **Login sebagai Admin**
   - Login dengan admin@test.com / password

2. **View Analytics**
   - Dashboard admin menampilkan:
     - Total clients: 1
     - Total operators: 1
     - Active orders: 1
     - Total revenue: Rp 50.000
     - Pending requests: 0
     - Completed today: 1

3. **Check Top Operators**
   - Lihat leaderboard operator
   - Sorted by completed count & rating

4. **View Recent Orders**
   - Table dengan semua orders
   - Client info, package, dates, status

## 📁 Struktur File Penting

```
smart-copy-smk/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php      # Role-based dashboard
│   │   │   ├── PackageController.php        # Package listing
│   │   │   ├── OrderController.php          # Order management
│   │   │   └── CopywritingRequestController.php  # Request workflow
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php           # Role authorization
│   │   └── Policies/
│   │       ├── OrderPolicy.php              # Order authorization
│   │       └── CopywritingRequestPolicy.php # Request authorization
│   ├── Models/
│   │   ├── User.php                         # User with roles
│   │   ├── Package.php                      # Service packages
│   │   ├── Order.php                        # Subscriptions
│   │   ├── Project.php                      # Business profiles
│   │   └── CopywritingRequest.php           # Copywriting requests
│   └── Services/
│       └── AIService.php                    # OpenAI integration
├── database/
│   ├── migrations/                          # Database schema
│   └── seeders/
│       ├── PackageSeeder.php                # 3 packages
│       └── DatabaseSeeder.php               # Test users
├── resources/
│   └── views/
│       ├── packages/
│       │   └── index.blade.php              # Landing page
│       └── dashboard/
│           ├── client.blade.php             # Client dashboard
│           ├── operator.blade.php           # Operator dashboard
│           └── admin.blade.php              # Admin dashboard
├── routes/
│   └── web.php                              # All routes
└── Dokumentasi/
    ├── BUSINESS_PLAN.md                     # Business model
    ├── PLATFORM_FEATURES.md                 # Feature specs
    ├── MARKETING_STRATEGY.md                # Marketing plan
    ├── CURRICULUM_INTEGRATION.md            # School integration
    ├── DEPLOYMENT_GUIDE.md                  # Production deployment
    ├── ROLE_SYSTEM_GUIDE.md                 # Role documentation
    ├── TESTING_GUIDE.md                     # Testing checklist
    └── FINAL_SUMMARY.md                     # Project summary
```

## 🔧 Troubleshooting

### Error: "Class 'OpenAI' not found"
**Solusi**: OpenAI API optional, platform tetap jalan tanpa AI generation
```bash
# Atau install OpenAI client
composer require openai-php/client
```

### Error: Database connection
**Solusi**: Pastikan menggunakan SQLite
```bash
# Check .env
DB_CONNECTION=sqlite

# Re-migrate
php artisan migrate:fresh --seed
```

### Error: 403 Forbidden saat akses route
**Solusi**: Pastikan login dengan role yang benar
- Client → `/orders`, `/copywriting`
- Operator → `/operator/queue`
- Admin → semua route

### Error: Vite not found
**Solusi**: Install npm dependencies
```bash
npm install
npm run dev
```

## 📚 Dokumentasi Lengkap

Baca dokumentasi lengkap di folder root:

1. **BUSINESS_PLAN.md** - Untuk memahami model bisnis
2. **PLATFORM_FEATURES.md** - Untuk lihat semua fitur
3. **MARKETING_STRATEGY.md** - Untuk strategi pemasaran
4. **CURRICULUM_INTEGRATION.md** - Untuk integrasi sekolah
5. **ROLE_SYSTEM_GUIDE.md** - Untuk memahami sistem role
6. **TESTING_GUIDE.md** - Untuk testing lengkap
7. **DEPLOYMENT_GUIDE.md** - Untuk deploy production

## 🎓 Next Steps

### Untuk Siswa
1. Explore semua fitur yang sudah ada
2. Baca dokumentasi teknis
3. Coba buat fitur baru (lihat roadmap)
4. Practice Git workflow
5. Join komunitas Laravel Indonesia

### Untuk Guru
1. Review dokumentasi bisnis
2. Setup lab komputer
3. Prepare training materials
4. Coordinate dengan UMKM lokal
5. Plan soft launch

### Untuk Development
1. Complete UI/UX untuk semua pages
2. Add image upload functionality
3. Implement email notifications
4. Add content calendar
5. Create template library
6. Build analytics dashboard

## 💡 Tips

- **Gunakan Git**: Commit setiap perubahan
- **Test Dulu**: Sebelum deploy, test semua fitur
- **Dokumentasi**: Update docs saat ada perubahan
- **Code Review**: Review code bersama tim
- **Ask Questions**: Jangan ragu bertanya di komunitas

## 🆘 Butuh Bantuan?

### Komunitas Laravel Indonesia
- Telegram: https://t.me/laravelindonesia
- Forum: https://laracasts.com/discuss
- Discord: Laravel Indonesia

### Resources
- Laravel Docs: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- OpenAI API: https://platform.openai.com/docs

## ✅ Checklist Setup

- [ ] Dependencies installed
- [ ] Environment configured
- [ ] Database migrated & seeded
- [ ] Development server running
- [ ] Can access landing page
- [ ] Can login as client
- [ ] Can login as operator
- [ ] Can login as admin
- [ ] All dashboards working
- [ ] Routes accessible by role

**Jika semua checklist ✅, Anda siap mulai development!** 🎉

---

**Happy Coding!** 🚀

# Smart Copy SMK 🚀

Platform digital berbasis AI untuk layanan copywriting UMKM, dikelola oleh siswa SMK sebagai Teaching Factory.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![License](https://img.shields.io/badge/License-MIT-green)

## 📖 Tentang Proyek

Smart Copy SMK adalah platform yang menggabungkan:
- 🤖 **Teknologi AI** (OpenAI GPT) untuk generate copywriting
- 👨‍🎓 **Siswa SMK** sebagai operator yang edit & improve content
- 💼 **UMKM** sebagai client yang mendapat layanan terjangkau
- 🏫 **Sekolah** sebagai teaching factory yang menghasilkan revenue

### Tujuan
1. Memberikan pengalaman kerja nyata kepada siswa SMK
2. Membantu UMKM mendapat konten promosi berkualitas dengan harga terjangkau
3. Mengintegrasikan pembelajaran TKJ dan Bahasa Indonesia
4. Menghasilkan revenue untuk sekolah dan insentif siswa

## ✨ Fitur Utama

### Untuk Client (UMKM)
- 📦 Subscribe paket layanan (Basic, Professional, Enterprise)
- ✍️ Submit copywriting request (caption, deskripsi produk, dll)
- 🔄 Request revisi sesuai limit paket
- ⭐ Beri rating & feedback
- 📊 Dashboard dengan statistik personal

### Untuk Operator (Siswa)
- 📋 Lihat queue request pending
- 🎯 Assign request ke diri sendiri
- 🤖 Generate AI copywriting
- ✏️ Edit & improve AI-generated content
- 📤 Submit hasil ke client
- 📈 Track performance & rating

### Untuk Admin (Guru)
- 👥 User management
- 💰 Revenue tracking
- 📊 Analytics & reports
- 🏆 Top operators leaderboard
- ⚙️ System settings

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (sudah included)

### Installation

```bash
# 1. Clone repository
git clone https://github.com/your-username/smart-copy-smk.git
cd smart-copy-smk

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Setup database
php artisan migrate:fresh --seed

# 5. (Optional) Add OpenAI API key di .env
# OPENAI_API_KEY=sk-your-key-here

# 6. Run development server
php artisan serve
npm run dev
```

### Test Users

Setelah seeding, gunakan kredensial ini:

| Role | Email | Password |
|------|-------|----------|
| Client | client@test.com | password |
| Operator | operator@test.com | password |
| Admin | admin@test.com | password |

## 📚 Dokumentasi

### Dokumentasi Bisnis
- [📋 Business Plan](BUSINESS_PLAN.md) - Model bisnis & strategi operasional
- [🎯 Platform Features](PLATFORM_FEATURES.md) - Spesifikasi fitur lengkap
- [📢 Marketing Strategy](MARKETING_STRATEGY.md) - Strategi pemasaran digital & offline
- [🎓 Curriculum Integration](CURRICULUM_INTEGRATION.md) - Integrasi dengan kurikulum SMK

### Dokumentasi Teknis
- [⚡ Quick Start](QUICK_START.md) - Panduan setup & testing cepat
- [🔧 Implementation Guide](README_IMPLEMENTATION.md) - Panduan implementasi untuk siswa
- [🚀 Deployment Guide](DEPLOYMENT_GUIDE.md) - Panduan deploy ke production
- [👥 Role System Guide](ROLE_SYSTEM_GUIDE.md) - Dokumentasi sistem role
- [✅ Testing Guide](TESTING_GUIDE.md) - Panduan testing lengkap
- [📝 Implementation Checklist](IMPLEMENTATION_CHECKLIST.md) - Checklist development

### Ringkasan
- [📊 Project Summary](PROJECT_SUMMARY.md) - Ringkasan eksekutif proyek
- [🎉 Final Summary](FINAL_SUMMARY.md) - Summary lengkap dengan next steps

## 🏗️ Teknologi

### Backend
- **Framework**: Laravel 11
- **Database**: SQLite (development), MySQL (production)
- **AI**: OpenAI GPT-3.5 Turbo
- **Authentication**: Laravel Breeze

### Frontend
- **CSS**: Tailwind CSS
- **JavaScript**: Vanilla JS / Alpine.js
- **Build Tool**: Vite

### Infrastructure
- **Server**: Nginx/Apache
- **Hosting**: VPS (DigitalOcean/Vultr) atau Shared Hosting
- **SSL**: Let's Encrypt

## 📊 Paket Layanan

| Paket | Harga | Caption | Deskripsi Produk | Revisi | Response Time |
|-------|-------|---------|------------------|--------|---------------|
| **Basic** | Rp 50.000/bulan | 20 | 0 | 1x | 24 jam |
| **Professional** | Rp 150.000/bulan | 50 | 5 | 3x | 12 jam |
| **Enterprise** | Rp 300.000/bulan | Unlimited | 20 | Unlimited | 6 jam |

## 🎯 Roadmap

### ✅ Phase 0: Foundation (Selesai)
- [x] Database schema
- [x] Authentication & authorization
- [x] Role-based system
- [x] Basic controllers & models
- [x] Dashboard untuk semua role
- [x] Dokumentasi lengkap

### 🚧 Phase 1: Core UI/UX (Week 1-2)
- [ ] Client interface lengkap
- [ ] Operator interface lengkap
- [ ] Admin interface lengkap
- [ ] Responsive design

### 📅 Phase 2: Features (Week 3-4)
- [ ] Notification system
- [ ] Template library
- [ ] Content calendar
- [ ] File upload

### 🔮 Phase 3: Advanced (Week 5-6)
- [ ] Analytics & reports
- [ ] Payment gateway
- [ ] Social media integration
- [ ] WhatsApp integration

### 🚀 Phase 4: Launch (Week 7-8)
- [ ] Testing menyeluruh
- [ ] Performance optimization
- [ ] Security hardening
- [ ] Soft launch
- [ ] Marketing campaign

## 👥 Tim

### Struktur Tim Ideal
- **Tim Teknis** (4 siswa TKJ): Backend, frontend, integration, devops
- **Tim Konten** (4 siswa): Content lead, copywriters, strategist
- **Tim Marketing** (4 siswa): Marketing lead, social media, CS, sales
- **Supervisor** (3 guru): TKJ, Bahasa Indonesia, PKK

## 💰 Proyeksi Bisnis

### Biaya Operasional (Bulanan)
- VPS/Hosting: Rp 90.000
- Domain: Rp 15.000
- OpenAI API: Rp 300.000
- Marketing: Rp 500.000
- **Total**: Rp 905.000/bulan

### Target Revenue
- **Bulan 1-3**: 10 clients = Rp 500.000
- **Bulan 4-6**: 30 clients = Rp 1.500.000
- **Bulan 7-12**: 50+ clients = Rp 2.500.000+

### Break Even Point
Target: Bulan ke-3 dengan 20 clients mixed packages

## 🤝 Contributing

Kami welcome kontribusi dari:
- Siswa SMK lain yang ingin belajar
- Developer yang ingin membantu
- Guru yang ingin adopt model ini
- UMKM yang ingin feedback

### How to Contribute
1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Contact

**Smart Copy SMK**
- Email: info@smartcopysmk.com
- Instagram: [@smartcopysmk](https://instagram.com/smartcopysmk)
- WhatsApp: +62-xxx-xxxx-xxxx

**For Technical Support**:
- GitHub Issues: [Create Issue](https://github.com/your-username/smart-copy-smk/issues)
- Email: tech@smartcopysmk.com

## 🙏 Acknowledgments

- Laravel Framework
- OpenAI API
- Tailwind CSS
- Komunitas Laravel Indonesia
- Semua siswa & guru yang terlibat

## 📸 Screenshots

### Landing Page
![Landing Page](docs/screenshots/landing.png)

### Client Dashboard
![Client Dashboard](docs/screenshots/client-dashboard.png)

### Operator Dashboard
![Operator Dashboard](docs/screenshots/operator-dashboard.png)

### Admin Dashboard
![Admin Dashboard](docs/screenshots/admin-dashboard.png)

---

**Dibuat dengan ❤️ oleh Siswa SMK untuk UMKM Indonesia**

**Mari bersama memajukan pendidikan vokasi dan ekonomi digital!** 🚀

# Smart Copy SMK - Panduan Implementasi

## Setup Awal

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database
Edit file `.env`:
```
DB_CONNECTION=sqlite
```

Jalankan migrasi:
```bash
php artisan migrate
php artisan db:seed --class=PackageSeeder
```

### 4. Konfigurasi OpenAI
Tambahkan API key OpenAI di file `.env`:
```
OPENAI_API_KEY=sk-your-api-key-here
```

Dapatkan API key dari: https://platform.openai.com/api-keys

### 5. Compile Assets
```bash
npm run dev
```

### 6. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: http://localhost:8000

## Struktur Database

### Tables
1. **users** - Data pengguna (client & operator)
2. **packages** - Paket layanan (Basic, Professional, Enterprise)
3. **projects** - Profil bisnis client
4. **orders** - Subscription aktif client
5. **copywriting_requests** - Request copywriting dari client

## Fitur yang Sudah Diimplementasikan

### Backend
- ✅ Database schema lengkap
- ✅ Model dengan relationships
- ✅ AI Service untuk generate copywriting
- ✅ Package seeder (3 paket)

### Yang Perlu Dikembangkan Selanjutnya

#### Phase 1 - Core Features (Minggu 1-2)
1. Authentication & Authorization
   - Register/Login untuk client
   - Role management (client, operator, admin)
   
2. Dashboard Client
   - View paket yang tersedia
   - Subscribe paket
   - Submit copywriting request
   - View history & status

3. Dashboard Operator (Siswa)
   - View queue order
   - Generate AI copywriting
   - Edit & quality control
   - Submit ke client

#### Phase 2 - Advanced Features (Minggu 3-4)
1. Revision System
   - Client request revisi
   - Track revision count
   - Approval workflow

2. Rating & Feedback
   - Client beri rating
   - Feedback untuk improvement

3. Analytics Dashboard
   - Performance metrics
   - Client satisfaction
   - Revenue tracking

#### Phase 3 - Enhancement (Minggu 5-6)
1. Content Calendar
2. Template Library
3. Hashtag Generator
4. Multi-platform optimizer

## Panduan untuk Siswa

### Tim Teknis (TKJ)
**Tugas**:
- Maintenance server & database
- Develop fitur baru
- Bug fixing
- Monitoring performance

**Tools yang Perlu Dikuasai**:
- Laravel framework
- MySQL/SQLite
- Git version control
- API integration

### Tim Konten (Bahasa Indonesia)
**Tugas**:
- Review AI-generated content
- Edit & improve copywriting
- Quality assurance
- Maintain brand voice

**Skills yang Perlu Dikuasai**:
- Copywriting techniques
- Grammar & spelling
- Tone adjustment
- Platform-specific writing

### Tim Marketing
**Tugas**:
- Customer service
- Social media management
- Client onboarding
- Collect feedback

**Skills yang Perlu Dikuasai**:
- Communication
- Problem solving
- Social media marketing
- Customer relationship

## Workflow Operasional

### 1. Client Submit Request
```
Client login → Pilih project → Submit brief → Upload foto (optional)
```

### 2. AI Generate Draft
```
System auto-generate → Save as draft → Notify operator
```

### 3. Operator Review
```
Operator login → View queue → Review AI draft → Edit if needed → Submit
```

### 4. Client Review
```
Client notified → Review content → Approve/Request revision
```

### 5. Completion
```
If approved → Mark completed → Client can rate → Update quota
```

## Tips Pengembangan

### Untuk Guru Pembimbing
1. Mulai dengan fitur sederhana dulu
2. Buat dokumentasi lengkap setiap fitur
3. Code review rutin setiap minggu
4. Encourage siswa untuk eksperimen

### Untuk Siswa
1. Jangan takut error, itu bagian dari belajar
2. Gunakan Git untuk version control
3. Test setiap fitur sebelum deploy
4. Dokumentasikan setiap perubahan

## Resources Belajar

### Laravel
- https://laravel.com/docs
- https://laracasts.com (video tutorial)

### Copywriting
- https://copyblogger.com
- https://www.copyhackers.com

### AI Integration
- https://platform.openai.com/docs
- https://cookbook.openai.com

## Troubleshooting

### Error: "Class 'OpenAI' not found"
```bash
composer require openai-php/client
```

### Error: Database connection
```bash
php artisan migrate:fresh
```

### Error: Permission denied
```bash
chmod -R 775 storage bootstrap/cache
```

## Next Steps

1. Buat UI/UX design mockup
2. Implement authentication
3. Build dashboard client
4. Build dashboard operator
5. Testing dengan real client
6. Soft launch
7. Collect feedback
8. Iterate & improve

## Contact & Support

Untuk pertanyaan teknis, hubungi:
- Guru TKJ: [email/phone]
- Guru Bahasa Indonesia: [email/phone]
- Tim Teknis: [email/phone]

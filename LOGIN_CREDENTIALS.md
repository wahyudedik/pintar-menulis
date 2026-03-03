# 🔐 Login Credentials - Smart Copy SMK

## Test Accounts

Semua password: `password`

### 👤 Client (Pembeli Jasa)
- **Email**: client@test.com
- **Password**: password
- **Akses**:
  - Dashboard client
  - AI Generator (generate copywriting instant)
  - Browse Operators (cari freelancer)
  - Submit order ke operator
  - View orders history

### 💼 Operator (Freelancer) - Test Account
- **Email**: operator@test.com
- **Password**: password
- **Akses**:
  - Dashboard operator
  - View incoming orders
  - Accept/reject orders
  - Workspace untuk mengerjakan
  - Earnings dashboard

### 💼 Sample Operators (5 Operators dengan Profile Lengkap)

1. **Budi Santoso**
   - Email: budi@operator.com
   - Spesialisasi: Social Media, Ads, Marketplace
   - Base Price: Rp 75.000
   - Rating: 4.9 ⭐ (156 reviews)
   - Completed: 234 orders

2. **Siti Nurhaliza**
   - Email: siti@operator.com
   - Spesialisasi: UX Writing, Website, Landing Page
   - Base Price: Rp 150.000
   - Rating: 4.8 ⭐ (89 reviews)
   - Completed: 145 orders

3. **Ahmad Fauzi**
   - Email: ahmad@operator.com
   - Spesialisasi: Email Marketing, Proposal, Company Profile
   - Base Price: Rp 100.000
   - Rating: 4.7 ⭐ (67 reviews)
   - Completed: 98 orders

4. **Dewi Lestari**
   - Email: dewi@operator.com
   - Spesialisasi: Personal Branding, LinkedIn, Bio Instagram
   - Base Price: Rp 120.000
   - Rating: 4.9 ⭐ (112 reviews)
   - Completed: 178 orders

5. **Rudi Hartono**
   - Email: rudi@operator.com
   - Spesialisasi: Marketplace, Product Description, SEO
   - Base Price: Rp 60.000
   - Rating: 4.6 ⭐ (203 reviews)
   - Completed: 312 orders

### 👨‍🏫 Guru (ML Trainer)
- **Email**: guru@test.com
- **Password**: password
- **Akses**:
  - Dashboard guru
  - ML Training interface
  - Review AI outputs
  - Rate & correct copywriting
  - Submit training data

### 👑 Admin (Platform Manager)
- **Email**: admin@test.com
- **Password**: password
- **Akses**:
  - Dashboard admin (full analytics)
  - User management
  - Package management
  - Financial reports
  - System settings

## 🚀 Quick Start Testing

### Test Flow 1: AI Generator (Client)
1. Login sebagai client@test.com
2. Klik "AI Generator" di navbar
3. Pilih kategori & subcategory
4. Isi brief & pilih tone
5. Klik "Generate"
6. Copy hasil atau save to history

### Test Flow 2: Marketplace (Client → Operator)
1. Login sebagai client@test.com
2. Klik "Browse Operators" di navbar
3. Browse & filter operators
4. Klik "Pilih" pada operator
5. Isi form order (category, brief, budget, deadline)
6. Submit order
7. Order tersimpan ke database
8. Redirect ke "My Orders"

### Test Flow 3: Operator Dashboard
1. Login sebagai operator@test.com
2. View dashboard operator
3. Lihat incoming orders (jika ada)
4. Accept order
5. Kerjakan di workspace
6. Submit hasil ke client

### Test Flow 4: Admin Analytics
1. Login sebagai admin@test.com
2. View full analytics
3. Monitor revenue
4. See top operators
5. Manage users & packages

## 📊 Database Stats

Total Users: **9**
- Clients: 1
- Operators: 6 (5 sample + 1 test)
- Admins: 1
- Gurus: 1

Total Packages: **3**
- Starter (Rp 99k/bulan)
- Professional (Rp 299k/bulan)
- Enterprise (Rp 999k/bulan)

Total Operator Profiles: **6**
- All verified & available
- Ready untuk menerima orders

## 🔄 Reset Database

Jika ingin reset database ke kondisi awal:

```bash
php artisan migrate:fresh --seed
```

Ini akan:
1. Drop semua tables
2. Run migrations
3. Seed packages (3 packages)
4. Seed operators (5 sample operators)
5. Create test users (client, operator, admin, guru)
6. Create operator profiles

## 🎯 Testing Checklist

### Client Features
- [ ] Login sebagai client
- [ ] Generate copywriting dengan AI
- [ ] Browse operators
- [ ] Filter & sort operators
- [ ] Submit order ke operator
- [ ] View order history
- [ ] View dashboard stats

### Operator Features
- [ ] Login sebagai operator
- [ ] View dashboard
- [ ] See incoming orders
- [ ] Accept/reject orders
- [ ] Work on orders
- [ ] Submit completed work
- [ ] View earnings

### Admin Features
- [ ] Login sebagai admin
- [ ] View analytics
- [ ] Manage users
- [ ] Manage packages
- [ ] View financial reports

### Guru Features
- [ ] Login sebagai guru
- [ ] View training dashboard
- [ ] Review AI outputs
- [ ] Rate & correct
- [ ] Submit training data

## 🔒 Security Notes

1. **Production**: Ganti semua password default!
2. **Environment**: Jangan commit .env ke git
3. **API Keys**: Simpan di .env, jangan hardcode
4. **CSRF**: Sudah enabled untuk semua POST requests
5. **Validation**: Server-side validation untuk semua inputs

## 📝 Notes

- Semua sample operators bisa login dengan password: `password`
- Test operator (operator@test.com) untuk development testing
- Sample operators (budi, siti, dll) untuk demo & showcase
- Client bisa submit order ke semua operators yang available
- Orders tersimpan dengan status 'pending' by default

# Panduan Testing Smart Copy SMK

## Setup Testing Environment

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Database Testing
```bash
# Copy .env untuk testing
cp .env .env.testing

# Edit .env.testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### 3. Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

## Manual Testing Checklist

### A. Testing Role System

#### Test 1: Client Registration & Login
**Steps**:
1. Buka browser → http://localhost:8000/register
2. Register dengan:
   - Name: Test Client
   - Email: client@test.com
   - Password: password
3. Login dengan kredensial di atas
4. Verify: Redirect ke dashboard client
5. Verify: Badge "Client" muncul di navbar
6. Verify: Menu yang muncul sesuai role client

**Expected Result**:
- ✅ Registration berhasil
- ✅ Role otomatis 'client'
- ✅ Dashboard client tampil
- ✅ Tidak bisa akses `/operator/queue`

#### Test 2: Operator Login
**Steps**:
1. Buat user operator via tinker:
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Test Operator',
    'email' => 'operator@test.com',
    'password' => bcrypt('password'),
    'role' => 'operator'
]);
```
2. Login sebagai operator
3. Verify: Dashboard operator tampil
4. Verify: Badge "Operator" muncul
5. Coba akses `/orders` → Should get 403

**Expected Result**:
- ✅ Login berhasil
- ✅ Dashboard operator tampil
- ✅ Tidak bisa akses route client
- ✅ Bisa akses `/operator/queue`

#### Test 3: Admin Login
**Steps**:
1. Buat user admin via tinker:
```php
User::create([
    'name' => 'Test Admin',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```
2. Login sebagai admin
3. Verify: Dashboard admin tampil
4. Verify: Badge "Admin" muncul
5. Verify: Bisa akses semua route

**Expected Result**:
- ✅ Login berhasil
- ✅ Dashboard admin dengan full stats
- ✅ Bisa akses operator routes
- ✅ Bisa akses admin routes

### B. Testing Package & Order Flow

#### Test 4: Browse & Subscribe Package
**Login as**: Client

**Steps**:
1. Akses `/packages`
2. Verify: 3 paket tampil (Basic, Professional, Enterprise)
3. Klik "Pilih Paket" pada paket Basic
4. Verify: Redirect ke form order
5. Submit order
6. Verify: Order berhasil dibuat
7. Verify: Redirect ke detail order
8. Verify: Quota tampil dengan benar

**Expected Result**:
- ✅ Paket tampil lengkap dengan harga
- ✅ Order berhasil dibuat
- ✅ Status order = 'active'
- ✅ Start date = today
- ✅ End date = today + 1 month

#### Test 5: Submit Copywriting Request
**Login as**: Client (yang sudah punya order aktif)

**Steps**:
1. Dari dashboard, klik "Request Copywriting"
2. Pilih order aktif
3. Isi form:
   - Type: caption
   - Platform: instagram
   - Brief: "Promosi menu baru warung makan"
   - Tone: casual
   - Keywords: "enak, murah, dekat"
4. Submit
5. Verify: Request berhasil dibuat
6. Verify: AI generated content muncul
7. Verify: Status = 'pending'

**Expected Result**:
- ✅ Form validation bekerja
- ✅ AI generate content (jika API key valid)
- ✅ Request tersimpan di database
- ✅ Quota belum berkurang (masih pending)

### C. Testing Operator Workflow

#### Test 6: View Queue & Assign Request
**Login as**: Operator

**Steps**:
1. Akses `/operator/queue`
2. Verify: Request pending tampil
3. Klik "Ambil" pada salah satu request
4. Verify: Request status berubah 'in_progress'
5. Verify: Request muncul di "Assigned to Me"
6. Verify: assigned_to = operator user id

**Expected Result**:
- ✅ Queue tampil dengan benar
- ✅ Assign berhasil
- ✅ Status update ke 'in_progress'
- ✅ Operator bisa lihat detail request

#### Test 7: Complete Copywriting Request
**Login as**: Operator (yang sudah assign request)

**Steps**:
1. Dari dashboard, lihat assigned request
2. Klik "Kerjakan"
3. Edit AI-generated content
4. Isi final_content
5. Set status = 'completed'
6. Submit
7. Verify: Status berubah 'completed'
8. Verify: completed_at terisi
9. Verify: Quota client berkurang

**Expected Result**:
- ✅ Update berhasil
- ✅ Status = 'completed'
- ✅ Client bisa lihat hasil
- ✅ Quota berkurang sesuai type

### D. Testing Revision Flow

#### Test 8: Client Request Revision
**Login as**: Client

**Steps**:
1. Lihat completed request
2. Klik "Request Revision"
3. Isi revision notes: "Tolong tambahkan emoji"
4. Submit
5. Verify: Status berubah 'revision'
6. Verify: revision_count bertambah
7. Verify: Operator bisa lihat revision notes

**Expected Result**:
- ✅ Revision request berhasil
- ✅ Status update ke 'revision'
- ✅ revision_count increment
- ✅ Tidak melebihi revision_limit

#### Test 9: Revision Limit Check
**Login as**: Client

**Steps**:
1. Request revisi sampai mencapai limit
2. Coba request revisi lagi
3. Verify: Error message muncul
4. Verify: Request tidak bisa direvisi lagi

**Expected Result**:
- ✅ Error: "Batas revisi sudah tercapai"
- ✅ Status tetap 'completed'
- ✅ revision_count tidak bertambah

### E. Testing Rating & Feedback

#### Test 10: Client Give Rating
**Login as**: Client

**Steps**:
1. Lihat completed request
2. Klik "Beri Rating"
3. Pilih rating: 5 bintang
4. Isi feedback: "Bagus sekali!"
5. Submit
6. Verify: Rating tersimpan
7. Verify: Feedback tersimpan
8. Login as operator → Verify rating tampil

**Expected Result**:
- ✅ Rating tersimpan (1-5)
- ✅ Feedback tersimpan
- ✅ Operator bisa lihat rating
- ✅ Average rating di dashboard update

### F. Testing Authorization

#### Test 11: Client Cannot Access Operator Routes
**Login as**: Client

**Steps**:
1. Coba akses `/operator/queue`
2. Verify: 403 Forbidden
3. Coba akses `/admin/users`
4. Verify: 403 Forbidden

**Expected Result**:
- ✅ 403 error muncul
- ✅ Tidak bisa akses route lain

#### Test 12: Operator Cannot Access Client Orders
**Login as**: Operator

**Steps**:
1. Coba akses `/orders`
2. Verify: 403 Forbidden
3. Coba akses `/orders/create/1`
4. Verify: 403 Forbidden

**Expected Result**:
- ✅ 403 error muncul
- ✅ Operator tidak bisa subscribe paket

#### Test 13: Client Cannot View Other Client's Data
**Login as**: Client 1

**Steps**:
1. Buat order sebagai Client 1
2. Catat order ID (misal: 1)
3. Logout, login sebagai Client 2
4. Coba akses `/orders/1`
5. Verify: 403 Forbidden atau redirect

**Expected Result**:
- ✅ Client 2 tidak bisa lihat order Client 1
- ✅ Authorization policy bekerja

### G. Testing Dashboard Stats

#### Test 14: Client Dashboard Stats
**Login as**: Client

**Steps**:
1. Buat beberapa order dan request
2. Akses dashboard
3. Verify stats:
   - Total orders
   - Active orders
   - Pending requests
   - Completed requests

**Expected Result**:
- ✅ Semua stats akurat
- ✅ Real-time update
- ✅ Hanya data milik client

#### Test 15: Operator Dashboard Stats
**Login as**: Operator

**Steps**:
1. Assign beberapa request
2. Complete beberapa request
3. Akses dashboard
4. Verify stats:
   - Assigned to me
   - Completed today
   - Pending queue
   - Average rating

**Expected Result**:
- ✅ Stats akurat
- ✅ Hanya request yang di-assign
- ✅ Average rating benar

#### Test 16: Admin Dashboard Stats
**Login as**: Admin

**Steps**:
1. Akses dashboard admin
2. Verify stats:
   - Total clients
   - Total operators
   - Active orders
   - Total revenue
   - Top operators

**Expected Result**:
- ✅ All stats accurate
- ✅ Revenue calculation correct
- ✅ Top operators sorted by performance

## Automated Testing (Optional)

### Setup PHPUnit
```bash
composer require --dev phpunit/phpunit
```

### Example Test: Role Middleware
```php
// tests/Feature/RoleMiddlewareTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    public function test_client_cannot_access_operator_routes()
    {
        $client = User::factory()->create(['role' => 'client']);
        
        $response = $this->actingAs($client)->get('/operator/queue');
        
        $response->assertStatus(403);
    }
    
    public function test_operator_can_access_queue()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        
        $response = $this->actingAs($operator)->get('/operator/queue');
        
        $response->assertStatus(200);
    }
}
```

### Run Tests
```bash
php artisan test
```

## Performance Testing

### Test 1: Load Testing
```bash
# Install Apache Bench
sudo apt install apache2-utils

# Test 100 requests, 10 concurrent
ab -n 100 -c 10 http://localhost:8000/packages
```

### Test 2: Database Query Optimization
```php
// Enable query log
DB::enableQueryLog();

// Run operation
$orders = Order::with('package', 'user')->get();

// Check queries
dd(DB::getQueryLog());
```

## Security Testing

### Test 1: SQL Injection
- Coba input: `' OR '1'='1` di form
- Expected: Laravel escaping bekerja

### Test 2: XSS Attack
- Coba input: `<script>alert('XSS')</script>` di brief
- Expected: Laravel blade escaping bekerja

### Test 3: CSRF Protection
- Coba submit form tanpa @csrf token
- Expected: 419 error

## Bug Report Template

```markdown
## Bug Report

**Title**: [Brief description]

**Severity**: Critical / High / Medium / Low

**Steps to Reproduce**:
1. Login as [role]
2. Navigate to [page]
3. Click [button]
4. Observe [issue]

**Expected Behavior**:
[What should happen]

**Actual Behavior**:
[What actually happens]

**Screenshots**:
[If applicable]

**Environment**:
- Browser: Chrome 120
- OS: Windows 11
- PHP: 8.2
- Laravel: 11.x
```

## Testing Checklist Summary

- [ ] All roles can login
- [ ] Role-based dashboard works
- [ ] Middleware blocks unauthorized access
- [ ] Client can subscribe packages
- [ ] Client can submit requests
- [ ] Operator can view queue
- [ ] Operator can assign requests
- [ ] Operator can complete requests
- [ ] Client can request revision
- [ ] Revision limit enforced
- [ ] Client can give rating
- [ ] Stats are accurate
- [ ] Authorization policies work
- [ ] No SQL injection vulnerability
- [ ] No XSS vulnerability
- [ ] CSRF protection works

## Next Steps After Testing

1. Fix all bugs found
2. Optimize slow queries
3. Add more validation
4. Improve error messages
5. Add loading states
6. Implement notifications
7. Add email notifications
8. Deploy to staging
9. User acceptance testing
10. Deploy to production

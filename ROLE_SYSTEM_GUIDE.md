# Panduan Sistem Role Smart Copy SMK

## Overview Sistem Role

Platform Smart Copy SMK menggunakan 3 level role untuk mengatur akses dan fungsi:

1. **Client** - Pelanggan UMKM yang menggunakan layanan
2. **Operator** - Siswa SMK yang mengerjakan copywriting
3. **Admin** - Guru/supervisor yang mengelola sistem

## Role Structure

### 1. Client Role

**Akses**:
- Dashboard client dengan statistik personal
- Lihat dan subscribe paket layanan
- Manage projects (profil bisnis)
- Submit copywriting request
- Review dan approve hasil copywriting
- Request revisi
- Memberikan rating dan feedback

**Tidak Bisa**:
- Akses queue operator
- Edit copywriting orang lain
- Lihat data client lain
- Akses admin panel

**Dashboard Features**:
- Total orders
- Active orders
- Pending requests
- Completed requests
- List paket aktif dengan quota
- Recent copywriting requests

### 2. Operator Role

**Akses**:
- Dashboard operator dengan workload
- Lihat queue request pending
- Ambil/assign request ke diri sendiri
- Generate AI copywriting
- Edit dan improve AI-generated content
- Submit hasil ke client
- Handle revision request
- Lihat rating dan feedback

**Tidak Bisa**:
- Subscribe paket (bukan client)
- Akses data pribadi client
- Delete atau cancel order
- Akses admin functions

**Dashboard Features**:
- Assigned to me (jumlah request aktif)
- Completed today
- Pending queue (total)
- Average rating
- List request yang ditugaskan
- Pending queue preview

### 3. Admin Role

**Akses**:
- Semua akses operator
- Dashboard admin dengan analytics
- User management (CRUD users)
- View all orders dan revenue
- Performance reports
- Top operators leaderboard
- System settings
- Package management

**Dashboard Features**:
- Total clients
- Total operators
- Active orders
- Total revenue
- Pending requests
- Completed today
- Recent orders
- Top operators by performance

## Implementasi Teknis

### Database Schema

```sql
-- users table
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'client';
-- Possible values: 'client', 'operator', 'admin'
```

### Middleware

**File**: `app/Http/Middleware/RoleMiddleware.php`

```php
// Protect route untuk single role
Route::middleware(['role:client'])->group(function () {
    // Client-only routes
});

// Protect route untuk multiple roles
Route::middleware(['role:operator,admin'])->group(function () {
    // Operator dan Admin bisa akses
});
```

### Model Methods

**File**: `app/Models/User.php`

```php
// Check role
$user->isClient()    // return boolean
$user->isOperator()  // return boolean
$user->isAdmin()     // return boolean
```

### Authorization Policies

**OrderPolicy**:
- Client hanya bisa view order miliknya sendiri
- Operator tidak bisa akses order
- Admin bisa view semua order

**CopywritingRequestPolicy**:
- Client bisa view request miliknya
- Operator bisa view request yang di-assign ke dia
- Admin bisa view semua request

## Route Structure

### Public Routes
```
GET  /                      - Landing page
GET  /packages              - List paket
GET  /packages/{id}         - Detail paket
```

### Client Routes (middleware: auth, role:client)
```
GET  /dashboard             - Dashboard client
GET  /orders                - List orders
POST /orders                - Subscribe paket
GET  /orders/{id}           - Detail order
GET  /projects              - List projects
POST /projects              - Create project
GET  /copywriting           - List requests
POST /copywriting           - Submit request
POST /copywriting/{id}/revision  - Request revisi
POST /copywriting/{id}/rate      - Beri rating
```

### Operator Routes (middleware: auth, role:operator,admin)
```
GET  /dashboard             - Dashboard operator
GET  /operator/queue        - Antrian request
POST /operator/copywriting/{id}/assign  - Ambil request
PUT  /operator/copywriting/{id}         - Submit hasil
```

### Admin Routes (middleware: auth, role:admin)
```
GET  /dashboard             - Dashboard admin
GET  /admin/users           - User management
GET  /admin/reports         - Reports & analytics
GET  /admin/packages        - Package management
```

## Workflow by Role

### Client Workflow

1. **Register** → Role otomatis 'client'
2. **Browse Packages** → Pilih paket yang sesuai
3. **Subscribe** → Bayar dan aktifkan paket
4. **Create Project** (optional) → Setup profil bisnis
5. **Submit Request** → Isi brief copywriting
6. **Wait** → Operator kerjakan
7. **Review** → Approve atau request revisi
8. **Rate** → Beri rating dan feedback

### Operator Workflow

1. **Login** → Akses dashboard operator
2. **Check Queue** → Lihat request pending
3. **Assign** → Ambil request yang sesuai skill
4. **Generate** → AI generate draft
5. **Edit** → Improve dan quality control
6. **Submit** → Kirim ke client
7. **Handle Revision** → Jika client request revisi
8. **Complete** → Mark as completed

### Admin Workflow

1. **Login** → Akses dashboard admin
2. **Monitor** → Lihat metrics dan KPI
3. **Manage Users** → Add/edit/delete users
4. **Review Performance** → Check operator performance
5. **Generate Reports** → Export data untuk evaluasi
6. **System Settings** → Update packages, pricing, dll

## Testing Role System

### Create Test Users

```bash
php artisan tinker
```

```php
// Create client
User::create([
    'name' => 'Test Client',
    'email' => 'client@test.com',
    'password' => bcrypt('password'),
    'role' => 'client'
]);

// Create operator
User::create([
    'name' => 'Test Operator',
    'email' => 'operator@test.com',
    'password' => bcrypt('password'),
    'role' => 'operator'
]);

// Create admin
User::create([
    'name' => 'Test Admin',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

### Test Scenarios

**Test 1: Client Access**
1. Login sebagai client
2. Coba akses `/operator/queue` → Should get 403 Forbidden
3. Akses `/orders` → Should work
4. Subscribe paket → Should work

**Test 2: Operator Access**
1. Login sebagai operator
2. Coba akses `/orders` → Should get 403 Forbidden
3. Akses `/operator/queue` → Should work
4. Ambil request → Should work

**Test 3: Admin Access**
1. Login sebagai admin
2. Akses `/operator/queue` → Should work
3. Akses `/admin/users` → Should work
4. View all data → Should work

## Security Best Practices

### 1. Always Check Authorization
```php
// In controller
$this->authorize('view', $order);
```

### 2. Use Middleware
```php
// In routes
Route::middleware(['role:client'])->group(function () {
    // Protected routes
});
```

### 3. Validate Role in Forms
```php
// Prevent role escalation
$validated = $request->validate([
    'role' => 'in:client' // Only allow client role in registration
]);
```

### 4. Hide Sensitive Data
```php
// In views
@if(auth()->user()->isAdmin())
    <!-- Show admin-only data -->
@endif
```

## Common Issues & Solutions

### Issue 1: "Unauthorized action" Error
**Cause**: User trying to access route without proper role
**Solution**: Check middleware in routes and user role in database

### Issue 2: Dashboard Not Loading
**Cause**: Missing view file for specific role
**Solution**: Create view file: `resources/views/dashboard/{role}.blade.php`

### Issue 3: Role Not Saved on Registration
**Cause**: 'role' not in fillable array
**Solution**: Add 'role' to `$fillable` in User model

## Future Enhancements

### Planned Features
1. **Permission System**: Granular permissions per role
2. **Team Management**: Operator bisa punya team
3. **Role Hierarchy**: Sub-roles (senior operator, junior operator)
4. **Activity Log**: Track semua actions per role
5. **Role-based Notifications**: Different notifications per role

### Scalability
- Easy to add new roles (e.g., 'supervisor', 'accountant')
- Middleware supports multiple roles
- Policy-based authorization
- Flexible dashboard routing

## Kesimpulan

Sistem role di Smart Copy SMK dirancang untuk:
- **Security**: Setiap role hanya akses yang diperlukan
- **Clarity**: Jelas siapa bisa apa
- **Flexibility**: Mudah extend untuk role baru
- **User Experience**: Dashboard sesuai kebutuhan role

Dengan sistem ini, siswa belajar:
- Authorization & authentication
- Role-based access control (RBAC)
- Security best practices
- Real-world application architecture

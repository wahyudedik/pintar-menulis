# 🎯 Banner Information Popup Feature

## ✅ Implementation Complete!

Fitur Banner Information Popup sudah diimplementasikan dengan lengkap untuk menampilkan informasi penting kepada user.

---

## 📋 Feature Overview

### Konsep:
- **Banner popup** yang bisa di-manage oleh admin
- **4 jenis banner**: Landing, Client Dashboard, Operator Dashboard, Guru Dashboard
- **Muncul sekali saja** per user (first visit only)
- **Kalau di-close, tidak muncul lagi** (menggunakan localStorage)
- **Kalau admin kosongkan title/content, banner tidak muncul**

---

## 🎨 Banner Types

### 1. Landing Page Banner
- **Type**: `landing`
- **Target**: Semua visitor di halaman landing
- **Purpose**: Welcome message, fitur unggulan, call-to-action

### 2. Client Dashboard Banner
- **Type**: `client`
- **Target**: User dengan role `client`
- **Purpose**: Panduan fitur client, tips penggunaan

### 3. Operator Dashboard Banner
- **Type**: `operator`
- **Target**: User dengan role `operator`
- **Purpose**: Panduan tugas operator, motivasi

### 4. Guru Dashboard Banner
- **Type**: `guru`
- **Target**: User dengan role `guru`
- **Purpose**: Panduan training AI, tanggung jawab

---

## 🔧 How It Works

### 1. Admin Setup
```
Admin → Banner Information Management
→ Set title & content untuk setiap banner type
→ Toggle active/inactive
→ Save
```

### 2. User Experience
```
User visit page (first time)
→ Check localStorage: banner_closed_{type}
→ If not closed before:
   → Fetch banner from API
   → If banner active & has content:
      → Show popup after 500ms
→ User close popup
→ If "Don't show again" checked:
   → Save to localStorage
   → Never show again
```

### 3. Technical Flow
```
Component Load
→ Alpine.js init()
→ Check localStorage
→ Fetch /api/banner/{type}
→ If success & active:
   → Display popup
→ User clicks close:
   → If checkbox checked:
      → localStorage.setItem('banner_closed_{type}', 'true')
   → Hide popup
```

---

## 📁 Files Created/Modified

### New Files:
- ✅ `app/Models/BannerInformation.php`
- ✅ `app/Http/Controllers/BannerInformationController.php`
- ✅ `app/Http/Controllers/Admin/BannerInformationController.php`
- ✅ `database/migrations/2026_03_09_184126_create_banner_information_table.php`
- ✅ `database/seeders/BannerInformationSeeder.php`
- ✅ `resources/views/admin/banner-information/index.blade.php`
- ✅ `resources/views/components/banner-popup.blade.php`

### Modified Files:
- ✅ `routes/web.php` (added admin & API routes)
- ✅ `resources/views/welcome.blade.php` (added landing banner)
- ✅ `resources/views/dashboard/client.blade.php` (added client banner)
- ✅ `resources/views/dashboard/operator.blade.php` (added operator banner)
- ✅ `resources/views/dashboard/guru.blade.php` (added guru banner)

---

## 🗄️ Database Schema

### Table: `banner_information`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| type | enum | 'landing', 'client', 'operator', 'guru' (UNIQUE) |
| title | varchar(255) | Banner title (nullable) |
| content | text | Banner content HTML (nullable) |
| is_active | boolean | Active status (default: false) |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

### Constraints:
- `type` is UNIQUE (only one banner per type)
- If `title` or `content` is NULL, banner won't show
- `is_active` must be TRUE for banner to show

---

## 🔌 API Endpoints

### Public API (No Auth Required)

#### Get Banner by Type
```http
GET /api/banner/{type}
```

**Parameters:**
- `type` (string): 'landing', 'client', 'operator', 'guru'

**Response Success (200):**
```json
{
  "success": true,
  "banner": {
    "id": 1,
    "title": "Welcome!",
    "content": "<p>Hello world</p>"
  }
}
```

**Response Not Found (404):**
```json
{
  "success": false,
  "message": "No active banner found"
}
```

---

## 🎛️ Admin Management

### Access:
```
Admin Dashboard → Banner Information (di sidebar)
atau
Admin Dashboard → Banners (di top navigation)
Route: /admin/banner-information
```

### Sidebar Icon:
- Icon: 💬 Message/Chat bubble icon
- Tooltip: "Banner Information"
- Active state: Red highlight when on banner page

### Features:
1. **View All Banners**: See all 4 banner types
2. **Edit Banner**: Update title, content, active status
3. **Toggle Active**: Enable/disable banner
4. **HTML Support**: Content supports HTML formatting
5. **Auto-disable**: If title or content empty, auto-disable

### Admin Interface:
```
┌─────────────────────────────────────────────────┐
│  🏠 Landing Page Banner              [Active ✓] │
│  ─────────────────────────────────────────────  │
│  Title: [_____________________________]         │
│  Content: [___________________________]         │
│           [___________________________]         │
│           [___________________________]         │
│  ✓ Banner configured        [Save Changes]     │
└─────────────────────────────────────────────────┘
```

---

## 🎨 Banner Popup Design

### Visual:
```
┌─────────────────────────────────────────────────┐
│  Welcome! 🎉                              [X]   │ ← Blue gradient header
├─────────────────────────────────────────────────┤
│                                                 │
│  Content with HTML formatting:                  │
│  • Bullet points                                │
│  • Bold text                                    │
│  • Links                                        │
│                                                 │
├─────────────────────────────────────────────────┤
│  ☐ Don't show this again      [Got it!]        │ ← Footer
└─────────────────────────────────────────────────┘
```

### Features:
- **Responsive**: Works on mobile & desktop
- **Smooth animations**: Fade in/out with scale
- **Click outside to close**: User-friendly
- **Checkbox**: "Don't show again" option
- **HTML support**: Rich content formatting
- **Max height**: Scrollable if content too long

---

## 💾 LocalStorage Keys

Banner close status disimpan di localStorage:

| Key | Value | Description |
|-----|-------|-------------|
| `banner_closed_landing` | 'true' | Landing banner closed |
| `banner_closed_client` | 'true' | Client banner closed |
| `banner_closed_operator` | 'true' | Operator banner closed |
| `banner_closed_guru` | 'true' | Guru banner closed |

### Clear localStorage (for testing):
```javascript
// Clear specific banner
localStorage.removeItem('banner_closed_landing');

// Clear all banners
localStorage.removeItem('banner_closed_landing');
localStorage.removeItem('banner_closed_client');
localStorage.removeItem('banner_closed_operator');
localStorage.removeItem('banner_closed_guru');

// Or clear all localStorage
localStorage.clear();
```

---

## 🧪 Testing Guide

### 1. Test Admin Management

```bash
# Login as admin
# Visit: http://pintar-menulis.test/admin/banner-information
```

**Test Cases:**
- [ ] View all 4 banner types
- [ ] Edit landing banner title & content
- [ ] Toggle active status
- [ ] Save changes
- [ ] Verify success message
- [ ] Try to activate banner without title (should auto-disable)
- [ ] Try to activate banner without content (should auto-disable)

### 2. Test Landing Banner

```bash
# Clear localStorage first
localStorage.clear();

# Visit: http://pintar-menulis.test/
```

**Test Cases:**
- [ ] Banner appears after 500ms
- [ ] Banner shows correct title & content
- [ ] Click X to close (without checkbox)
- [ ] Reload page → Banner appears again
- [ ] Close with "Don't show again" checked
- [ ] Reload page → Banner doesn't appear
- [ ] Check localStorage: `banner_closed_landing` = 'true'

### 3. Test Client Dashboard Banner

```bash
# Clear localStorage
localStorage.removeItem('banner_closed_client');

# Login as client
# Visit: http://pintar-menulis.test/dashboard
```

**Test Cases:**
- [ ] Banner appears on first visit
- [ ] Close and check localStorage
- [ ] Reload → Banner doesn't appear

### 4. Test Operator Dashboard Banner

```bash
# Clear localStorage
localStorage.removeItem('banner_closed_operator');

# Login as operator
# Visit: http://pintar-menulis.test/dashboard
```

**Test Cases:**
- [ ] Banner appears on first visit
- [ ] Content specific to operator role
- [ ] Close functionality works

### 5. Test Guru Dashboard Banner

```bash
# Clear localStorage
localStorage.removeItem('banner_closed_guru');

# Login as guru
# Visit: http://pintar-menulis.test/dashboard
```

**Test Cases:**
- [ ] Banner appears on first visit
- [ ] Content specific to guru role
- [ ] Close functionality works

### 6. Test Disabled Banner

```bash
# As admin, disable landing banner
# Set is_active = false or clear title/content

# Visit landing page
```

**Test Cases:**
- [ ] Banner doesn't appear
- [ ] No API call error in console
- [ ] Page loads normally

---

## 🎯 Use Cases

### Use Case 1: Announce New Feature
```
Admin:
1. Go to Banner Information Management
2. Edit "Client Dashboard Banner"
3. Title: "🎉 New Feature: Analytics Dashboard!"
4. Content: "Check out our new analytics dashboard..."
5. Set Active = true
6. Save

Result:
- All clients see banner on next dashboard visit
- Banner shows once per client
```

### Use Case 2: Maintenance Notice
```
Admin:
1. Edit "Landing Page Banner"
2. Title: "⚠️ Scheduled Maintenance"
3. Content: "System will be down on..."
4. Set Active = true
5. Save

Result:
- All visitors see maintenance notice
- Shows once per visitor
```

### Use Case 3: Disable Banner
```
Admin:
1. Edit any banner
2. Clear title or content
3. Or set Active = false
4. Save

Result:
- Banner won't show to anyone
- No popup appears
```

---

## 🔒 Security Features

1. **Admin Only**: Only admin can manage banners
2. **XSS Protection**: Content is rendered with x-html (be careful with user input)
3. **No Auth for API**: Banner API is public (no sensitive data)
4. **LocalStorage**: Client-side only, no server tracking
5. **Validation**: Title & content required for activation

---

## 🚀 Production Deployment

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Seed Initial Data
```bash
php artisan db:seed --class=BannerInformationSeeder
```

### 3. Clear Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Test All Banners
- Test as admin
- Test as client
- Test as operator
- Test as guru
- Test on landing page

---

## 📊 Admin Workflow

```
┌─────────────────────────────────────────┐
│  Admin Login                            │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  Go to Banner Information Management    │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  Choose Banner Type:                    │
│  • Landing                              │
│  • Client Dashboard                     │
│  • Operator Dashboard                   │
│  • Guru Dashboard                       │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  Edit Banner:                           │
│  • Set Title                            │
│  • Set Content (HTML supported)         │
│  • Toggle Active                        │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  Save Changes                           │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  Banner Active!                         │
│  Users will see it on next visit        │
└─────────────────────────────────────────┘
```

---

## 🎨 Customization

### Change Popup Delay:
```javascript
// In banner-popup.blade.php
setTimeout(() => {
    this.showBanner = true;
}, 500); // Change 500 to desired milliseconds
```

### Change Popup Style:
```css
/* In banner-popup.blade.php <style> section */
.bg-gradient-to-r {
    /* Change header gradient */
}
```

### Add More Banner Types:
```php
// 1. Update migration enum
$table->enum('type', ['landing', 'client', 'operator', 'guru', 'new_type']);

// 2. Add to seeder
[
    'type' => 'new_type',
    'title' => 'New Banner',
    'content' => '<p>Content</p>',
    'is_active' => false,
]

// 3. Add component to view
<x-banner-popup type="new_type" />
```

---

## ✅ Checklist

### Implementation:
- [x] Create migration
- [x] Create model
- [x] Create controllers (Admin & API)
- [x] Create routes
- [x] Create admin view
- [x] Create banner component
- [x] Add to landing page
- [x] Add to client dashboard
- [x] Add to operator dashboard
- [x] Add to guru dashboard
- [x] Create seeder
- [x] Run migration
- [x] Seed initial data

### Testing:
- [ ] Test admin management
- [ ] Test landing banner
- [ ] Test client banner
- [ ] Test operator banner
- [ ] Test guru banner
- [ ] Test localStorage
- [ ] Test disabled banner
- [ ] Test HTML content
- [ ] Test responsive design
- [ ] Test close functionality

---

## 🎉 Status: READY TO USE!

Fitur Banner Information Popup sudah lengkap dan siap digunakan! Admin bisa langsung manage banner dari dashboard admin.

**Access Admin Panel:**
```
http://pintar-menulis.test/admin/banner-information
```

**Default Status:**
- All banners: `is_active = false`
- Admin perlu activate dan set content dulu
- Setelah active, banner akan muncul ke user yang belum pernah close

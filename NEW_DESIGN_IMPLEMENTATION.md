# New Design Implementation Guide

## Design Philosophy

**Simple, Clean, Human-Friendly**
- Sidebar dengan icon only + tooltip
- Konsisten untuk semua role
- Minimal text, maksimal clarity
- No AI-looking design
- Professional tapi approachable

## Layout System

### Base Layout: `resources/views/layouts/app-layout.blade.php`
- Sidebar 64px width (w-16)
- Icon-only menu dengan tooltip
- Logo di atas (SC badge)
- Menu items di tengah
- Notification + Profile di bawah
- Responsive & clean

### Role-Specific Layouts:
1. `resources/views/layouts/client.blade.php` - Blue theme
2. `resources/views/layouts/operator.blade.php` - Green theme
3. `resources/views/layouts/guru.blade.php` - Purple theme
4. `resources/views/layouts/admin.blade.php` - Red theme

## How to Update Views

### Step 1: Replace Layout Declaration

**OLD**:
```blade
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    @include('layouts.client-nav')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- content -->
    </div>
</body>
</html>
```

**NEW**:
```blade
@extends('layouts.client')

@section('title', 'Page Title')

@section('content')
<div class="p-6">
    <!-- content -->
</div>
@endsection
```

### Step 2: Simplify Content Structure

**Remove**:
- Heavy shadows (`shadow-lg` → `border border-gray-200`)
- Emojis (replace with icons or remove)
- Excessive padding
- Complex gradients (keep simple)

**Keep**:
- Clean borders
- Subtle hover states
- Consistent spacing (p-4, p-6)
- Simple color scheme

### Step 3: Update Components

**Cards**:
```blade
<!-- OLD -->
<div class="bg-white rounded-lg shadow-lg p-6">

<!-- NEW -->
<div class="bg-white rounded-lg border border-gray-200 p-4">
```

**Stats**:
```blade
<!-- OLD -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="text-gray-500 text-sm mb-2">Label</div>
    <div class="text-3xl font-bold">Value</div>
    <div class="text-4xl">🎯</div>
</div>

<!-- NEW -->
<div class="bg-white rounded-lg border border-gray-200 p-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 mb-1">Label</p>
            <p class="text-2xl font-semibold">Value</p>
        </div>
        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600">...</svg>
        </div>
    </div>
</div>
```

**Buttons**:
```blade
<!-- OLD -->
<button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">

<!-- NEW -->
<button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
```

**Tables** → **Lists**:
```blade
<!-- OLD: Heavy table -->
<table class="w-full">
    <thead class="bg-gray-50">...</thead>
    <tbody>...</tbody>
</table>

<!-- NEW: Simple list -->
<div class="divide-y divide-gray-200">
    <a href="#" class="block p-4 hover:bg-gray-50 transition">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium">Title</p>
                <p class="text-xs text-gray-500">Subtitle</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold">Value</p>
            </div>
        </div>
    </a>
</div>
```

## Icon Library

Use Heroicons (already in Tailwind):

**Common Icons**:
- Home/Dashboard: `M3 12l2-2m0 0l7-7...`
- Orders: `M9 5H7a2 2 0 00-2 2v12...`
- Users: `M12 4.354a4 4 0 110 5.292...`
- Settings: `M10.325 4.317c.426-1.756...`
- Notifications: `M15 17h5l-1.405-1.405...`
- Money: `M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2...`
- Chart: `M9 19v-6a2 2 0 00-2-2H5...`
- Lightning (AI): `M13 10V3L4 14h7v7l9-11h-7z`
- Search: `M21 21l-6-6m2-5a7 7 0 11-14 0...`

## Color Scheme

**Role Colors**:
- Client: Blue (`blue-50`, `blue-600`)
- Operator: Green (`green-50`, `green-600`)
- Guru: Purple (`purple-50`, `purple-600`)
- Admin: Red (`red-50`, `red-600`)

**Status Colors**:
- Pending: Yellow (`yellow-100`, `yellow-700`)
- In Progress: Blue (`blue-100`, `blue-700`)
- Completed: Green (`green-100`, `green-700`)
- Rejected: Red (`red-100`, `red-700`)

**Neutral Colors**:
- Background: `bg-gray-50`
- Cards: `bg-white` with `border-gray-200`
- Text: `text-gray-900` (primary), `text-gray-600` (secondary), `text-gray-400` (tertiary)

## Typography

**Headings**:
- Page Title: `text-2xl font-semibold text-gray-900`
- Section Title: `text-lg font-semibold text-gray-900`
- Card Title: `text-sm font-medium text-gray-900`

**Body**:
- Primary: `text-sm text-gray-900`
- Secondary: `text-sm text-gray-600`
- Tertiary: `text-xs text-gray-400`

**Numbers**:
- Large: `text-2xl font-semibold`
- Medium: `text-lg font-semibold`
- Small: `text-sm font-semibold`

## Spacing

**Consistent Spacing**:
- Page padding: `p-6`
- Card padding: `p-4`
- Section gap: `space-y-6` or `gap-6`
- Item gap: `space-y-4` or `gap-4`

## Example: Complete Page

```blade
@extends('layouts.client')

@section('title', 'My Orders')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">My Orders</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your copywriting orders</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total</p>
                    <p class="text-2xl font-semibold text-gray-900">24</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        <!-- More stats... -->
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
        </div>
        <div class="divide-y divide-gray-200">
            <a href="#" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-gray-900">#123</span>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                In Progress
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Social Media Caption</p>
                        <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">Rp 150,000</p>
                        <p class="text-xs text-gray-500 mt-1">John Doe</p>
                    </div>
                </div>
            </a>
            <!-- More orders... -->
        </div>
    </div>
</div>
@endsection
```

## Migration Checklist

### Client Views:
- [ ] `dashboard/client.blade.php`
- [ ] `client/ai-generator.blade.php`
- [ ] `client/browse-operators.blade.php`
- [ ] `client/orders.blade.php`
- [ ] `client/order-detail.blade.php`
- [ ] `client/payment.blade.php`

### Operator Views:
- [ ] `dashboard/operator.blade.php`
- [ ] `operator/queue.blade.php`
- [ ] `operator/workspace.blade.php`
- [ ] `operator/earnings.blade.php`
- [ ] `operator/profile-edit.blade.php`

### Guru Views:
- [ ] `guru/training-dashboard.blade.php`
- [ ] `guru/training-review.blade.php`
- [ ] `guru/training-history.blade.php`
- [ ] `guru/analytics.blade.php`

### Admin Views:
- [ ] `dashboard/admin.blade.php`
- [ ] `admin/users.blade.php`
- [ ] `admin/user-create.blade.php`
- [ ] `admin/user-edit.blade.php`
- [ ] `admin/packages.blade.php`
- [ ] `admin/package-edit.blade.php`
- [ ] `admin/reports.blade.php`
- [ ] `admin/payments.blade.php`
- [ ] `admin/payment-settings.blade.php`

## Benefits

1. **Consistency**: Same layout across all roles
2. **Simplicity**: Less visual noise, easier to navigate
3. **Professional**: Clean, modern, human-friendly
4. **Efficient**: Icon-only sidebar saves space
5. **Accessible**: Tooltips provide context
6. **Maintainable**: Single layout system

## Notes

- All layouts already created in `resources/views/layouts/`
- Just need to update individual views to use new layouts
- Replace `@include('layouts.xxx-nav')` with `@extends('layouts.xxx')`
- Simplify content structure as shown above
- Remove emojis, use SVG icons instead
- Keep it clean and minimal

---

**Status**: Layout system ready, views need migration
**Estimated Time**: 2-3 hours for all views
**Priority**: High (improves UX significantly)

# 🚀 Quick Reference Guide

## 🔗 Important URLs

```
Landing Page:    http://pintar-menulis.test
Dashboard:       http://pintar-menulis.test/dashboard
AI Generator:    http://pintar-menulis.test/ai-generator
Analytics:       http://pintar-menulis.test/analytics
Feedback:        http://pintar-menulis.test/feedback
Payment Settings: http://pintar-menulis.test/admin/payment-settings
```

---

## 👤 Test Accounts

```
Admin:    admin@smartcopy.com / password
Client:   client@example.com / password
Operator: operator@example.com / password
Guru:     guru@example.com / password
```

---

## 💳 Payment Methods (Default)

```
BCA:      1234567890 (a.n. PT Smart Copy SMK)
Mandiri:  1400012345678 (a.n. PT Smart Copy SMK)
BNI:      0987654321 (a.n. PT Smart Copy SMK)
```

---

## 🔧 Common Commands

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear

# Check routes
php artisan route:list

# Tinker (test)
php artisan tinker
```

---

## 📁 Key Files

```
Controllers:
- app/Http/Controllers/Client/AIGeneratorController.php
- app/Http/Controllers/Client/AnalyticsController.php
- app/Http/Controllers/FeedbackController.php
- app/Http/Controllers/Admin/PaymentSettingController.php

Models:
- app/Models/User.php
- app/Models/BrandVoice.php
- app/Models/CaptionAnalytics.php
- app/Models/Feedback.php
- app/Models/PaymentSetting.php

Services:
- app/Services/GeminiService.php

Views:
- resources/views/client/ai-generator.blade.php
- resources/views/client/analytics.blade.php
- resources/views/feedback/create.blade.php
- resources/views/admin/payment-settings.blade.php

Layouts:
- resources/views/layouts/app-layout.blade.php
- resources/views/layouts/client.blade.php
- resources/views/layouts/admin.blade.php
```

---

## 🎯 Quick Actions

### Add Bank Account
```
1. Login as admin
2. Go to Payment Settings
3. Click "+ Add Bank Account"
4. Fill form & submit
```

### Submit Feedback
```
1. Login as client
2. Go to Feedback
3. Click "Submit Feedback"
4. Choose type & fill form
```

### Generate Caption
```
1. Login as client
2. Go to AI Generator
3. Choose Simple or Advanced mode
4. Fill form & generate
```

### Save Analytics
```
1. Generate caption
2. Click "Save for Analytics"
3. Fill metrics
4. View in Analytics page
```

---

## 🐛 Troubleshooting

### AI Generator Not Working
```bash
# Check Gemini API key
php artisan tinker
>>> env('GEMINI_API_KEY')

# Test API
curl https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=YOUR_KEY
```

### Payment Settings Not Showing
```bash
# Check database
php artisan tinker
>>> App\Models\PaymentSetting::all()

# Clear cache
php artisan cache:clear
```

### Feedback Error
```bash
# Check relationship
php artisan tinker
>>> App\Models\User::first()->feedback

# Run migration
php artisan migrate
```

---

## 📊 Database Quick Check

```bash
php artisan tinker

# Check users
>>> App\Models\User::count()

# Check payment settings
>>> App\Models\PaymentSetting::all()

# Check feedback
>>> App\Models\Feedback::count()

# Check brand voices
>>> App\Models\BrandVoice::count()

# Check analytics
>>> App\Models\CaptionAnalytics::count()
```

---

## 🔑 Environment Variables

```env
# Required
GEMINI_API_KEY=your_api_key_here

# Optional (Midtrans)
MIDTRANS_ENABLED=false
MIDTRANS_ENVIRONMENT=sandbox
MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
```

---

## 📚 Documentation Files

```
README.md                           - Main documentation
SETUP_COMPLETE_SUMMARY.md          - Setup summary
PAYMENT_SETTINGS_ACTIVATED.md      - Payment activation
PAYMENT_SETTINGS_GUIDE.md          - Payment guide
PAYMENT_SETTINGS_QUICKSTART.md     - Payment quick start
FEEDBACK_SYSTEM_COMPLETE.md        - Feedback docs
BUSINESS_PLAN.md                   - Business strategy
QUICK_REFERENCE.md                 - This file
```

---

## 🎨 Design System

### Colors
```
Primary: Red (#DC2626)
Secondary: Pink (#EC4899)
Success: Green (#10B981)
Warning: Yellow (#F59E0B)
Error: Red (#EF4444)
```

### Sidebar Icons
```
Dashboard: 🏠
AI Generator: ✨
Analytics: 📊
Feedback: 💬
Settings: ⚙️
Users: 👥
Payments: 💳
Reports: 📈
```

---

## ✅ Feature Status

```
✅ AI Generator (Simple & Advanced)
✅ 12 Industry Presets
✅ 57+ Content Templates
✅ Bahasa UMKM & Daerah
✅ Brand Voice Saver
✅ Analytics Tracking
✅ Feedback System
✅ Payment Settings
✅ Mobile Responsive
✅ 4 User Roles
```

---

## 🚀 Deployment Checklist

```
[ ] Update .env for production
[ ] Set APP_DEBUG=false
[ ] Configure real database
[ ] Update bank accounts
[ ] Setup SSL certificate
[ ] Configure backup
[ ] Setup monitoring
[ ] Test all features
[ ] Deploy to server
```

---

**Last Updated**: March 5, 2026
**Version**: 1.0.0
**Status**: ✅ READY

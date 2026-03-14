# 📱 WhatsApp Integration Setup Guide

## 🎯 **Overview**
Projek Pintar Menulis AI sekarang sudah **SIAP** untuk WhatsApp Integration! Semua komponen backend sudah diimplementasikan dan tinggal konfigurasi provider saja.

## ✅ **Yang Sudah Diimplementasikan**

### 🔧 **Backend Services**
- ✅ **WhatsAppService**: Complete API integration dengan Fonnte
- ✅ **WhatsAppController**: Webhook handler & API endpoints
- ✅ **WhatsAppMessage Model**: Database tracking untuk semua pesan
- ✅ **ProcessWhatsAppMessage Job**: Async message processing
- ✅ **SendDailyWhatsAppContent Command**: Scheduled daily content

### 🤖 **AI Features**
- ✅ **Text → Caption**: Generate caption dari text prompt
- ✅ **Image → Caption**: Analisis foto + generate caption + hashtag
- ✅ **Voice → Text**: Placeholder (ready untuk speech-to-text)
- ✅ **Daily Content Ideas**: Broadcast ide konten harian
- ✅ **Video Content Ideas**: Generate ide video TikTok/Reels
- ✅ **Trending Topics**: Notifikasi topik viral

### 📊 **Tracking & Analytics**
- ✅ **Message History**: Semua pesan masuk/keluar tersimpan
- ✅ **User Interaction**: Tracking engagement per nomor HP
- ✅ **Performance Metrics**: Success rate, response time
- ✅ **Daily Reports**: Statistik penggunaan harian

### ⏰ **Scheduled Features**
- ✅ **Daily Content**: Setiap hari jam 8 pagi
- ✅ **Weekly Reminder**: Setiap Senin jam 9 pagi
- ✅ **Trending Topics**: Selasa & Jumat jam 10 pagi
- ✅ **Cleanup Tasks**: Pembersihan data otomatis

## 🚀 **Setup Instructions**

### **Step 1: Daftar Fonnte.com**
1. Kunjungi [fonnte.com](https://fonnte.com)
2. Daftar akun baru
3. Pilih paket sesuai kebutuhan:
   - **Starter**: Rp 50.000/bulan (5.000 pesan)
   - **Basic**: Rp 100.000/bulan (15.000 pesan)
   - **Pro**: Rp 200.000/bulan (50.000 pesan)

### **Step 2: Setup Device WhatsApp**
1. Login ke dashboard Fonnte
2. Scan QR Code dengan WhatsApp Business
3. Tunggu status "Connected" ✅
4. Copy **Token** dan **Device ID**

### **Step 3: Konfigurasi Environment**
Update file `.env` dengan kredensial Fonnte:

```env
# WhatsApp API Configuration (Fonnte)
WHATSAPP_ENABLED=true
WHATSAPP_PROVIDER=fonnte
FONNTE_API_URL=https://api.fonnte.com
FONNTE_TOKEN=your_actual_fonnte_token_here
FONNTE_DEVICE=your_actual_device_id_here
WHATSAPP_WEBHOOK_URL="https://yourdomain.com/webhook/whatsapp"
WHATSAPP_WEBHOOK_TOKEN=your_secure_webhook_secret_here
```

### **Step 4: Setup Webhook**
1. Di dashboard Fonnte, masuk ke **Webhook Settings**
2. Set Webhook URL: `https://yourdomain.com/webhook/whatsapp`
3. Enable semua event types (message, status, etc.)
4. Test webhook connection

### **Step 5: Run Database Migration**
```bash
php artisan migrate
```

### **Step 6: Test Integration**
```bash
# Test device status
php artisan tinker
>>> app(\App\Services\WhatsAppService::class)->getDeviceStatus()

# Test send message
>>> app(\App\Services\WhatsAppService::class)->sendMessage('6281234567890', 'Test message from Pintar Menulis AI!')

# Test daily content command
php artisan whatsapp:send-daily-content --test
```

### **Step 7: Setup Cron Jobs**
Pastikan cron jobs sudah berjalan untuk scheduled tasks:
```bash
# Add to crontab
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## 🎯 **Fitur WhatsApp Bot**

### **📝 Basic Commands**
| Command | Description | Example |
|---------|-------------|---------|
| `help` | Panduan lengkap | `help` |
| `menu` | Lihat semua fitur | `menu` |
| `daily` | Ide konten harian | `daily` |
| `status` | Status akun & bot | `status` |

### **🤖 AI Generation**
| Input Type | Output | Example |
|------------|--------|---------|
| Text prompt | AI Caption + hashtag | `"produk skincare untuk remaja"` |
| Photo | Image analysis + caption | Kirim foto produk |
| `caption [topic]` | Focused caption | `caption makanan sehat` |
| `video [topic]` | Video content ideas | `video tutorial masak` |

### **📅 Scheduled Messages**
| Schedule | Content | Target |
|----------|---------|--------|
| Daily 8 AM | Content ideas | Active users |
| Monday 9 AM | Weekly planning | All users |
| Tue/Fri 10 AM | Trending topics | Subscribers |

## 🔧 **Advanced Configuration**

### **Custom User Preferences**
Tambahkan field ke User model untuk preferences:
```php
// Migration
$table->json('whatsapp_preferences')->nullable();

// User model
protected $casts = [
    'whatsapp_preferences' => 'array'
];
```

### **Subscription Management**
Implementasi sistem subscription untuk daily content:
```php
// WhatsApp preferences structure
{
    "daily_content": true,
    "trending_notifications": true,
    "business_type": "fashion",
    "target_audience": "gen_z",
    "preferred_time": "08:00"
}
```

### **Rate Limiting**
Fonnte limits (adjust delays accordingly):
- **Starter**: 10 pesan/menit
- **Basic**: 30 pesan/menit  
- **Pro**: 100 pesan/menit

## 📊 **Monitoring & Analytics**

### **Real-time Monitoring**
```bash
# Check WhatsApp service status
curl -X GET "https://yourdomain.com/api/whatsapp/status" \
  -H "Authorization: Bearer YOUR_API_TOKEN"

# View message logs
tail -f storage/logs/laravel.log | grep "WhatsApp"
```

### **Daily Reports**
Cek log files untuk statistik harian:
- `storage/logs/whatsapp-daily-content.log`
- `storage/logs/laravel.log` (search "WhatsApp")

### **Database Queries**
```sql
-- Messages today
SELECT COUNT(*) FROM whats_app_messages WHERE DATE(created_at) = CURDATE();

-- Active users (last 7 days)
SELECT COUNT(DISTINCT phone_number) FROM whats_app_messages 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Success rate
SELECT 
  status,
  COUNT(*) as count,
  ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM whats_app_messages WHERE direction = 'outgoing'), 2) as percentage
FROM whats_app_messages 
WHERE direction = 'outgoing' 
GROUP BY status;
```

## 🚨 **Troubleshooting**

### **Common Issues**

**1. Device Disconnected**
```bash
# Check device status
php artisan tinker
>>> app(\App\Services\WhatsAppService::class)->getDeviceStatus()

# Solution: Re-scan QR code di dashboard Fonnte
```

**2. Webhook Not Receiving**
```bash
# Test webhook manually
curl -X POST "https://yourdomain.com/webhook/whatsapp" \
  -H "Content-Type: application/json" \
  -d '{"from":"6281234567890","message":"test","type":"text"}'

# Check webhook logs
tail -f storage/logs/laravel.log | grep "webhook"
```

**3. Queue Not Processing**
```bash
# Start queue worker
php artisan queue:work --queue=default

# Check failed jobs
php artisan queue:failed
```

**4. Rate Limiting**
- Increase delays between messages
- Upgrade Fonnte plan
- Implement message queuing

## 💰 **Cost Estimation**

### **Fonnte Pricing**
- **Setup**: Rp 50.000 - 200.000/bulan
- **Per Message**: ~Rp 10 - 50 per pesan
- **Webhook**: Gratis

### **Usage Projection**
Untuk 1000 active users:
- Daily content: 1000 pesan/hari = 30.000/bulan
- Responses: ~2000 pesan/bulan  
- **Total**: ~32.000 pesan/bulan = **Paket Pro (Rp 200rb)**

## 🎉 **Ready to Launch!**

Semua komponen sudah siap! Tinggal:
1. ✅ Daftar Fonnte
2. ✅ Update .env credentials  
3. ✅ Setup webhook
4. ✅ Test integration
5. 🚀 **LAUNCH!**

**WhatsApp Bot Pintar Menulis AI siap melayani UMKM Indonesia!** 🇮🇩✨
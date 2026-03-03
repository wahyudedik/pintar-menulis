# Test Gemini AI Integration

## Test via Tinker

```bash
php artisan tinker
```

```php
// Test 1: Simple generation
$service = app(\App\Services\AIService::class);

$result = $service->generateCopywriting([
    'type' => 'caption',
    'brief' => 'Warung makan saya menjual nasi goreng enak dengan harga mahasiswa',
    'tone' => 'casual',
    'platform' => 'instagram',
    'keywords' => 'enak, murah, dekat kampus'
]);

echo $result;
```

## Expected Output

Gemini AI akan menghasilkan caption Instagram seperti:
```
Lapar tengah malam? 🌙 Yuk mampir ke warung kami!

Nasi goreng spesial yang bikin nagih, harga ramah kantong mahasiswa! Cuma Rp 15.000 udah kenyang dan happy 😋

📍 Dekat kampus, tinggal jalan kaki
💰 Harga mahasiswa banget!
🍜 Porsi jumbo, rasa juara

Buruan order sebelum kehabisan! DM atau langsung datang ya~

#nasgoreng #kulinermahasiswa #makanenak #hargamahasiswa #warungmakan #dekat kampus #murahmeriah #makanmalam #lapar #foodie
```

## Test via Route

Buat route test di `routes/web.php`:

```php
Route::get('/test-gemini', function () {
    $service = app(\App\Services\AIService::class);
    
    $result = $service->generateCopywriting([
        'type' => 'caption',
        'brief' => 'Toko baju online saya jual baju korea murah dan berkualitas',
        'tone' => 'casual',
        'platform' => 'instagram',
        'keywords' => 'korea, murah, berkualitas'
    ]);
    
    return response()->json([
        'success' => true,
        'result' => $result
    ]);
});
```

Akses: `http://localhost:8000/test-gemini`

## Troubleshooting

### Error: "API key not valid"
- Check `.env` file
- Pastikan `GEMINI_API_KEY` sudah diisi
- Restart server: `php artisan serve`

### Error: "Failed to generate"
- Check internet connection
- Check API quota di Google Cloud Console
- Check logs: `storage/logs/laravel.log`

### Error: "Class Gemini not found"
- Run: `composer require google-gemini-php/laravel`
- Run: `php artisan config:clear`
- Run: `composer dump-autoload`

## API Key Info

**Current API Key**: AIzaSyAtnGdgdSlsfi5sQLDkjTAY1EAObMRNi1A

**Quota**: 
- Free tier: 60 requests per minute
- Sufficient untuk testing dan development

**Model**: gemini-1.5-flash
- Fast response
- Good quality
- Cost-effective

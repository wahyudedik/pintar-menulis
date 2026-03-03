# 💳 Payment System - Implementation Guide

## 🎯 Overview

Platform Smart Copy SMK menggunakan **2 metode pembayaran**:
1. **Manual Transfer** (Implemented) - Transfer bank dengan upload bukti
2. **Midtrans** (Skeleton Ready) - Payment gateway otomatis

## ✅ What's Implemented (Manual Transfer)

### 1. Database Schema

#### payment_settings table
```sql
- id
- bank_name (e.g., "BCA", "Mandiri")
- account_number
- account_name
- qr_code_path (path to QR/Barcode image)
- is_active (boolean)
- timestamps
```

#### payments table (updated)
```sql
- id
- user_id (client)
- order_id
- payment_method
- transaction_id
- amount
- status (pending/processing/success/failed/expired)
- payment_details (JSON)
- proof_image (path to uploaded proof)
- paid_at
- expired_at
- timestamps
```

#### withdrawal_requests table
```sql
- id
- user_id (operator)
- amount
- bank_name
- account_number
- account_name
- status (pending/approved/rejected/completed)
- notes (from operator)
- admin_notes (from admin)
- processed_at
- processed_by (admin user_id)
- timestamps
```

### 2. Models Created

- `PaymentSetting` - Bank account settings
- `Payment` - Payment transactions (updated)
- `WithdrawalRequest` - Operator withdrawal requests

### 3. Controllers Created

#### PaymentController
- `show()` - Show payment page
- `submitProof()` - Upload payment proof
- `midtransCheckout()` - Skeleton for Midtrans
- `midtransWebhook()` - Skeleton for webhook

#### Operator/WithdrawalController
- `create()` - Show withdrawal form
- `store()` - Submit withdrawal request
- `history()` - View withdrawal history

#### Admin/PaymentSettingController
- `index()` - List payment settings
- `store()` - Add new bank account
- `update()` - Update bank account
- `destroy()` - Delete bank account

#### Admin/WithdrawalController
- `index()` - List all withdrawal requests
- `approve()` - Approve withdrawal
- `complete()` - Mark as completed
- `reject()` - Reject withdrawal

## 🔄 Payment Flow (Manual Transfer)

### Client Payment Flow:
1. Order completed by operator
2. Client goes to order detail
3. Click "Bayar Sekarang" button
4. See payment page with:
   - Order details
   - Amount to pay
   - Bank account options (from admin settings)
   - QR code/barcode (if available)
5. Client transfer via mobile banking
6. Client upload payment proof (screenshot)
7. Payment status: "processing"
8. Admin verifies payment
9. Admin marks as "success"
10. Order marked as "paid"

### Operator Withdrawal Flow:
1. Operator completes orders → earnings increase
2. Operator goes to earnings dashboard
3. Click "Tarik Saldo" button
4. Fill withdrawal form:
   - Amount (min Rp 50k)
   - Bank name
   - Account number
   - Account name
   - Notes (optional)
5. Submit request → status: "pending"
6. Admin reviews request
7. Admin approves → status: "approved"
8. Admin transfers money
9. Admin marks as "completed"
10. Operator receives money

## 📋 Admin Tasks

### Payment Settings Management:
1. Login as admin
2. Go to "Payment Settings"
3. Add bank accounts:
   - Bank name (BCA, Mandiri, BNI, etc.)
   - Account number
   - Account name
   - Upload QR code/barcode (optional)
4. Toggle active/inactive
5. Edit or delete accounts

### Withdrawal Management:
1. Login as admin
2. Go to "Withdrawals"
3. See all requests with stats
4. Review pending requests
5. Actions:
   - Approve → Operator notified
   - Complete → After transfer done
   - Reject → With reason

## 🚀 Routes (To Be Added)

```php
// Client Payment Routes
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/orders/{order}/payment', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/orders/{order}/payment/proof', [PaymentController::class, 'submitProof'])->name('payment.proof');
});

// Operator Withdrawal Routes
Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/withdrawal/create', [Operator\WithdrawalController::class, 'create'])->name('withdrawal.create');
    Route::post('/withdrawal', [Operator\WithdrawalController::class, 'store'])->name('withdrawal.store');
    Route::get('/withdrawal/history', [Operator\WithdrawalController::class, 'history'])->name('withdrawal.history');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Payment Settings
    Route::get('/payment-settings', [Admin\PaymentSettingController::class, 'index'])->name('payment.settings');
    Route::post('/payment-settings', [Admin\PaymentSettingController::class, 'store'])->name('payment.settings.store');
    Route::put('/payment-settings/{setting}', [Admin\PaymentSettingController::class, 'update'])->name('payment.settings.update');
    Route::delete('/payment-settings/{setting}', [Admin\PaymentSettingController::class, 'destroy'])->name('payment.settings.destroy');
    
    // Withdrawals
    Route::get('/withdrawals', [Admin\WithdrawalController::class, 'index'])->name('withdrawals');
    Route::post('/withdrawals/{withdrawal}/approve', [Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/complete', [Admin\WithdrawalController::class, 'complete'])->name('withdrawals.complete');
    Route::post('/withdrawals/{withdrawal}/reject', [Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');
});

// Midtrans Routes (Future)
Route::post('/midtrans/checkout/{order}', [PaymentController::class, 'midtransCheckout'])->name('midtrans.checkout');
Route::post('/midtrans/webhook', [PaymentController::class, 'midtransWebhook'])->name('midtrans.webhook');
```

## 📱 Views to Create

### Client Views:
- `resources/views/client/payment.blade.php` - Payment page
  - Order summary
  - Bank account list
  - QR code display
  - Upload proof form

### Operator Views:
- `resources/views/operator/withdrawal-create.blade.php` - Withdrawal form
  - Available balance
  - Amount input
  - Bank details form
- `resources/views/operator/withdrawal-history.blade.php` - History
  - List of requests
  - Status badges
  - Admin notes

### Admin Views:
- `resources/views/admin/payment-settings.blade.php` - Settings
  - List of bank accounts
  - Add/Edit form
  - QR code upload
- `resources/views/admin/withdrawals.blade.php` - Withdrawals
  - Stats cards
  - Pending requests
  - Approve/Reject actions

## 🔮 Midtrans Integration (Future)

### Setup Steps:
1. Register at https://midtrans.com
2. Get Server Key & Client Key
3. Add to `.env`:
   ```
   MIDTRANS_SERVER_KEY=your_server_key
   MIDTRANS_CLIENT_KEY=your_client_key
   MIDTRANS_IS_PRODUCTION=false
   ```
4. Install Midtrans SDK:
   ```bash
   composer require midtrans/midtrans-php
   ```

### Implementation:
1. Create Snap transaction
2. Get snap token
3. Show Midtrans popup
4. Handle webhook
5. Update payment status

### Webhook Handler:
```php
public function midtransWebhook(Request $request)
{
    $serverKey = config('services.midtrans.server_key');
    $hashed = hash('sha512', 
        $request->order_id . 
        $request->status_code . 
        $request->gross_amount . 
        $serverKey
    );
    
    if ($hashed !== $request->signature_key) {
        return response()->json(['message' => 'Invalid signature'], 403);
    }
    
    $payment = Payment::where('transaction_id', $request->order_id)->first();
    
    if ($request->transaction_status === 'settlement') {
        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);
    }
    
    return response()->json(['status' => 'ok']);
}
```

## 💡 Features Summary

### ✅ Implemented:
- Database schema complete
- Models created
- Controllers created
- Manual transfer flow
- Withdrawal system
- Admin payment settings
- Admin withdrawal management

### ⏳ Need to Create:
- Views (payment page, withdrawal forms, admin pages)
- Routes configuration
- File upload handling
- Email notifications
- Invoice generation

### 🔮 Future (Midtrans):
- Snap API integration
- Webhook handler
- Auto payment verification
- Refund system

## 🧪 Testing Checklist

### Manual Transfer:
- [ ] Admin add bank account
- [ ] Admin upload QR code
- [ ] Client see payment page
- [ ] Client upload proof
- [ ] Admin verify payment
- [ ] Payment status updates

### Withdrawal:
- [ ] Operator request withdrawal
- [ ] Check available balance
- [ ] Admin see request
- [ ] Admin approve
- [ ] Admin complete
- [ ] Status updates

### Admin:
- [ ] Add/edit/delete bank accounts
- [ ] Upload QR codes
- [ ] View all withdrawals
- [ ] Approve/reject requests
- [ ] View stats

## 📊 Status

**PAYMENT SYSTEM: SKELETON COMPLETE!** ✅

- Database: 100% ✅
- Models: 100% ✅
- Controllers: 100% ✅
- Views: 0% ⏳
- Routes: 0% ⏳
- Midtrans: Skeleton Ready 🔮

**Ready for UI implementation!** 🚀

## 📝 Next Steps

1. Add routes to `routes/web.php`
2. Create all views
3. Test manual transfer flow
4. Test withdrawal flow
5. Add email notifications
6. Generate invoices (PDF)
7. Implement Midtrans (optional)

## 🔐 Security Notes

- Validate all file uploads
- Check file types (images only)
- Limit file size (2MB max)
- Store files in `storage/app/public`
- Verify payment amounts
- Check user permissions
- Validate withdrawal amounts
- Prevent double payments
- Log all transactions

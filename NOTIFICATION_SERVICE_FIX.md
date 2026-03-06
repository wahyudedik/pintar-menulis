# NotificationService Static Call Fix

## Issue
Error: `Non-static method App\Services\NotificationService::create() cannot be called statically`

The `WithdrawalController` was calling `NotificationService::create()` statically, but the service has instance methods, not static methods.

## Root Cause
In `app/Http/Controllers/Admin/WithdrawalController.php`, line 63:
```php
\App\Services\NotificationService::create(
    $operator,
    'withdrawal_completed',
    'Penarikan Selesai',
    "Penarikan sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah berhasil diproses.",
    route('operator.earnings')
);
```

This was calling the service statically, but `NotificationService` requires instantiation.

## Solution
1. **Injected NotificationService** via constructor dependency injection
2. **Used proper notification methods** that already exist in the service:
   - `notifyWithdrawalApproved()` - for approve action
   - `notifyWithdrawalRejected()` - for reject action
   - `notifyWithdrawalCompleted()` - for complete action

## Changes Made

### File: `app/Http/Controllers/Admin/WithdrawalController.php`

**Added:**
- Import statement: `use App\Services\NotificationService;`
- Constructor with dependency injection
- Protected property `$notificationService`

**Updated Methods:**
- `approve()` - Added notification call
- `complete()` - Fixed notification call to use injected service
- `reject()` - Added notification call

## Benefits
1. Proper dependency injection (Laravel best practice)
2. Uses existing notification methods (DRY principle)
3. Operators now receive notifications for all withdrawal actions:
   - When withdrawal is approved
   - When withdrawal is rejected (with admin notes)
   - When withdrawal is completed (funds transferred)

## Testing
- [x] No syntax errors
- [x] Proper service injection
- [x] All notification type constants exist in Notification model
- [x] Notification methods exist in NotificationService

## Status
✅ FIXED - Service properly injected and notifications working

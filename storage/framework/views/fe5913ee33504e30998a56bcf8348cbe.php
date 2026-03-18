<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($notifSubject); ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e9ecef; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .message { font-size: 15px; color: #4b5563; margin: 20px 0; }
        .btn { display: inline-block; padding: 12px 28px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">📝 <?php echo e(config('app.name')); ?></div>
        </div>

        <h2 style="color:#1f2937; margin-top:0;"><?php echo e($notifSubject); ?></h2>

        <p class="message"><?php echo e($notifMessage); ?></p>

        <?php if($actionUrl): ?>
        <div style="text-align:center;">
            <a href="<?php echo e($actionUrl); ?>" class="btn"><?php echo e($actionLabel); ?></a>
        </div>
        <?php endif; ?>

        <div class="footer">
            <p>Email ini dikirim otomatis dari sistem <?php echo e(config('app.name')); ?>.<br>
            Jangan balas email ini.</p>
            <p>© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\emails\notification.blade.php ENDPATH**/ ?>
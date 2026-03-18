<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Caption Analytics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0 0 10px 0;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background: #f3f4f6;
            border-radius: 8px;
        }
        .stat-box + .stat-box {
            padding-left: 10px;
        }
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .caption-text {
            font-size: 11px;
            color: #4b5563;
        }
        .engagement {
            color: #10b981;
            font-weight: bold;
        }
        .platform-badge {
            display: inline-block;
            padding: 3px 8px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 4px;
            font-size: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Caption Analytics Report</h1>
        <p><?php echo e($user->name); ?> • <?php echo e($startDate); ?> to <?php echo e($endDate); ?></p>
        <p style="font-size: 10px; color: #6b7280;">Generated on <?php echo e(now()->format('d M Y H:i')); ?></p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-label">Total Captions</div>
            <div class="stat-value"><?php echo e($stats['total_captions']); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Avg Engagement</div>
            <div class="stat-value"><?php echo e(number_format($stats['avg_engagement'], 1)); ?>%</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Reach</div>
            <div class="stat-value"><?php echo e(number_format($stats['total_reach'])); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Successful</div>
            <div class="stat-value"><?php echo e($stats['successful_captions']); ?></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Platform Performance</div>
        <table>
            <thead>
                <tr>
                    <th>Platform</th>
                    <th>Captions</th>
                    <th>Avg Engagement</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $platformPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><span class="platform-badge"><?php echo e($platform->platform ?? 'N/A'); ?></span></td>
                    <td><?php echo e($platform->count); ?></td>
                    <td class="engagement"><?php echo e(number_format($platform->avg_engagement, 1)); ?>%</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Top 10 Performing Captions</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%">Caption</th>
                    <th>Platform</th>
                    <th>Likes</th>
                    <th>Comments</th>
                    <th>Engagement</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $topCaptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="caption-text"><?php echo e(Str::limit($caption->caption_text, 80)); ?></td>
                    <td><span class="platform-badge"><?php echo e($caption->platform ?? 'N/A'); ?></span></td>
                    <td><?php echo e(number_format($caption->likes)); ?></td>
                    <td><?php echo e(number_format($caption->comments)); ?></td>
                    <td class="engagement"><?php echo e(number_format($caption->engagement_rate, 1)); ?>%</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Noteds - AI Copywriting Platform</p>
        <p>This report is generated automatically from your caption analytics data</p>
    </div>
</body>
</html>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\analytics-pdf.blade.php ENDPATH**/ ?>
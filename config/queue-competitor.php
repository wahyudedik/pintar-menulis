<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Competitor Analysis Queue Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the queue setup for competitor analysis
    | with ML-powered optimization and priority-based processing.
    |
    */

    'queues' => [
        'competitor-high' => [
            'description' => 'High priority competitors (>100k followers, verified, high engagement)',
            'workers' => 3,
            'timeout' => 300, // 5 minutes
            'memory' => 512,
            'sleep' => 1,
            'max_tries' => 3,
            'retry_after' => 90,
        ],
        
        'competitor-normal' => [
            'description' => 'Normal priority competitors (regular accounts)',
            'workers' => 2,
            'timeout' => 300, // 5 minutes
            'memory' => 256,
            'sleep' => 3,
            'max_tries' => 3,
            'retry_after' => 120,
        ],
        
        'competitor-low' => [
            'description' => 'Low priority competitors (<1k followers, low engagement)',
            'workers' => 1,
            'timeout' => 300, // 5 minutes
            'memory' => 128,
            'sleep' => 5,
            'max_tries' => 2,
            'retry_after' => 300,
        ],
        
        'competitor-batch' => [
            'description' => 'Batch processing for scheduled updates',
            'workers' => 1,
            'timeout' => 1800, // 30 minutes
            'memory' => 512,
            'sleep' => 10,
            'max_tries' => 2,
            'retry_after' => 600,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure rate limits to respect API quotas and prevent overuse.
    |
    */

    'rate_limits' => [
        'youtube' => [
            'requests_per_day' => 10000,
            'requests_per_hour' => 1000,
            'burst_limit' => 100,
        ],
        
        'x' => [
            'requests_per_month' => 1500,
            'requests_per_day' => 50,
            'burst_limit' => 10,
        ],
        
        'instagram' => [
            'requests_per_hour' => 200,
            'requests_per_day' => 2000,
            'burst_limit' => 20,
        ],
        
        'facebook' => [
            'requests_per_hour' => 200,
            'requests_per_day' => 2000,
            'burst_limit' => 20,
        ],
        
        'linkedin' => [
            'requests_per_hour' => 100,
            'requests_per_day' => 1000,
            'burst_limit' => 10,
        ],
        
        'tiktok' => [
            'requests_per_hour' => 100,
            'requests_per_day' => 1000,
            'burst_limit' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ML Optimization Settings
    |--------------------------------------------------------------------------
    |
    | Configure ML-based optimization parameters.
    |
    */

    'ml_optimization' => [
        'cache_hit_target' => 80, // Target 80% cache hit rate
        'data_freshness_hours' => [
            'high_priority' => 6,
            'normal_priority' => 12,
            'low_priority' => 24,
        ],
        'quality_threshold' => 70, // Minimum data quality score
        'cleanup_interval_days' => 90,
        'batch_size' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Alerts
    |--------------------------------------------------------------------------
    |
    | Configure monitoring thresholds and alert settings.
    |
    */

    'monitoring' => [
        'queue_size_alert' => 1000, // Alert if queue size exceeds this
        'failed_job_alert' => 50, // Alert if failed jobs exceed this
        'api_error_rate_alert' => 10, // Alert if API error rate exceeds this %
        'cache_hit_rate_alert' => 60, // Alert if cache hit rate drops below this %
    ],

    /*
    |--------------------------------------------------------------------------
    | Supervisor Configuration Template
    |--------------------------------------------------------------------------
    |
    | Template for supervisor configuration files.
    |
    */

    'supervisor_template' => [
        'competitor-high' => [
            'command' => 'php artisan queue:work --queue=competitor-high --sleep=1 --tries=3 --max-time=3600 --memory=512',
            'process_name' => 'competitor-high-%(process_num)02d',
            'numprocs' => 3,
            'autostart' => true,
            'autorestart' => true,
            'user' => 'www-data',
            'redirect_stderr' => true,
            'stdout_logfile' => '/var/log/supervisor/competitor-high.log',
        ],
        
        'competitor-normal' => [
            'command' => 'php artisan queue:work --queue=competitor-normal --sleep=3 --tries=3 --max-time=3600 --memory=256',
            'process_name' => 'competitor-normal-%(process_num)02d',
            'numprocs' => 2,
            'autostart' => true,
            'autorestart' => true,
            'user' => 'www-data',
            'redirect_stderr' => true,
            'stdout_logfile' => '/var/log/supervisor/competitor-normal.log',
        ],
        
        'competitor-low' => [
            'command' => 'php artisan queue:work --queue=competitor-low --sleep=5 --tries=2 --max-time=3600 --memory=128',
            'process_name' => 'competitor-low-%(process_num)02d',
            'numprocs' => 1,
            'autostart' => true,
            'autorestart' => true,
            'user' => 'www-data',
            'redirect_stderr' => true,
            'stdout_logfile' => '/var/log/supervisor/competitor-low.log',
        ],
        
        'competitor-batch' => [
            'command' => 'php artisan queue:work --queue=competitor-batch --sleep=10 --tries=2 --max-time=7200 --memory=512',
            'process_name' => 'competitor-batch-%(process_num)02d',
            'numprocs' => 1,
            'autostart' => true,
            'autorestart' => true,
            'user' => 'www-data',
            'redirect_stderr' => true,
            'stdout_logfile' => '/var/log/supervisor/competitor-batch.log',
        ],
    ],
];
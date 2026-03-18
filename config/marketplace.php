<?php

return [
    /*
     * Default commission rate (%) for template marketplace purchases.
     * Can be overridden at runtime via cache('commission.template_rate').
     */
    'commission_rate' => env('MARKETPLACE_COMMISSION_RATE', 20),

    /*
     * Default commission rate (%) for operator orders.
     * Can be overridden at runtime via cache('commission.order_rate').
     */
    'order_commission_rate' => env('ORDER_COMMISSION_RATE', 10),
];

<?php

return [
    'available_to' => [
        'NONE' => 'NONE',
        'ALL' => 'ALL',
        'FAMILY' => 'FAMILY',
        'REPEAT' => 'REPEAT',
    ],
    'status' => [
        'success' => 'SUCCESS',
        'pending' => 'PENDING',
        'failed' => 'FAILED',
    ],
    'discount_code' => [
        'family' => 'FAMILY5',
        'repeat' => 'REPEAT5',
    ],
    'messages' => [
        "discount" => [
            'valid' => 'Discount rules are valid.',
            'maxUsageLimit' => 'Maximum allowed discount code usage has been reached.',
            'maxAmountLimit' => 'Maximum allowed discount code amount has been reached.',
            'expired' => 'Discount code has expired.',
            'notAvailable' => 'Discount not Available.',
        ]
    ]
];

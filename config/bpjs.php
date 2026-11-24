<?php

return [
    'cons_id' => env('BPJS_CONS_ID', ''),
    'secret_key' => env('BPJS_SECRET_KEY', ''),
    'user_key' => env('BPJS_USER_KEY', ''),
    'env' => env('BPJS_ENV', 'dev'), // 'dev', 'prod_v1', 'prod_v2'

    'base_urls' => [
        'dev' => 'https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev',
        'prod_v1' => 'https://apijkn.bpjs-kesehatan.go.id/antreanrs',
        'prod_v2' => 'https://new-apijkn.bpjs-kesehatan.go.id/antreanrs',
    ],
];

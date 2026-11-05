<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Integrasi BPJS Kesehatan
    |--------------------------------------------------------------------------
    */
    'antrian_online' => [
        'base_url' => env('ANTRIAN_ONLINE_BPJS_URL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs'),
        'cons_id' => env('CONS_ID_ANTRIAN_ONLINE_BPJS', '16555'),
        'secret_key' => env('SECRET_KEY_ANTRIAN_ONLINE_BPJS', 'jTo7lhJsjr'),
        'user_key' => env('USER_KEY_ANTRIAN_ONLINE_BPJS', 'f4b4225536e4d1a30b175d11608b41b0'),
    ],
];


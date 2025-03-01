<?php

use Illuminate\Support\Str;

return [

    'driver' => env('SESSION_DRIVER', 'database'), // Pastikan ini sesuai dengan database

    'lifetime' => env('SESSION_LIFETIME', 120), // Tetap 120 menit (2 jam)

    'expire_on_close' => false, // Jangan expire session saat browser ditutup

    'encrypt' => false, // Tidak perlu enkripsi session kecuali data sangat sensitif

    'files' => storage_path('framework/sessions'),

    'connection' => env('SESSION_CONNECTION', null),

    'table' => 'sessions', // Pastikan tabel ini ada di database

    'store' => env('SESSION_STORE', null),

    'lottery' => [2, 100],

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_') . '_session'
    ),

    'path' => '/',

    'domain' => env('SESSION_DOMAIN', null), // Jika subdomain digunakan, atur ke '.domain.com'

    'secure' => env('SESSION_SECURE_COOKIE', false), // TRUE jika pakai HTTPS

    'http_only' => true,

    'same_site' => 'lax', // Lax lebih fleksibel untuk Remember Me

    'partitioned' => false, // Tidak perlu untuk kebanyakan aplikasi

];

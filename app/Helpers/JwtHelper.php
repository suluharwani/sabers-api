<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    function generateJWT($userId)
    {
        $key = getenv('JWT_SECRET'); // Ambil secret key dari .env
        $payload = [
            'iat' => time(), // Waktu token dibuat
            'exp' => time() + 3600, // Token berlaku selama 1 jam
            'uid' => $userId, // User ID
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}

if (!function_exists('validateJWT')) {
    function validateJWT($token)
    {
        try {
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->uid; // Return user ID
        } catch (\Exception $e) {
            return false; // Token tidak valid
        }
    }
}
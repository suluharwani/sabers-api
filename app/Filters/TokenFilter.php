<?php
// app/Filters/TokenFilter.php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class TokenFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        if (empty($header)) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Token is required',
            ])->setStatusCode(401);
        }

        $token = explode(' ', $header)[1] ?? ''; // Ambil token dari header
        $userId = $this->validateJWT($token); // Validasi token

        if (!$userId) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Invalid or expired token',
            ])->setStatusCode(401);
        }

        // Simpan user ID di request untuk digunakan di controller
        $request->userID = $userId;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa di sini
    }

    private function validateJWT($token)
    {
        try {
            $key = getenv('JWT_SECRET_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->uid; // Return user ID
        } catch (Exception $e) {
            return false; // Token tidak valid
        }
    }
}
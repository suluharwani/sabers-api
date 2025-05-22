<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeader("Authorization");
        if (!$header) {
            $response = Services::response();
            $response->setJSON(['message' => 'Token Required']);
            return $response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $token = explode(' ', $header->getValue())[1];
            JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));
        } catch (\Exception $e) {
            $response = Services::response();
            $response->setJSON(['message' => 'Invalid Token']);
            return $response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
    }
}
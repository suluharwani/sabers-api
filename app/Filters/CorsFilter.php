<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request->getMethod() === 'options') {
            $response = service('response');
            return $response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Headers', '*')
                ->setHeader('Access-Control-Allow-Methods', '*')
                ->setStatusCode(200);
        }
        
        return;
    }

   public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
{
    // Hanya tambahkan headers jika belum ada
    if (!$response->hasHeader('Access-Control-Allow-Origin')) {
        // $response->setHeader('Access-Control-Allow-Origin', '*');
    }
    
    $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
             ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-KEY')
             ->setHeader('Access-Control-Allow-Credentials', 'true');
    
    return $response;
}
}
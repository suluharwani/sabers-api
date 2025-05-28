<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle preflight request
        if ($request->getMethod() === 'options') {
            $response = service('response');
            return $response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-KEY')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE, PATCH')
                ->setStatusCode(200);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-KEY')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE, PATCH');
        
        return $response;
    }
}
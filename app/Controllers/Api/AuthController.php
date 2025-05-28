<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use App\Models\UserModel;

class AuthController extends ResourceController
{
    protected $format = 'json';
    public function options()
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setHeader('Access-Control-Allow-Methods', 'OPTIONS, POST, GET, PUT, DELETE')
            ->setStatusCode(200);
    }
   public function login()
    {
        // Handle preflight OPTIONS request
        if ($this->request->getMethod() === 'options') {
            return $this->options();
        }

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationError($this->validator->getErrors());
        }

        $model = new UserModel();
        $user = $model->findByEmail($this->request->getJsonVar('email'));

        if (!$user) {
            return $this->failNotFound('Email not found');
        }

        if (!password_verify($this->request->getJsonVar('password'), $user['password'])) {
            return $this->failUnauthorized('Invalid password');
        }

        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 hours
            'uid' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]
        ]);
    }

    public function register()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $model = new UserModel();
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password')
        ];

        $user_id = $model->insert($data);

        return $this->respondCreated([
            'status' => 201,
            'message' => 'User created successfully',
            'id' => $user_id
        ]);
    }
}
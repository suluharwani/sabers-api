<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use App\Models\UserModel;

class AuthController extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $model = new UserModel();
        $user = $model->findByEmail($this->request->getVar('email'));

        if (!$user) {
            return $this->failNotFound('Email not found');
        }

        if (!password_verify($this->request->getVar('password'), $user['password'])) {
            return $this->fail('Invalid password');
        }

        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 hours
            'uid' => $user['id'],
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 200,
            'token' => $token
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
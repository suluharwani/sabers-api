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
        // Validasi input
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Cari user berdasarkan email
        $model = new UserModel();
        $user = $model->findByEmail($this->request->getVar('email'));

        if (!$user) {
            return $this->failNotFound('Email not found');
        }

        // Verifikasi password
        if (!password_verify($this->request->getVar('password'), $user['password'])) {
            return $this->fail('Invalid password');
        }

        // Generate JWT token
        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(), // Waktu token dibuat
            'exp' => time() + (60 * 60 * 24), // Token berlaku selama 24 jam
            'uid' => $user['id'], // User ID
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        // Response
        return $this->respond([
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    public function register()
    {
        // Validasi input
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Simpan user baru
        $model = new UserModel();
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT) // Hash password
        ];

        $user_id = $model->insert($data);

        // Response
        return $this->respondCreated([
            'status' => 201,
            'message' => 'User created successfully',
            'id' => $user_id
        ]);
    }
}
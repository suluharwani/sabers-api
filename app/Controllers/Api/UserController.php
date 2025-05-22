<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class UserController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        $users = $this->model->select('id, name, email, created_at, updated_at')->findAll();
        return $this->respond($users);
    }

    public function show($id = null)
    {
        $user = $this->model->select('id, name, email, created_at, updated_at')->find($id);
        if (!$user) {
            return $this->failNotFound('User not found');
        }
        return $this->respond($user);
    }

    public function create()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password')
        ];

        $user_id = $this->model->insert($data);
        $user = $this->model->select('id, name, email, created_at, updated_at')->find($user_id);
        
        return $this->respondCreated($user);
    }

    public function update($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $rules = [
            // 'name' => 'required',
            // 'email' => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
            // 'password' => 'permit_empty|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email')
        ];

        // Only update password if provided
        if ($this->request->getVar('password')) {
            $data['password'] = $this->request->getVar('password');
        }

        $this->model->update($id, $data);
        $user = $this->model->select('id, name, email, created_at, updated_at')->find($id);
        
        return $this->respond($user);
    }

    public function delete($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'User deleted successfully']);
    }
}
<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ContactModel;

class ContactController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new ContactModel();
    }

    public function index()
    {
        $data = $this->model->orderBy('created_at', 'DESC')->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Contact message not found');
        }
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            // 'name' => 'required|min_length[3]',
            // 'email' => 'required|valid_email',
            // 'subject' => 'required|min_length[5]',
            // 'message' => 'required|min_length[10]',
            // 'phone' => 'permit_empty|numeric|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'subject' => $this->request->getVar('subject'),
            'message' => $this->request->getVar('message'),
            'status' => 'new'
        ];

        $id = $this->model->insert($data);
        $data['id'] = $id;
        
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            return $this->failNotFound('Contact message not found');
        }

        $rules = [
            'status' => 'required|in_list[new,read,replied]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'status' => $this->request->getVar('status')
        ];

        $this->model->update($id, $data);
        return $this->respond(['message' => 'Contact status updated successfully']);
    }

    public function delete($id = null)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            return $this->failNotFound('Contact message not found');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Contact message deleted successfully']);
    }
}
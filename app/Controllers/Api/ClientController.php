<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ClientModel;

class ClientController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new ClientModel();
    }

    public function index()
    {
        $data = $this->model->orderBy('name', 'ASC')->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Client not found');
        }
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'company' => 'required|min_length[2]',
            'logo' => 'uploaded[logo]|max_size[logo,4096]|is_image[logo]',
            'website' => 'valid_url_strict[https]|permit_empty',
            'industry' => 'permit_empty|min_length[2]|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $logo = $this->request->getFile('logo');
        $newName = $logo->getRandomName();
        $logo->move(FCPATH . 'uploads/clients', $newName);

        $data = [
            'name' => $this->request->getVar('name'),
            'company' => $this->request->getVar('company'),
            'logo' => $newName,
            'website' => $this->request->getVar('website'),
            'description' => $this->request->getVar('description'),
            'industry' => $this->request->getVar('industry')
        ];

        $id = $this->model->insert($data);
        $data['id'] = $id;
        
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $client = $this->model->find($id);
        if (!$client) {
            return $this->failNotFound('Client not found');
        }

        $rules = [
            'name' => 'min_length[3]',
            'company' => 'min_length[2]',
            'website' => 'valid_url_strict[https]|permit_empty',
            'industry' => 'permit_empty|min_length[2]|max_length[100]'
        ];

        if ($this->request->getFile('logo') !== null) {
            $rules['logo'] = 'uploaded[logo]|is_image[logo]';
        }

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'company' => $this->request->getVar('company'),
            'website' => $this->request->getVar('website'),
            'description' => $this->request->getVar('description'),
            'industry' => $this->request->getVar('industry')
        ];

        if ($this->request->getFile('logo') !== null) {
            // Delete old logo
            if (file_exists(FCPATH . 'uploads/clients/' . $client['logo'])) {
                unlink(FCPATH . 'uploads/clients/' . $client['logo']);
            }

            $logo = $this->request->getFile('logo');
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/clients', $newName);
            $data['logo'] = $newName;
        }

        $this->model->update($id, $data);
        return $this->respond(['message' => 'Client updated successfully']);
    }

    public function delete($id = null)
    {
        $client = $this->model->find($id);
        if (!$client) {
            return $this->failNotFound('Client not found');
        }

        // Delete logo file
        if (file_exists(FCPATH . 'uploads/clients/' . $client['logo'])) {
            unlink(FCPATH . 'uploads/clients/' . $client['logo']);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Client deleted successfully']);
    }
}
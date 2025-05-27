<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\SponsorModel;

class SponsorController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new SponsorModel();
    }

    public function index()
    {
        $data = $this->model->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Sponsor not found');
        }
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'logo' => 'uploaded[logo]|max_size[logo,4096]|is_image[logo]',
            'website' => 'valid_url_strict[https]|permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $logo = $this->request->getFile('logo');
        $newName = $logo->getRandomName();
        $logo->move(FCPATH . 'uploads/sponsors', $newName);

        $data = [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'logo' => $newName,
            'website' => $this->request->getVar('website')
        ];

        $id = $this->model->insert($data);
        $data['id'] = $id;
        
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $sponsor = $this->model->find($id);
        if (!$sponsor) {
            return $this->failNotFound('Sponsor not found');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'website' => 'valid_url_strict[https]|permit_empty'
        ];

        if ($this->request->getFile('logo') !== null) {
            $rules['logo'] = 'uploaded[logo]|max_size[logo,4096]|is_image[logo]';
        }

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'website' => $this->request->getVar('website')
        ];

        if ($this->request->getFile('logo') !== null) {
            // Delete old logo
            if (file_exists(FCPATH . 'uploads/sponsors/' . $sponsor['logo'])) {
                unlink(FCPATH . 'uploads/sponsors/' . $sponsor['logo']);
            }

            $logo = $this->request->getFile('logo');
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/sponsors', $newName);
            $data['logo'] = $newName;
        }

        $this->model->update($id, $data);
        return $this->respond(['message' => 'Sponsor updated successfully']);
    }

    public function delete($id = null)
    {
        $sponsor = $this->model->find($id);
        if (!$sponsor) {
            return $this->failNotFound('Sponsor not found');
        }

        // Delete logo file
        if (file_exists(FCPATH . 'uploads/sponsors/' . $sponsor['logo'])) {
            unlink(FCPATH . 'uploads/sponsors/' . $sponsor['logo']);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Sponsor deleted successfully']);
    }
}
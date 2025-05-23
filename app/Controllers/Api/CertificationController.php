<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CertificationModel;

class CertificationController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new CertificationModel();
    }

    public function index()
    {
        // Ambil parameter filter jika ada
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        
        $builder = $this->model->builder();
        
        // Filter berdasarkan type jika ada
        if (!empty($type) && in_array($type, ['HR', 'Company'])) {
            $builder->where('type', $type);
        }
        
        // Filter berdasarkan status jika ada
        if (!empty($status) && in_array($status, ['active', 'expired', 'revoked'])) {
            $builder->where('status', $status);
        }
        
        $data = $builder->orderBy('issue_date', 'DESC')->get()->getResult();
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'certification_name' => 'required|min_length[3]',
            'type' => 'required|in_list[HR,Company]',
            'issue_date' => 'required|valid_date',
            'expiration_date' => 'permit_empty|valid_date',
            'credential_id' => 'permit_empty|max_length[100]',
            'credential_url' => 'permit_empty|valid_url',
            'status' => 'permit_empty|in_list[active,expired,revoked]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'certification_name' => $this->request->getVar('certification_name'),
            'type' => $this->request->getVar('type'),
            'issue_date' => $this->request->getVar('issue_date'),
            'expiration_date' => $this->request->getVar('expiration_date'),
            'credential_id' => $this->request->getVar('credential_id'),
            'credential_url' => $this->request->getVar('credential_url'),
            'status' => $this->request->getVar('status') ?? 'active'
        ];

        $id = $this->model->insert($data);
        $data['id'] = $id;
        
        return $this->respondCreated($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Certification record not found');
        }
        return $this->respond($data);
    }

  public function byType($type)
    {
        if (!in_array($type, ['HR', 'Company'])) {
            return $this->failValidationError('Invalid type');
        }
        
        $data = $this->model->where('type', $type)
                          ->orderBy('issue_date', 'DESC')
                          ->findAll();
        return $this->respond($data);
    }

    public function update($id = null)
    {
        $certification = $this->model->find($id);
        if (!$certification) {
            return $this->failNotFound('Certification record not found');
        }

        $rules = [
            'certification_name' => 'permit_empty|min_length[3]',
            'type' => 'in_list[HR,Company]',
            'issue_date' => 'permit_empty|valid_date',
            'expiration_date' => 'permit_empty|valid_date',
            'credential_id' => 'permit_empty|max_length[100]',
            'credential_url' => 'permit_empty|valid_url',
            'status' => 'permit_empty|in_list[active,expired,revoked]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'certification_name' => $this->request->getVar('certification_name') ?? $certification['certification_name'],
            'type' => $this->request->getVar('type') ?? $certification['type'],
            'issue_date' => $this->request->getVar('issue_date') ?? $certification['issue_date'],
            'expiration_date' => $this->request->getVar('expiration_date') ?? $certification['expiration_date'],
            'credential_id' => $this->request->getVar('credential_id') ?? $certification['credential_id'],
            'credential_url' => $this->request->getVar('credential_url') ?? $certification['credential_url'],
            'status' => $this->request->getVar('status') ?? $certification['status']
        ];

        $this->model->update($id, $data);
        return $this->respond(['message' => 'Certification updated successfully']);
    }

    public function delete($id = null)
    {
        $certification = $this->model->find($id);
        if (!$certification) {
            return $this->failNotFound('Certification record not found');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Certification record deleted successfully']);
    }
    public function activeCertifications()
{
    $data = $this->model->where('status', 'active')
                       ->orderBy('issue_date', 'DESC')
                       ->findAll();
    return $this->respond($data);
}

public function expiredCertifications()
{
    $data = $this->model->where('status', 'expired')
                       ->orderBy('expiration_date', 'DESC')
                       ->findAll();
    return $this->respond($data);
}
}
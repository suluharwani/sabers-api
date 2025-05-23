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
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        
        $builder = $this->model->builder();
        
        if (!empty($type) && in_array($type, ['HR', 'Company'])) {
            $builder->where('type', $type);
        }
        
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
            'description' => 'permit_empty|string', // Added description validation
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
            'description' => $this->request->getVar('description'), // Added description
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
        // Check if certification exists
        $certification = $this->model->find($id);
        if (!$certification) {
            return $this->failNotFound('Certification record not found');
        }

        // Set validation rules
        $rules = [
            'certification_name' => 'permit_empty|min_length[3]',
            'type' => 'permit_empty|in_list[HR,Company]',
            'description' => 'permit_empty|string',
            'issue_date' => 'permit_empty|valid_date',
            'expiration_date' => 'permit_empty|valid_date',
            'credential_id' => 'permit_empty|max_length[100]',
            'credential_url' => 'permit_empty|valid_url',
            'status' => 'permit_empty|in_list[active,expired,revoked]'
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        // Prepare update data
        $updateData = [];
        
        // Only update fields that are provided in the request
        if ($this->request->getVar('certification_name') !== null) {
            $updateData['certification_name'] = $this->request->getVar('certification_name');
        }
        
        if ($this->request->getVar('type') !== null) {
            $updateData['type'] = $this->request->getVar('type');
        }
        
        if ($this->request->getVar('description') !== null) {
            $updateData['description'] = $this->request->getVar('description');
        }
        
        if ($this->request->getVar('issue_date') !== null) {
            $updateData['issue_date'] = $this->request->getVar('issue_date');
        }
        
        if ($this->request->getVar('expiration_date') !== null) {
            $updateData['expiration_date'] = $this->request->getVar('expiration_date');
        }
        
        if ($this->request->getVar('credential_id') !== null) {
            $updateData['credential_id'] = $this->request->getVar('credential_id');
        }
        
        if ($this->request->getVar('credential_url') !== null) {
            $updateData['credential_url'] = $this->request->getVar('credential_url');
        }
        
        if ($this->request->getVar('status') !== null) {
            $updateData['status'] = $this->request->getVar('status');
        }

        // If no data provided to update
        if (empty($updateData)) {
            return $this->failValidationError('No data provided for update');
        }

        // Perform the update
        if ($this->model->update($id, $updateData)) {
            // Get the updated record
            $updatedCertification = $this->model->find($id);
            return $this->respond([
                'status' => 'success',
                'message' => 'Certification updated successfully',
                'data' => $updatedCertification
            ]);
        } else {
            return $this->failServerError('Failed to update certification');
        }
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
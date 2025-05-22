<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificationModel extends Model
{
    protected $table = 'certifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'certification_name',
        'issuing_organization',
        'issue_date',
        'expiration_date',
        'credential_id',
        'credential_url',
        'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'certification_name' => 'required|min_length[3]',
        'issuing_organization' => 'required|min_length[3]',
        'issue_date' => 'required|valid_date',
        'status' => 'permit_empty|in_list[active,expired,revoked]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
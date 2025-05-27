<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectImageModel extends Model
{
    protected $table = 'project_images';
    protected $primaryKey = 'id';
    protected $allowedFields = ['project_id', 'image_path'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
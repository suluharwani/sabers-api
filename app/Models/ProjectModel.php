<?php
// app/Models/ProjectModel.php
namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table      = 'projects';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'client_id', 'description', 'thumbnail', 'start_date', 'end_date', 'status', 'budget', 'location'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
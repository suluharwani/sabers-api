<?php
// app/Models/SponsorModel.php
namespace App\Models;

use CodeIgniter\Model;

class SponsorModel extends Model
{
    protected $table      = 'sponsors';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'logo', 'website'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
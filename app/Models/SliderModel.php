<?php
// app/Models/SliderModel.php
namespace App\Models;

use CodeIgniter\Model;

class SliderModel extends Model
{
    protected $table      = 'sliders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'image'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
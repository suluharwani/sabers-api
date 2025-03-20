<?php
// app/Models/BlogTagModel.php
namespace App\Models;

use CodeIgniter\Model;

class BlogTagModel extends Model
{
    protected $table      = 'blog_tags';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
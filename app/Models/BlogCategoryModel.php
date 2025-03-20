<?php
// app/Models/BlogCategoryModel.php
namespace App\Models;

use CodeIgniter\Model;

class BlogCategoryModel extends Model
{
    protected $table      = 'blog_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug', 'description'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
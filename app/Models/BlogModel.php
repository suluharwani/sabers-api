<?php
// app/Models/BlogModel.php
namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table      = 'blogs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'slug', 'content', 'excerpt', 'featured_image', 'category_id', 'author_id', 'status', 'published_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
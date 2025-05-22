<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogCategoryModel extends Model
{
    protected $table = 'blog_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug', 'description'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['name'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }
}
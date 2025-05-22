<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogTagModel extends Model
{
    protected $table = 'blog_tags';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug'];
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
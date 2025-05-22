<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table = 'blogs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'slug', 'content', 'excerpt', 'featured_image',
        'category_id', 'author_id', 'status', 'published_at'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateSlug', 'setPublishedAt'];
    protected $beforeUpdate = ['generateSlug', 'setPublishedAt'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['title'])) {
            $data['data']['slug'] = url_title($data['data']['title'], '-', true);
        }
        return $data;
    }

    protected function setPublishedAt(array $data)
    {
        if (isset($data['data']['status']) && $data['data']['status'] === 'published') {
            if (empty($data['data']['published_at'])) {
                $data['data']['published_at'] = date('Y-m-d H:i:s');
            }
        }
        return $data;
    }

    public function getWithRelations($id = null)
    {
        $this->select('blogs.*, blog_categories.name as category_name, users.name as author_name');
        $this->join('blog_categories', 'blog_categories.id = blogs.category_id');
        $this->join('users', 'users.id = blogs.author_id');
        
        if ($id !== null) {
            return $this->find($id);
        }
        
        return $this->findAll();
    }

    public function getTags($blogId)
    {
        $db = \Config\Database::connect();
        return $db->table('blog_tags')
            ->select('blog_tags.*')
            ->join('blog_tag_pivot', 'blog_tag_pivot.tag_id = blog_tags.id')
            ->where('blog_tag_pivot.blog_id', $blogId)
            ->get()
            ->getResultArray();
    }

    public function syncTags($blogId, array $tagIds)
    {
        $db = \Config\Database::connect();
        
        // Remove existing tags
        $db->table('blog_tag_pivot')->where('blog_id', $blogId)->delete();
        
        // Add new tags
        $data = array_map(function($tagId) use ($blogId) {
            return [
                'blog_id' => $blogId,
                'tag_id' => $tagId
            ];
        }, $tagIds);
        
        if (!empty($data)) {
            $db->table('blog_tag_pivot')->insertBatch($data);
        }
    }
}
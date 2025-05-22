<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BlogModel;
use App\Models\BlogCategoryModel;
use App\Models\BlogTagModel;

class BlogController extends ResourceController
{
    protected $format = 'json';
    protected $model;
    protected $categoryModel;
    protected $tagModel;

    public function __construct()
    {
        $this->model = new BlogModel();
        $this->categoryModel = new BlogCategoryModel();
        $this->tagModel = new BlogTagModel();
    }

    // public function index()
    // {
    //     $blogs = $this->model->getWithRelations();
        
    //     // Add tags to each blog
    //     foreach ($blogs as &$blog) {
    //         $blog['tags'] = $this->model->getTags($blog['id']);
    //     }
        
    //     return $this->respond($blogs);
    // }

    // public function show($id = null)
    // {
    //     $blog = $this->model->getWithRelations($id);
    //     if (!$blog) {
    //         return $this->failNotFound('Blog post not found');
    //     }
        
    //     $blog['tags'] = $this->model->getTags($blog['id']);
    //     return $this->respond($blog);
    // }
    public function index()
    {
        $data = $this->model->orderBy('title', 'ASC')->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Client not found');
        }
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required',
            'featured_image' => 'uploaded[featured_image]|max_size[featured_image,4096]|is_image[featured_image]',
            'category_id' => 'is_natural_no_zero',
            'status' => 'required|in_list[draft,published,archived]',
            'tags' => 'permit_empty|is_natural_no_zero[]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $image = $this->request->getFile('featured_image');
        $newName = $image->getRandomName();
        $image->move(FCPATH . 'uploads/blogs', $newName);

        $data = [
            'title' => $this->request->getVar('title'),
            'content' => $this->request->getVar('content'),
            'excerpt' => $this->request->getVar('excerpt'),
            'featured_image' => $newName,
            'category_id' => $this->request->getVar('category_id'),
            'author_id' => 1, // TODO: Get from authenticated user
            'status' => $this->request->getVar('status')
        ];

        $id = $this->model->insert($data);
        
        // Sync tags if provided
        $tags = $this->request->getVar('tags');
        if (!empty($tags)) {
            $this->model->syncTags($id, $tags);
        }
        
        $blog = $this->model->getWithRelations($id);
        $blog['tags'] = $this->model->getTags($id);
        
        return $this->respondCreated($blog);
    }

    public function update($id = null)
    {
        $blog = $this->model->find($id);
        if (!$blog) {
            return $this->failNotFound('Blog post not found');
        }

        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required',
            // 'category_id' => 'required|is_natural_no_zero|exists[blog_categories.id]',
            'status' => 'required|in_list[draft,published,archived]',
            'tags' => 'permit_empty|is_natural_no_zero[]'
        ];

        if ($this->request->getFile('featured_image') !== null) {
            $rules['featured_image'] = 'uploaded[featured_image]|max_size[featured_image,4096]|is_image[featured_image]';
        }

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'content' => $this->request->getVar('content'),
            'excerpt' => $this->request->getVar('excerpt'),
            'category_id' => $this->request->getVar('category_id'),
            'status' => $this->request->getVar('status')
        ];

        if ($this->request->getFile('featured_image') !== null) {
            // Delete old image
            if (file_exists(FCPATH . 'uploads/blogs/' . $blog['featured_image'])) {
                unlink(FCPATH . 'uploads/blogs/' . $blog['featured_image']);
            }

            $image = $this->request->getFile('featured_image');
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/blogs', $newName);
            $data['featured_image'] = $newName;
        }

        $this->model->update($id, $data);
        
        // Sync tags if provided
        $tags = $this->request->getVar('tags');
        if ($tags !== null) {
            $this->model->syncTags($id, $tags);
        }
        
        $blog = $this->model->getWithRelations($id);
        $blog['tags'] = $this->model->getTags($id);
        
        return $this->respond($blog);
    }

    public function delete($id = null)
    {
        $blog = $this->model->find($id);
        if (!$blog) {
            return $this->failNotFound('Blog post not found');
        }

        // Delete featured image
        if (file_exists(FCPATH . 'uploads/blogs/' . $blog['featured_image'])) {
            unlink(FCPATH . 'uploads/blogs/' . $blog['featured_image']);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Blog post deleted successfully']);
    }
}
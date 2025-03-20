<?php
// app/Controllers/Api/BlogController.php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BlogModel;
use App\Models\BlogCategoryModel;
use App\Models\BlogTagModel;

class BlogController extends ResourceController
{
    protected $format = 'json';

    // Get all blogs
    public function index()
    {
        $model = new BlogModel();
        $blogs = $model->select('blogs.*, blog_categories.name as category_name, users.name as author_name')
            ->join('blog_categories', 'blog_categories.id = blogs.category_id')
            ->join('users', 'users.id = blogs.author_id')
            ->findAll();

        return $this->respond([
            'status' => 200,
            'data' => $blogs
        ]);
    }

    // Get a single blog by ID
    public function show($id = null)
    {
        $model = new BlogModel();
        $blog = $model->select('blogs.*, blog_categories.name as category_name, users.name as author_name')
            ->join('blog_categories', 'blog_categories.id = blogs.category_id')
            ->join('users', 'users.id = blogs.author_id')
            ->find($id);

        if ($blog) {
            return $this->respond([
                'status' => 200,
                'data' => $blog
            ]);
        } else {
            return $this->failNotFound('Blog not found');
        }
    }

    // Create a new blog
    public function create()
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required|is_unique[blogs.slug]',
            'content' => 'required',
            'category_id' => 'required|numeric',
            'author_id' => 'required|numeric',
            'status' => 'required|in_list[draft,published,archived]',
            'featured_image' => 'uploaded[featured_image]|max_size[featured_image,2048]|mime_in[featured_image,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Upload featured image
        $featuredImage = $this->request->getFile('featured_image');
        if ($featuredImage->isValid() && !$featuredImage->hasMoved()) {
            $newName = $featuredImage->getRandomName();
            $featuredImage->move(WRITEPATH . 'uploads', $newName);
        } else {
            return $this->fail('Failed to upload featured image');
        }

        $model = new BlogModel();
        $data = [
            'title' => $this->request->getVar('title'),
            'slug' => $this->request->getVar('slug'),
            'content' => $this->request->getVar('content'),
            'excerpt' => $this->request->getVar('excerpt'),
            'featured_image' => $newName, // Simpan nama file gambar
            'category_id' => $this->request->getVar('category_id'),
            'author_id' => $this->request->getVar('author_id'),
            'status' => $this->request->getVar('status'),
            'published_at' => $this->request->getVar('published_at'),
        ];

        $blogId = $model->insert($data);

        // Attach tags if provided
        $tagIds = $this->request->getVar('tag_ids');
        if ($tagIds && is_array($tagIds)) {
            $model->tags()->attach($tagIds);
        }

        return $this->respondCreated([
            'status' => 201,
            'message' => 'Blog created successfully',
            'id' => $blogId
        ]);
    }

    // Update a blog
    public function update($id = null)
    {
        $model = new BlogModel();
        $blog = $model->find($id);

        if (!$blog) {
            return $this->failNotFound('Blog not found');
        }

        $rules = [
            'title' => 'required',
            'slug' => "required|is_unique[blogs.slug,id,{$id}]",
            'content' => 'required',
            'category_id' => 'required|numeric',
            'author_id' => 'required|numeric',
            'status' => 'required|in_list[draft,published,archived]',
            'featured_image' => 'max_size[featured_image,2048]|mime_in[featured_image,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'slug' => $this->request->getVar('slug'),
            'content' => $this->request->getVar('content'),
            'excerpt' => $this->request->getVar('excerpt'),
            'category_id' => $this->request->getVar('category_id'),
            'author_id' => $this->request->getVar('author_id'),
            'status' => $this->request->getVar('status'),
            'published_at' => $this->request->getVar('published_at'),
        ];

        // Handle featured image upload if provided
        $featuredImage = $this->request->getFile('featured_image');
        if ($featuredImage && $featuredImage->isValid() && !$featuredImage->hasMoved()) {
            $newName = $featuredImage->getRandomName();
            $featuredImage->move(WRITEPATH . 'uploads', $newName);

            // Hapus gambar lama jika ada
            if ($blog['featured_image']) {
                unlink(WRITEPATH . 'uploads/' . $blog['featured_image']);
            }

            $data['featured_image'] = $newName;
        }

        $model->update($id, $data);

        // Sync tags if provided
        $tagIds = $this->request->getVar('tag_ids');
        if ($tagIds && is_array($tagIds)) {
            $model->tags()->sync($tagIds);
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Blog updated successfully'
        ]);
    }

    // Delete a blog
    public function delete($id = null)
    {
        $model = new BlogModel();
        $blog = $model->find($id);

        if (!$blog) {
            return $this->failNotFound('Blog not found');
        }

        // Hapus gambar jika ada
        if ($blog['featured_image']) {
            unlink(WRITEPATH . 'uploads/' . $blog['featured_image']);
        }

        $model->delete($id);

        return $this->respond([
            'status' => 200,
            'message' => 'Blog deleted successfully'
        ]);
    }
}
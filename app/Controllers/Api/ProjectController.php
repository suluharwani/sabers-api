<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProjectModel;
use App\Models\ProjectImageModel;

class ProjectController extends ResourceController
{
    protected $format = 'json';
    protected $model;
    protected $imageModel;

    public function __construct()
    {
        $this->model = new ProjectModel();
        $this->imageModel = new ProjectImageModel();
    }

    public function index()
    {
        $data = $this->model->getWithClientAndImages();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->getWithClientAndImages($id);
        if (!$data) {
            return $this->failNotFound('Project not found');
        }
        return $this->respond($data);
    }

public function create()
{
    $rules = [
        'title' => 'required|min_length[3]',
        'description' => 'required',
        'images[]' => 'uploaded[images]|max_size[images,4096]|is_image[images]',
        'start_date' => 'required|valid_date',
        'end_date' => 'permit_empty|valid_date',
        'status' => 'required|in_list[planned,ongoing,completed,cancelled]',
        'budget' => 'permit_empty|numeric',
        'location' => 'permit_empty|min_length[2]',
        'client_id' => 'permit_empty|is_natural_no_zero|field_exists[clients.id]'
    ];

    if (!$this->validate($rules)) {
        return $this->fail($this->validator->getErrors());
    }

    $data = [
        'title' => $this->request->getVar('title'),
        'client_id' => $this->request->getVar('client_id'),
        'description' => $this->request->getVar('description'),
        'start_date' => $this->request->getVar('start_date'),
        'end_date' => $this->request->getVar('end_date'),
        'status' => $this->request->getVar('status'),
        'budget' => $this->request->getVar('budget'),
        'location' => $this->request->getVar('location')
    ];

    $projectId = $this->model->insert($data);

    // Handle file uploads
    $uploadedFiles = $this->request->getFiles();
    
    if ($uploadedFiles && isset($uploadedFiles['images'])) {
        foreach ($uploadedFiles['images'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/projects', $newName);

                $this->imageModel->insert([
                    'project_id' => $projectId,
                    'image_path' => $newName
                ]);
            }
        }
    }

    $data['id'] = $projectId;
    $data['images'] = $this->imageModel->where('project_id', $projectId)->findAll();
    
    return $this->respondCreated($data);
}

public function update($id = null)
{
    $project = $this->model->find($id);
    if (!$project) {
        return $this->failNotFound('Project not found');
    }

    $rules = [
        'title' => 'required|min_length[3]',
        'description' => 'required',
        'images' => 'permit_empty|uploaded[images]|max_size[images,4096]|is_image[images]',
        'start_date' => 'required|valid_date',
        'end_date' => 'permit_empty|valid_date',
        'status' => 'required|in_list[planned,ongoing,completed,cancelled]',
        'budget' => 'permit_empty|numeric',
        'location' => 'permit_empty|min_length[2]',
        'client_id' => 'permit_empty|is_natural_no_zero|field_exists[clients.id]'
    ];

    if (!$this->validate($rules)) {
        return $this->fail($this->validator->getErrors());
    }

    $data = [
        'title' => $this->request->getVar('title'),
        'client_id' => $this->request->getVar('client_id'),
        'description' => $this->request->getVar('description'),
        'start_date' => $this->request->getVar('start_date'),
        'end_date' => $this->request->getVar('end_date'),
        'status' => $this->request->getVar('status'),
        'budget' => $this->request->getVar('budget'),
        'location' => $this->request->getVar('location')
    ];

    $this->model->update($id, $data);

    // Handle new image uploads
    if ($files = $this->request->getFiles()) {
        if (!empty($files['images'])) {
            foreach ($files['images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName();
                    $img->move(FCPATH . 'uploads/projects', $newName);

                    $this->imageModel->insert([
                        'project_id' => $id,
                        'image_path' => $newName
                    ]);
                }
            }
        }
    }

    return $this->respond(['message' => 'Project updated successfully']);
}

    public function delete($id = null)
    {
        $project = $this->model->find($id);
        if (!$project) {
            return $this->failNotFound('Project not found');
        }

        // Delete associated images
        $images = $this->imageModel->where('project_id', $id)->findAll();
        foreach ($images as $image) {
            if (file_exists(FCPATH . 'uploads/projects/' . $image['image_path'])) {
                unlink(FCPATH . 'uploads/projects/' . $image['image_path']);
            }
        }
        $this->imageModel->where('project_id', $id)->delete();

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Project deleted successfully']);
    }

    // Additional method to manage images
    public function deleteImage($projectId, $imageId)
    {
        $image = $this->imageModel->find($imageId);
        if (!$image || $image['project_id'] != $projectId) {
            return $this->failNotFound('Image not found');
        }

        if (file_exists(FCPATH . 'uploads/projects/' . $image['image_path'])) {
            unlink(FCPATH . 'uploads/projects/' . $image['image_path']);
        }

        $this->imageModel->delete($imageId);
        return $this->respondDeleted(['message' => 'Image deleted successfully']);
    }
}
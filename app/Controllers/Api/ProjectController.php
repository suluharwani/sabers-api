<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProjectModel;

class ProjectController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new ProjectModel();
    }

    public function index()
    {
        $data = $this->model->getWithClient();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->getWithClient($id);
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
            'thumbnail' => 'uploaded[thumbnail]|max_size[thumbnail,4096]|is_image[thumbnail]',
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

        $thumbnail = $this->request->getFile('thumbnail');
        $newName = $thumbnail->getRandomName();
        $thumbnail->move(FCPATH . 'uploads/projects', $newName);

        $data = [
            'title' => $this->request->getVar('title'),
            'client_id' => $this->request->getVar('client_id'),
            'description' => $this->request->getVar('description'),
            'thumbnail' => $newName,
            'start_date' => $this->request->getVar('start_date'),
            'end_date' => $this->request->getVar('end_date'),
            'status' => $this->request->getVar('status'),
            'budget' => $this->request->getVar('budget'),
            'location' => $this->request->getVar('location')
        ];

        $id = $this->model->insert($data);
        $data['id'] = $id;
        
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
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'status' => 'required|in_list[planned,ongoing,completed,cancelled]',
            'budget' => 'permit_empty|numeric',
            'location' => 'permit_empty|min_length[2]',
            'client_id' => 'permit_empty|is_natural_no_zero|field_exists[clients.id]'
        ];

        if ($this->request->getFile('thumbnail') !== null) {
            $rules['thumbnail'] = 'uploaded[thumbnail]|max_size[thumbnail,4096]|is_image[thumbnail]';
        }

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

        if ($this->request->getFile('thumbnail') !== null) {
            // Delete old thumbnail
            if (file_exists(FCPATH . 'uploads/projects/' . $project['thumbnail'])) {
                unlink(FCPATH . 'uploads/projects/' . $project['thumbnail']);
            }

            $thumbnail = $this->request->getFile('thumbnail');
            $newName = $thumbnail->getRandomName();
            $thumbnail->move(FCPATH . 'uploads/projects', $newName);
            $data['thumbnail'] = $newName;
        }

        $this->model->update($id, $data);
        return $this->respond(['message' => 'Project updated successfully']);
    }

    public function delete($id = null)
    {
        $project = $this->model->find($id);
        if (!$project) {
            return $this->failNotFound('Project not found');
        }

        // Delete thumbnail file
        if (file_exists(FCPATH . 'uploads/projects/' . $project['thumbnail'])) {
            unlink(FCPATH . 'uploads/projects/' . $project['thumbnail']);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Project deleted successfully']);
    }
}
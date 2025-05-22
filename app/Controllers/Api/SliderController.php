<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\SliderModel;

class SliderController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        $this->model = new SliderModel();
    }

    public function index()
    {
        $data = $this->model->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Slider not found');
        }
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'title' => 'required',
            'image' => 'uploaded[image]|max_size[image,4096]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $image = $this->request->getFile('image');
        $newName = $image->getRandomName();
        $image->move(FCPATH . 'uploads/sliders', $newName);

        $data = [
            'title' => $this->request->getVar('title'),
            'description' => $this->request->getVar('description'),
            'image' => $newName
        ];

        $id = $this->model->insert($data);
        $data['id'] = $id;
        
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $slider = $this->model->find($id);
        if (!$slider) {
            return $this->failNotFound('Slider not found');
        }

        $rules = [
            // 'title' => 'required'
        ];

        if ($this->request->getFile('image') !== null) {
            $rules['image'] = 'uploaded[image]|max_size[image,4096]|is_image[image]';
        }

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'description' => $this->request->getVar('description')
        ];

        if ($this->request->getFile('image') !== null) {
            // Delete old image
            if (file_exists(FCPATH . 'uploads/sliders/' . $slider['image'])) {
                unlink(FCPATH . 'uploads/sliders/' . $slider['image']);
            }

            $image = $this->request->getFile('image');
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/sliders', $newName);
            $data['image'] = $newName;
        }

        $this->model->update($id, $data);
        return $this->respond(['message' => 'Slider updated successfully']);
    }

    public function delete($id = null)
    {
        $slider = $this->model->find($id);
        if (!$slider) {
            return $this->failNotFound('Slider not found');
        }

        // Delete image file
        if (file_exists(FCPATH . 'uploads/sliders/' . $slider['image'])) {
            unlink(FCPATH . 'uploads/sliders/' . $slider['image']);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Slider deleted successfully']);
    }
}
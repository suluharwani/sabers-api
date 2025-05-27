<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'client_id', 'description',
        'start_date', 'end_date', 'status', 'budget', 'location'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]',
        'start_date' => 'required|valid_date',
        'end_date' => 'permit_empty|valid_date',
        'status' => 'required|in_list[planned,ongoing,completed,cancelled]',
        'budget' => 'permit_empty|numeric',
        'location' => 'permit_empty|min_length[2]'
    ];

    public function getWithClientAndImages($id = null)
    {
        $this->select('projects.*, clients.name as client_name, clients.company as client_company');
        $this->join('clients', 'clients.id = projects.client_id', 'left');
        
        if ($id !== null) {
            $project = $this->find($id);
            if ($project) {
                $imageModel = new ProjectImageModel();
                $project['images'] = $imageModel->where('project_id', $id)->findAll();
            }
            return $project;
        }
        
        $projects = $this->findAll();
        $imageModel = new ProjectImageModel();
        
        foreach ($projects as &$project) {
            $project['images'] = $imageModel->where('project_id', $project['id'])->findAll();
        }
        
        return $projects;
    }
}
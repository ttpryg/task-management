<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['title', 'description', 'deadline', 'status', 'user_id', 'category_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty',
        'deadline' => 'permit_empty|valid_date',
        'status' => 'required|in_list[pending,in_progress,completed]',
        'user_id' => 'required|integer',
        'category_id' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'title' => [
            'required' => 'Task title is required',
            'min_length' => 'Task title must be at least 3 characters long',
            'max_length' => 'Task title cannot exceed 255 characters'
        ],
        'deadline' => [
            'valid_date' => 'Please enter a valid date'
        ],
        'status' => [
            'required' => 'Task status is required',
            'in_list' => 'Invalid task status'
        ],
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be a valid integer'
        ],
        'category_id' => [
            'required' => 'Category ID is required',
            'integer' => 'Category ID must be a valid integer'
        ]
    ];
    
    protected $skipValidation = false;
} 
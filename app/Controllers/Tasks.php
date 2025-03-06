<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\CategoryModel;

class Tasks extends BaseController
{
    protected $taskModel;
    protected $categoryModel;
    
    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->categoryModel = new CategoryModel();
    }
    
    public function index()
    {
        $userId = session()->get('id');
        
        // Get filter parameters
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $categoryId = $this->request->getGet('category_id');
        
        // Start building the query
        $query = $this->taskModel->where('user_id', $userId);
        
        // Apply filters if they exist
        if ($search) {
            $query->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $data = [
            'tasks' => $query->findAll(),
            'categories' => $this->categoryModel->where('user_id', $userId)->findAll(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'category_id' => $categoryId
            ]
        ];
        
        return view('tasks/index', $data);
    }
    
    protected function sendResponse($data, $statusCode = 200)
    {
        // Get new CSRF token
        $newToken = csrf_hash();
        
        // Add CSRF token to response data
        $data['csrf_token'] = $newToken;
        
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data)
            ->setHeader('X-CSRF-TOKEN', $newToken);
    }

    public function create()
    {
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'deadline' => $this->request->getPost('deadline'),
            'category_id' => $this->request->getPost('category_id'),
            'status' => $this->request->getPost('status') ?: 'pending',
            'user_id' => session()->get('id')
        ];
        
        // Remove empty deadline if not set
        if (empty($data['deadline'])) {
            unset($data['deadline']);
        }
        
        if ($this->taskModel->insert($data)) {
            // Get the newly created task with all necessary data
            $task = $this->taskModel->find($this->taskModel->getInsertID());
            
            // Get category details
            if ($task['category_id']) {
                $category = $this->categoryModel->find($task['category_id']);
                $task['category_name'] = $category ? $category['name'] : '';
            }
            
            return $this->sendResponse([
                'success' => true,
                'message' => 'Task created successfully',
                'task' => $task
            ]);
        }
        
        return $this->sendResponse([
            'success' => false,
            'message' => 'Failed to create task',
            'errors' => $this->taskModel->errors()
        ], 400);
    }
    
    public function update($id)
    {
        $userId = session()->get('id');
        $task = $this->taskModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$task) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
        
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'deadline' => $this->request->getPost('deadline'),
            'category_id' => $this->request->getPost('category_id'),
            'status' => $this->request->getPost('status')
        ];
        
        // Remove empty deadline if not set
        if (empty($data['deadline'])) {
            unset($data['deadline']);
        }
        
        // Remove fields that weren't sent (to prevent overwriting with null)
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }
        
        if ($this->taskModel->update($id, $data)) {
            // Get the updated task with all necessary data
            $updatedTask = $this->taskModel->find($id);
            
            // Get category details
            if ($updatedTask['category_id']) {
                $category = $this->categoryModel->find($updatedTask['category_id']);
                $updatedTask['category_name'] = $category ? $category['name'] : '';
            }
            
            return $this->sendResponse([
                'success' => true,
                'message' => 'Task updated successfully',
                'task' => $updatedTask
            ]);
        }
        
        return $this->sendResponse([
            'success' => false,
            'message' => 'Failed to update task',
            'errors' => $this->taskModel->errors()
        ], 400);
    }
    
    public function delete($id)
    {
        $userId = session()->get('id');
        $task = $this->taskModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$task) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
        
        if ($this->taskModel->delete($id)) {
            return $this->sendResponse([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        }
        
        return $this->sendResponse([
            'success' => false,
            'message' => 'Failed to delete task'
        ], 500);
    }
    
    public function updateStatus($id)
    {
        $userId = session()->get('id');
        $task = $this->taskModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$task) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
        
        $status = $this->request->getPost('status');
        if (!in_array($status, ['pending', 'in_progress', 'completed'])) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'Invalid status'
            ], 400);
        }
        
        if ($this->taskModel->update($id, ['status' => $status])) {
            // Get the updated task with all necessary data
            $updatedTask = $this->taskModel->find($id);
            
            // Get category details
            if ($updatedTask['category_id']) {
                $category = $this->categoryModel->find($updatedTask['category_id']);
                $updatedTask['category_name'] = $category ? $category['name'] : '';
            }
            
            // Generate new CSRF token
            $newToken = csrf_hash();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Task status updated successfully',
                'task' => $updatedTask,
                'csrf_token' => $newToken
            ])->setHeader('X-CSRF-TOKEN', $newToken);
        }
        
        return $this->sendResponse([
            'success' => false,
            'message' => 'Failed to update task status'
        ], 500);
    }
    
    public function get($id)
    {
        $userId = session()->get('id');
        $task = $this->taskModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$task) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
        
        // Get category details
        if ($task['category_id']) {
            $category = $this->categoryModel->find($task['category_id']);
            $task['category_name'] = $category ? $category['name'] : '';
        }
        
        return $this->sendResponse([
            'success' => true,
            'task' => $task
        ]);
    }
} 
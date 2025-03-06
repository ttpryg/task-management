<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Categories extends BaseController
{
    protected $categoryModel;
    protected $session;
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->categoryModel = new CategoryModel();
    }
    
    protected function sendResponse($data, $statusCode = 200)
    {
        // Always include a fresh CSRF token in the response
        $data['csrf_token'] = csrf_hash();
        $data['csrf_token_name'] = csrf_token();
        
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data)
            ->setHeader('X-CSRF-TOKEN', $data['csrf_token']);
    }
    
    public function index()
    {
        $userId = $this->session->get('id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login first');
        }
        
        $data['categories'] = $this->categoryModel->where('user_id', $userId)->findAll();
        return view('categories/index', $data);
    }
    
    protected function getNextAvailableColor()
    {
        $colors = [
            'blue', 'green', 'purple', 'yellow', 'pink', 'indigo',
            'red', 'teal', 'orange', 'cyan', 'lime', 'violet'
        ];

        // Get all used colors
        $usedColors = $this->categoryModel
            ->where('user_id', $this->session->get('id'))
            ->findAll();
        
        $usedColorCounts = array_count_values(array_column($usedColors, 'color_class'));
        
        // Find the least used color
        $minCount = PHP_INT_MAX;
        $selectedColor = $colors[0];
        
        foreach ($colors as $color) {
            $count = $usedColorCounts[$color] ?? 0;
            if ($count < $minCount) {
                $minCount = $count;
                $selectedColor = $color;
            }
        }
        
        return $selectedColor;
    }
    
    public function create()
    {
        try {
            $userId = $this->session->get('id');
            if (!$userId) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Please login first'
                ], 401);
            }

            // Get and validate the name
            $name = trim($this->request->getPost('name'));
            if (empty($name)) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Category name is required',
                    'errors' => ['name' => 'Category name is required']
                ], 400);
            }

            // Validate name length
            if (strlen($name) < 3) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Category name must be at least 3 characters long',
                    'errors' => ['name' => 'Category name must be at least 3 characters long']
                ], 400);
            }

            if (strlen($name) > 100) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Category name cannot exceed 100 characters',
                    'errors' => ['name' => 'Category name cannot exceed 100 characters']
                ], 400);
            }

            // Check if category already exists for this user
            $existingCategory = $this->categoryModel
                ->where('user_id', $userId)
                ->where('name', $name)
                ->first();

            if ($existingCategory) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'A category with this name already exists',
                    'errors' => ['name' => 'A category with this name already exists']
                ], 400);
            }

            $data = [
                'name' => $name,
                'user_id' => $userId
            ];
            
            if ($this->categoryModel->insert($data)) {
                $categoryId = $this->categoryModel->getInsertID();
                $category = $this->categoryModel->find($categoryId);
                
                return $this->sendResponse([
                    'success' => true,
                    'message' => 'Category created successfully',
                    'category' => $category
                ]);
            }
            
            return $this->sendResponse([
                'success' => false,
                'message' => 'Failed to create category',
                'errors' => $this->categoryModel->errors()
            ], 400);
        } catch (\Exception $e) {
            log_message('error', '[CREATE] Exception in category creation: ' . $e->getMessage());
            log_message('error', '[CREATE] Stack trace: ' . $e->getTraceAsString());
            
            return $this->sendResponse([
                'success' => false,
                'message' => 'An error occurred while creating the category'
            ], 500);
        }
    }
    
    public function update($id)
    {
        try {
            $userId = $this->session->get('id');
            if (!$userId) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Please login first'
                ], 401);
            }

            $category = $this->categoryModel->where('id', $id)->where('user_id', $userId)->first();
            if (!$category) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $name = $this->request->getPost('name');
            if (empty($name)) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Category name is required',
                    'errors' => ['name' => 'Category name is required']
                ], 400);
            }

            // Only update the name, preserve other fields
            $data = ['name' => $name];
            
            if ($this->categoryModel->update($id, $data)) {
                // Get the complete updated category data
                $updatedCategory = $this->categoryModel->find($id);
                return $this->sendResponse([
                    'success' => true,
                    'message' => 'Category updated successfully',
                    'category' => $updatedCategory
                ]);
            }
            
            return $this->sendResponse([
                'success' => false,
                'message' => 'Failed to update category',
                'errors' => $this->categoryModel->errors()
            ], 400);
        } catch (\Exception $e) {
            log_message('error', '[UPDATE] Exception in category update: ' . $e->getMessage());
            
            return $this->sendResponse([
                'success' => false,
                'message' => 'An error occurred while updating the category'
            ], 500);
        }
    }
    
    public function delete($id)
    {
        try {
            $userId = $this->session->get('id');
            if (!$userId) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Please login first'
                ], 401);
            }

            $category = $this->categoryModel->where('id', $id)->where('user_id', $userId)->first();
            if (!$category) {
                return $this->sendResponse([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }
            
            if ($this->categoryModel->delete($id)) {
                return $this->sendResponse([
                    'success' => true,
                    'message' => 'Category deleted successfully'
                ]);
            }
            
            return $this->sendResponse([
                'success' => false,
                'message' => 'Failed to delete category'
            ], 500);
        } catch (\Exception $e) {
            log_message('error', '[DELETE] Exception in category deletion: ' . $e->getMessage());
            
            return $this->sendResponse([
                'success' => false,
                'message' => 'An error occurred while deleting the category'
            ], 500);
        }
    }
} 
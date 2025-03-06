<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\CategoryModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $taskModel = new TaskModel();
        $categoryModel = new CategoryModel();

        $userId = session()->get('id');

        $data = [
            'tasks' => $taskModel->where('user_id', $userId)->findAll(),
            'categories' => $categoryModel->where('user_id', $userId)->findAll(),
            'title' => 'Dashboard'
        ];

        return view('dashboard/index', $data);
    }
}

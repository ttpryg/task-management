<?php

namespace App\Controllers;

class Test extends BaseController
{
    public function index()
    {
        // Check environment variables
        $data = [
            'hostname' => env('database.default.hostname'),
            'database' => env('database.default.database'),
            'username' => env('database.default.username'),
            'port' => env('database.default.port'),
            'db_config' => config('Database')->default
        ];

        // For security, we'll not show the password
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
    }
} 
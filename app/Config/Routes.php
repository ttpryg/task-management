<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');

// Auth routes (no auth filter)
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->get('register', 'Auth::register');
    $routes->post('attemptLogin', 'Auth::attemptLogin');
    $routes->post('attemptRegister', 'Auth::attemptRegister');
    $routes->get('logout', 'Auth::logout');
});

// Protected routes (require authentication)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard routes
    $routes->get('dashboard', 'Dashboard::index');
    
    // Category routes
    $routes->group('categories', function($routes) {
        $routes->get('/', 'Categories::index');
        $routes->post('create', 'Categories::create');
        $routes->post('update/(:num)', 'Categories::update/$1');
        $routes->match(['post', 'delete'], 'delete/(:num)', 'Categories::delete/$1');
    });
    
    // Task management routes
    $routes->group('tasks', function($routes) {
        $routes->get('(:num)', 'Tasks::get/$1');
        $routes->post('create', 'Tasks::create');
        $routes->post('update/(:num)', 'Tasks::update/$1');
        $routes->post('updateStatus/(:num)', 'Tasks::updateStatus/$1');
        $routes->delete('delete/(:num)', 'Tasks::delete/$1');
        $routes->post('delete/(:num)', 'Tasks::delete/$1');
    });
});

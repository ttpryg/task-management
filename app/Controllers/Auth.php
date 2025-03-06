<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Traits\ResponseTrait;
use App\Traits\SessionTrait;
use CodeIgniter\Controller;

/**
 * Auth Controller
 * 
 * Handles all authentication-related operations including:
 * - User login
 * - User registration
 * - Session management
 * - Logout functionality
 */
class Auth extends Controller
{
    use ResponseTrait, SessionTrait;

    /**
     * @var UserModel User model instance
     */
    protected $userModel;

    /**
     * Constructor
     * 
     * Initializes the user model and loads required helpers
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->initSession();
        helper(['form', 'url']);
    }

    /**
     * Default route - redirects to login
     */
    public function index()
    {
        return redirect()->to('/auth/login');
    }

    /**
     * Display login page
     * Clears any existing session for security
     */
    public function login()
    {
        if ($this->isLoggedIn()) {
            $this->clearSession();
        }

        return view('auth/login');
    }

    /**
     * Display registration page
     * Clears any existing session for security
     */
    public function register()
    {
        if ($this->isLoggedIn()) {
            $this->clearSession();
        }

        return view('auth/register');
    }

    /**
     * Handle login attempt
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function attemptLogin()
    {
        $emailOrUsername = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate required fields
        if (empty($emailOrUsername) || empty($password)) {
            return $this->sendErrorResponse(
                empty($emailOrUsername) ? 'Email or username is required' : 'Password is required',
                empty($emailOrUsername) ? 'email' : 'password'
            );
        }

        // Check for test credentials
        if ($this->isTestAccount($emailOrUsername, $password)) {
            return $this->handleTestLogin();
        }

        // Regular user authentication
        return $this->handleUserLogin($emailOrUsername, $password);
    }

    /**
     * Handle user registration
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function attemptRegister()
    {
        $rules = $this->getRegistrationRules();

        if (!$this->validate($rules)) {
            return $this->sendValidationErrorResponse($this->validator->getErrors());
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        if ($this->userModel->insert($userData)) {
            return $this->sendSuccessResponse('Registration successful');
        }

        return $this->sendErrorResponse(
            'Registration failed',
            null,
            $this->userModel->errors(),
            500
        );
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
        $this->clearSession();
        return redirect()->to('/auth/login');
    }

    /**
     * Check if credentials match test account
     * 
     * @param string $emailOrUsername
     * @param string $password
     * @return bool
     */
    private function isTestAccount(string $emailOrUsername, string $password): bool
    {
        return ($emailOrUsername === 'test@example.com' || $emailOrUsername === 'TestUser')
            && $password === 'password123';
    }

    /**
     * Handle test account login
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    private function handleTestLogin()
    {
        $testUser = [
            'id' => 999999,
            'email' => 'test@example.com',
            'username' => 'TestUser'
        ];

        $this->createUserSession($testUser, true);
        return $this->sendSuccessResponse('Login successful');
    }

    /**
     * Handle regular user login
     * 
     * @param string $emailOrUsername
     * @param string $password
     * @return \CodeIgniter\HTTP\Response
     */
    private function handleUserLogin(string $emailOrUsername, string $password)
    {
        $user = $this->userModel
            ->groupStart()
            ->where('email', $emailOrUsername)
            ->orWhere('username', $emailOrUsername)
            ->groupEnd()
            ->first();

        if (!$user) {
            return $this->sendErrorResponse(
                'No account found with this email or username',
                'email',
                [],
                401
            );
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return $this->sendErrorResponse(
                'Incorrect password',
                'password',
                [],
                401
            );
        }

        $this->createUserSession($user);
        return $this->sendSuccessResponse('Login successful');
    }

    /**
     * Get validation rules for registration
     * 
     * @return array
     */
    private function getRegistrationRules(): array
    {
        return [
            'username' => [
                'rules' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters long',
                    'max_length' => 'Username cannot exceed 50 characters',
                    'is_unique' => 'This username is already taken'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Please enter a valid email address',
                    'is_unique' => 'This email is already registered'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must be at least 6 characters long'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Please confirm your password',
                    'matches' => 'Passwords do not match'
                ]
            ]
        ];
    }
}

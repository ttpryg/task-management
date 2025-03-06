<?php

namespace App\Traits;

/**
 * Session Trait
 * 
 * Provides standardized methods for session management across controllers
 */
trait SessionTrait
{
    /**
     * @var \Config\Session Session configuration
     */
    protected $sessionConfig;

    /**
     * Initialize session configuration
     */
    protected function initSession()
    {
        $this->sessionConfig = config('Session');
    }

    /**
     * Clear session and session cookie
     */
    protected function clearSession()
    {
        session()->destroy();
        
        if (isset($_COOKIE[$this->sessionConfig->cookieName])) {
            setcookie(
                $this->sessionConfig->cookieName,
                '',
                time() - 3600,
                $this->sessionConfig->cookiePath,
                $this->sessionConfig->cookieDomain,
                $this->sessionConfig->cookieSecure,
                $this->sessionConfig->cookieHTTPOnly
            );
        }
    }

    /**
     * Create a new session with user data
     * 
     * @param array $userData User data to store in session
     * @param bool $isTestUser Whether this is a test user session
     */
    protected function createUserSession(array $userData, bool $isTestUser = false)
    {
        $this->clearSession();
        session()->start();
        
        $sessionData = [
            'id' => $userData['id'],
            'email' => $userData['email'],
            'username' => $userData['username'],
            'isLoggedIn' => true,
            'isTestUser' => $isTestUser,
            'last_activity' => time()
        ];

        session()->set($sessionData);
        
        if (!$isTestUser) {
            session()->regenerate(true);
        }
    }

    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    /**
     * Get current user ID from session
     * 
     * @return int|null
     */
    protected function getCurrentUserId(): ?int
    {
        return session()->get('id');
    }

    /**
     * Update last activity timestamp
     */
    protected function updateLastActivity()
    {
        session()->set('last_activity', time());
    }
} 
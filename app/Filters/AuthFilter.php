<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Authentication Filter
 * 
 * Handles authentication and session management for protected routes.
 * This filter ensures that:
 * 1. Users are logged in to access protected routes
 * 2. Sessions are valid and not expired
 * 3. Test users are restricted from certain actions
 */
class AuthFilter implements FilterInterface
{
    /**
     * Session expiration time in seconds (2 hours)
     */
    private const SESSION_EXPIRATION = 7200;

    /**
     * List of paths that test users cannot access
     */
    private const RESTRICTED_PATHS = [
        'users/delete',
        'users/update'
    ];

    /**
     * Pre-route authentication check
     * 
     * @param RequestInterface $request The current request
     * @param array|null $arguments Additional arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the current session instance
        $session = session();

        // Check if user is logged in
        if (!$this->isUserLoggedIn($session)) {
            return $this->redirectToLogin('Please login first');
        }

        // Check session expiration
        if ($this->isSessionExpired($session)) {
            return $this->handleExpiredSession($session);
        }

        // Check if user ID exists in session
        if (!$this->isValidUserSession($session)) {
            return $this->redirectToLogin('Invalid session. Please login again.');
        }

        // Check test user restrictions
        if ($this->isRestrictedTestUserAccess($session, $request)) {
            return redirect()->back()->with('error', 'This action is not allowed for test users.');
        }

        // Update last activity time
        $this->updateLastActivity($session);
    }

    /**
     * Post-route processing (not used)
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Check if the user is logged in
     */
    private function isUserLoggedIn($session): bool
    {
        return (bool) $session->get('isLoggedIn');
    }

    /**
     * Check if the session has expired
     */
    private function isSessionExpired($session): bool
    {
        $lastActivity = $session->get('last_activity');
        return $lastActivity && (time() - $lastActivity > self::SESSION_EXPIRATION);
    }

    /**
     * Check if the user session is valid
     */
    private function isValidUserSession($session): bool
    {
        return (bool) $session->get('id');
    }

    /**
     * Check if test user is trying to access restricted paths
     */
    private function isRestrictedTestUserAccess($session, $request): bool
    {
        if (!$session->get('isTestUser')) {
            return false;
        }

        $currentPath = $request->uri->getPath();
        foreach (self::RESTRICTED_PATHS as $path) {
            if (strpos($currentPath, $path) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Handle expired session cleanup and redirect
     */
    private function handleExpiredSession($session)
    {
        $this->clearSession($session);
        return $this->redirectToLogin('Session expired. Please login again.');
    }

    /**
     * Clear session and session cookie
     */
    private function clearSession($session)
    {
            $session->destroy();

            if (isset($_COOKIE[$session->config->cookieName])) {
                setcookie(
                    $session->config->cookieName,
                    '',
                    time() - 3600,
                    $session->config->cookiePath,
                    $session->config->cookieDomain,
                    $session->config->cookieSecure,
                    $session->config->cookieHTTPOnly
                );
            }
    }

    /**
     * Update the last activity timestamp
     */
    private function updateLastActivity($session)
    {
        $session->set('last_activity', time());
    }

    /**
     * Redirect to login page with error message
     */
    private function redirectToLogin(string $message)
    {
        return redirect()->to('/auth/login')->with('error', $message);
    }
}

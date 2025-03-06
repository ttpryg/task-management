<?php

namespace App\Traits;

/**
 * Response Trait
 * 
 * Provides standardized methods for sending JSON responses across controllers
 */
trait ResponseTrait
{
    /**
     * Add security headers to response
     * 
     * @param \CodeIgniter\HTTP\Response $response
     * @return \CodeIgniter\HTTP\Response
     */
    protected function addSecurityHeaders($response)
    {
        return $response
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'SAMEORIGIN')
            ->setHeader('X-XSS-Protection', '1; mode=block')
            ->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }

    /**
     * Send a success response
     * 
     * @param string $message Success message
     * @param array $data Additional data to include in response
     * @param int $statusCode HTTP status code
     * @return \CodeIgniter\HTTP\Response
     */
    protected function sendSuccessResponse(string $message, array $data = [], int $statusCode = 200)
    {
        $response = $this->response->setJSON(array_merge([
            'success' => true,
            'message' => $message,
            'csrf_token' => csrf_hash(),
            'timestamp' => time()
        ], $data))->setStatusCode($statusCode);

        return $this->addSecurityHeaders($response);
    }

    /**
     * Send an error response
     * 
     * @param string $message Error message
     * @param string|null $field Field that caused the error
     * @param array $errors Validation errors
     * @param int $statusCode HTTP status code
     * @return \CodeIgniter\HTTP\Response
     */
    protected function sendErrorResponse(string $message, ?string $field = null, array $errors = [], int $statusCode = 400)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'csrf_token' => csrf_hash(),
            'timestamp' => time()
        ];

        if ($field) {
            $response['field'] = $field;
        }

        if ($errors) {
            $response['errors'] = $errors;
        }

        // Add request ID for error tracking
        $response['request_id'] = uniqid('err_', true);

        $response = $this->response->setJSON($response)->setStatusCode($statusCode);
        return $this->addSecurityHeaders($response);
    }

    /**
     * Send a validation error response
     * 
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return \CodeIgniter\HTTP\Response
     */
    protected function sendValidationErrorResponse(array $errors, string $message = 'Validation failed')
    {
        return $this->sendErrorResponse($message, null, $errors, 400);
    }

    /**
     * Send a not found response
     * 
     * @param string $message Error message
     * @param string $resource Resource type that was not found
     * @return \CodeIgniter\HTTP\Response
     */
    protected function sendNotFoundResponse(string $message = 'Resource not found', string $resource = '')
    {
        $response = [
            'success' => false,
            'message' => $message,
            'resource' => $resource,
            'csrf_token' => csrf_hash(),
            'timestamp' => time()
        ];

        $response = $this->response->setJSON($response)->setStatusCode(404);
        return $this->addSecurityHeaders($response);
    }

    /**
     * Send an unauthorized response
     * 
     * @param string $message Error message
     * @return \CodeIgniter\HTTP\Response
     */
    protected function sendUnauthorizedResponse(string $message = 'Unauthorized access')
    {
        $response = [
            'success' => false,
            'message' => $message,
            'csrf_token' => csrf_hash(),
            'timestamp' => time()
        ];

        $response = $this->response->setJSON($response)->setStatusCode(401);
        return $this->addSecurityHeaders($response);
    }
} 
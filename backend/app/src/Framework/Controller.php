<?php

namespace App\Framework;

/**
 * Base controller class
 * Contains helper methods used by all controllers
 */
class Controller
{
    public function __construct()
    {
    }

    /**
     * Reads and parses the JSON request body
     * Returns null if the body is missing or invalid
     *
     * @return array<string, mixed>|null
     */
    protected function getJsonBody(): ?array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input ?? '', true);

        return is_array($data) ? $data : null;
    }

    /**
     * Extracts the Bearer token from the Authorization header
     * Returns null if no token is found
     *
     * @return string|null
     */
    protected function getBearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (stripos($header, 'Bearer ') !== 0) {
            return null;
        }

        $token = trim(substr($header, 7));
        return $token !== '' ? $token : null;
    }

    /**
     * Sanitizes user input to prevent XSS / script injection
     * Uses htmlspecialchars() to convert special characters like <, >, &, ", '
     * to their HTML entity equivalents before storing or returning data
     *
     * @param string $value - raw input from the user
     * @return string - sanitized string safe for storage and output
     */
    protected function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sends a JSON success response with the given data and HTTP status code
     *
     * @param mixed $data
     * @param int $code - HTTP status code (default 200)
     */
    protected function sendSuccessResponse($data = [], $code = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Sends a JSON error response with an error message and HTTP status code
     *
     * @param string $message - error description
     * @param int $code - HTTP status code (default 500)
     */
    protected function sendErrorResponse(string $message, int $code = 500): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode(['error' => $message], JSON_PRETTY_PRINT);
    }

    /**
     * Maps POST data (JSON) to an instance of the specified class
     * Only sets properties that actually exist on the class to avoid mass-assignment issues
     *
     * @param string $className - the fully qualified class name to hydrate
     * @return object|null - populated instance or null if JSON was invalid
     */
    protected function mapPostDataToClass(string $className): ?object
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input ?? '', true);

        if (!is_array($data)) {
            $this->sendErrorResponse('Invalid JSON body', 400);
            return null;
        }

        $instance = new $className();

        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }
}

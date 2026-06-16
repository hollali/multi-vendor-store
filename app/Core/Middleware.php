<?php
namespace App\Core;

class Middleware
{
    private static function url(string $path): string
    {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        return rtrim($basePath, '/') . '/' . ltrim($path, '/');
    }
    public static function auth()
    {
        $session = Session::getInstance();
        $session->start();
        if (!$session->isAuthenticated()) {
            header('Content-Type: application/json');
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthenticated']);
                exit;
            }
            header('Location: ' . self::url('/login'));
            exit;
        }
        return true;
    }

    public static function guest()
    {
        $session = Session::getInstance();
        $session->start();
        if ($session->isAuthenticated()) {
            header('Location: ' . self::url('/dashboard'));
            exit;
        }
        return true;
    }

    public static function admin()
    {
        $session = Session::getInstance();
        $session->start();
        if (!$session->isAuthenticated()) {
            header('Location: ' . self::url('/login'));
            exit;
        }
        if ($session->getUserRole() !== 'admin') {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }
            header('Location: ' . self::url('/dashboard'));
            exit;
        }
        return true;
    }

    public static function vendor()
    {
        $session = Session::getInstance();
        $session->start();
        if (!$session->isAuthenticated()) {
            header('Location: ' . self::url('/login'));
            exit;
        }
        if ($session->getUserRole() !== 'vendor') {
            header('Location: ' . self::url('/dashboard'));
            exit;
        }
        return true;
    }

    public static function customer()
    {
        $session = Session::getInstance();
        $session->start();
        if (!$session->isAuthenticated()) {
            header('Location: ' . self::url('/login'));
            exit;
        }
        if (!in_array($session->getUserRole(), ['customer'])) {
            header('Location: ' . self::url('/dashboard'));
            exit;
        }
        return true;
    }

    public static function csrf()
    {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!self::validateCsrfToken($token)) {
            http_response_code(419);
            echo json_encode(['error' => 'CSRF token mismatch']);
            exit;
        }
        return true;
    }

    public static function generateCsrfToken(): string
    {
        $session = Session::getInstance();
        $session->start();
        if (!$session->has('_csrf_token')) {
            $token = bin2hex(random_bytes(32));
            $session->set('_csrf_token', $token);
        }
        return $session->get('_csrf_token');
    }

    public static function validateCsrfToken(string $token): bool
    {
        $session = Session::getInstance();
        $session->start();
        $stored = $session->get('_csrf_token');
        return $stored && hash_equals($stored, $token);
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . self::generateCsrfToken() . '">';
    }

    public static function rateLimit(string $key, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        $session = Session::getInstance();
        $attempts = (int)$session->get("rate_limit_{$key}", 0);
        $lastAttempt = (int)$session->get("rate_limit_{$key}_time", 0);

        if ($attempts >= $maxAttempts) {
            if (time() - $lastAttempt < $decayMinutes * 60) {
                return false;
            }
            $session->remove("rate_limit_{$key}");
            $session->remove("rate_limit_{$key}_time");
        }

        return true;
    }

    public static function incrementRateLimit(string $key): void
    {
        $session = Session::getInstance();
        $attempts = (int)$session->get("rate_limit_{$key}", 0);
        $session->set("rate_limit_{$key}", $attempts + 1);
        $session->set("rate_limit_{$key}_time", time());
    }

    public static function clearRateLimit(string $key): void
    {
        $session = Session::getInstance();
        $session->remove("rate_limit_{$key}");
        $session->remove("rate_limit_{$key}_time");
    }
}

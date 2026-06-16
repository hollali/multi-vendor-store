<?php
namespace App\Core;

class Controller
{
    protected Session $session;
    protected Validator $validator;
    protected array|\stdClass|null $currentUser = null;

    public function __construct()
    {
        $this->session = Session::getInstance();
        $this->validator = new Validator();
        $this->currentUser = $this->session->get('user_data');
    }

    protected function renderView(string $view, array $data = []): void
    {
        $data['user'] = $this->currentUser;
        $data = $this->normalizeData($data);
        $data['session'] = $this->session;
        $data['settings'] = $this->getSettings();
        $data['csrf_field'] = function () {
            return '<input type="hidden" name="_csrf_token" value="' . Middleware::generateCsrfToken() . '">';
        };
        $data['csrf_meta'] = function () {
            return '<meta name="csrf-token" content="' . Middleware::generateCsrfToken() . '">';
        };
        $data['flash'] = function () {
            $messages = [];
            foreach (['success', 'error', 'warning', 'info'] as $type) {
                if ($this->session->hasFlash($type)) {
                    $messages[$type] = $this->session->getFlash($type);
                }
            }
            return $messages;
        };
        $data['currency_symbol'] = 'GH₵';
        $data['site_name'] = 'Celer Market';

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $data['baseUrl'] = $basePath;
        $data['url'] = function ($path) use ($basePath) {
            return $basePath . '/' . ltrim($path, '/');
        };

        extract($data);
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        ob_start();
        require $viewPath;
        $html = ob_get_clean();

        if ($basePath && $basePath !== '') {
            $html = preg_replace_callback(
                '#(href|action|src)="(/[^"]*)"#',
                function ($m) use ($basePath) {
                    $path = $m[2];
                    if (str_starts_with($path, $basePath . '/') || $path === $basePath) return $m[0];
                    if (preg_match('~^(https?:|//|#|javascript:|mailto:)~', $path)) return $m[0];
                    return $m[1] . '="' . $basePath . $path . '"';
                },
                $html
            );
        }
        echo $html;
    }

    protected function renderJSON($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($basePath !== '' && !str_starts_with($url, 'http') && !str_starts_with($url, '//')) {
            $url = $basePath . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url);
        exit;
    }

    protected function redirectWith(string $url, string $message, string $type = 'success'): void
    {
        $this->session->setFlash($type, $message);
        $this->redirect($url);
    }

    protected function getRequestBody(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return $data ?? [];
    }

    protected function getSettings(): array
    {
        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll("SELECT `key`, `value` FROM settings");
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row->key] = $row->value;
            }
            return $settings;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $_POST[$key] ?? $default;
    }

    protected function formatPrice($amount): string
    {
        return 'GH₵' . number_format((float)$amount, 2);
    }

    private function normalizeData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if ($value instanceof \stdClass) {
                $result[$key] = (array)$value;
            } elseif (is_array($value)) {
                $result[$key] = $this->normalizeArray($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function normalizeArray(array $arr): array
    {
        $result = [];
        foreach ($arr as $key => $value) {
            if ($value instanceof \stdClass) {
                $result[$key] = (array)$value;
            } elseif (is_array($value)) {
                $result[$key] = $this->normalizeArray($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

}

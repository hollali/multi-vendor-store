<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $uri, $handler, ?string $middleware = null): void
    {
        $this->addRoute('GET', $uri, $handler, $middleware);
    }

    public function post(string $uri, $handler, ?string $middleware = null): void
    {
        $this->addRoute('POST', $uri, $handler, $middleware);
    }

    public function put(string $uri, $handler, ?string $middleware = null): void
    {
        $this->addRoute('PUT', $uri, $handler, $middleware);
    }

    public function delete(string $uri, $handler, ?string $middleware = null): void
    {
        $this->addRoute('DELETE', $uri, $handler, $middleware);
    }

    private function addRoute(string $method, string $uri, $handler, ?string $middleware): void
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $uri);
        $pattern = '#^' . $pattern . '$#';
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
            'uri' => $uri,
        ];
    }

    public function resolve(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if ($route['middleware']) {
                    $middlewareClass = "App\\Core\\Middleware";
                    $middlewareMethod = $route['middleware'];
                    if (method_exists($middlewareClass, $middlewareMethod)) {
                        $result = $middlewareClass::$middlewareMethod();
                        if ($result !== true) {
                            if (is_string($result)) {
                                header('Location: ' . $result);
                                exit;
                            }
                            return;
                        }
                    }
                }

                $this->dispatch($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found', 'uri' => $uri]);
        exit;
    }

    private function dispatch($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, ...array_values($params));
            return;
        }

        if (is_string($handler)) {
            [$controller, $action] = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";
            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "Controller {$controllerClass} not found";
                exit;
            }
            $instance = new $controllerClass();
            if (!method_exists($instance, $action)) {
                http_response_code(500);
                echo "Action {$action} not found in {$controllerClass}";
                exit;
            }
            call_user_func_array([$instance, $action], array_values($params));
        }
    }
}

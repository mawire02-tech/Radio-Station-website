<?php
/**
 * Router — maps URL segments to controller actions
 * No framework dependency; pure PHP.
 */
class Router
{
    private array $routes = [];

    /** Register a route: GET, POST, or ANY */
    public function add(string $method, string $pattern, callable|array $handler): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler');
    }

    public function get(string $pattern, callable|array $handler): void  { $this->add('GET',  $pattern, $handler); }
    public function post(string $pattern, callable|array $handler): void { $this->add('POST', $pattern, $handler); }
    public function any(string $pattern, callable|array $handler): void  { $this->add('ANY',  $pattern, $handler); }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip base path (e.g. /wavefm/) from URI
        $basePath = rtrim(parse_url(BASE_URL, PHP_URL_PATH), '/');
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . ltrim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) continue;

            // Convert :param placeholders to named regex groups
            $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $route['pattern']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Resolve handler
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$controllerClass, $actionMethod] = $handler;
                    $controller = new $controllerClass();
                    $action     = $actionMethod;
                    call_user_func_array([$controller, $action], array_values($params));
                } else {
                    call_user_func_array($handler, array_values($params));
                }
                return;
            }
        }

        // 404
        http_response_code(404);
        require APP_PATH . 'views/errors/404.php';
    }
}

<?php

namespace Sources;

class Router
{
    private array $handlers;
    private $notFoundHandler;

    private const METHOD_POST = 'POST';
    private const METHOD_GET = 'GET';
    private const METHOD_PUT = 'PUT';
    private const METHOD_DELETE = 'DELETE';

    public function get(string $path, $handler): void
    {
        $this->addHandler(self::METHOD_GET, $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->addHandler(self::METHOD_POST, $path, $handler);
    }

    public function put(string $path, $handler): void
    {
        $this->addHandler(self::METHOD_PUT, $path, $handler);
    }

    public function delete(string $path, $handler): void
    {
        $this->addHandler(self::METHOD_DELETE, $path, $handler);
    }

    public function addNotFoundHandler($handler): void
    {
        $this->notFoundHandler = $handler;
    }

    private function addHandler(string $method, string $path, $handler): void
    {
        $this->handlers[] = [
            'path' => $path,
            'method' => $method,
            'handler' => $handler,
        ];
    }

    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = str_replace('/v1', '', $requestUri['path']);

        $method = $_SERVER['REQUEST_METHOD'];
        $callback = null;

        foreach ($this->handlers as $handler) {
            if ($this->matchPath($handler['path'], $requestPath) && $method === $handler['method']) {
                $callback = $handler['handler'];
                break;
            }
        }

        if (!$callback) {
            if (!empty($this->notFoundHandler)) {
                $callback = $this->notFoundHandler;
            } else {
                $callback = function () {
                    header("HTTP/1.0 404 Not Found");
                    echo "Invalid request";
                };
            }
        }

        call_user_func_array($callback, [
            array_merge($_GET, $_POST)
        ]);
    }

    private function matchPath(string $pattern, string $path): bool
    {
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = preg_replace('/{(.*?)}/', '(?<$1>[^\/]+)', $pattern);
        $pattern = '/^' . $pattern . '\/?$/';
    
        $matches = [];
        $result = preg_match($pattern, $path, $matches);
    
        if ($result) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $_GET[$key] = $value;
                }
            }
        }
    
        return (bool) $result;
    }
}
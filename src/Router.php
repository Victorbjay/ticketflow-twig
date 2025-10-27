<?php

namespace ResolveHub;

class Router {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove /public from path if present
        $path = str_replace('/public', '', $path);
        
        // Normalize path
        $path = $path === '' ? '/' : $path;
        
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }
        
        // 404
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
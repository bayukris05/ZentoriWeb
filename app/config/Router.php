<?php

namespace App\config;

use App\middleware\AuthMiddleware;

class Router
{
    private static $routes = [];

    public static function add($method, $path, $controller, $function = null, $middleware = null)
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'function' => $function,
            'middleware' => $middleware
        ];
    }

    public static function run()
    {
        $path = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            $pattern = "#^" . $route['path'] . "$#";
            
            if (preg_match($pattern, $path, $variables) && $route['method'] == $method) {
                
                if ($route['middleware']) {
                    call_user_func($route['middleware']);
                }

                array_shift($variables);
                
                if ($route['controller'] instanceof \Closure) {
                    call_user_func_array($route['controller'], $variables);
                } else {
                    $controller = new $route['controller'];
                    $function = $route['function'];
                    call_user_func_array([$controller, $function], $variables);
                }
                return;
            }
        }
        http_response_code(404);
        echo "404 - Page not found";
    }
}
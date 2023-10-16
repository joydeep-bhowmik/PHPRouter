<?php
namespace JoydeepBhowmik\PHPRouter;

class Router
{
    public $routes = [];
    public $baseUrl;
    public static $view;

    public function baseUlr($url)
    {
        $this->baseUrl = $url;
    }
    public function addRoute($pattern, $callback, $method = 'get')
    {
        if ($pattern != '*' && !str_starts_with($pattern, '/')) {
            return;
        }
        $this->routes[$pattern] = [
            'pattern' => $pattern,
            'name' => null,
            'callback' => $callback,
            'method' => strtoupper($method),
        ];

        return $this;
    }

    public function get($pattern, $callback)
    {
        return $this->addRoute($pattern, $callback, $method = 'get');
    }

    public function post($pattern, $callback)
    {
        return $this->addRoute($pattern, $callback, $method = 'post');
    }

    public function put($pattern, $callback)
    {
        return $this->addRoute($pattern, $callback, $method = 'put');
    }

    public function delete($pattern, $callback)
    {
        return $this->addRoute($pattern, $callback, $method = 'delete');
    }

    public function handleRequest($requestUri, $method)
    {
        foreach ($this->routes as $key => $value) {
            $regex = '#^' . preg_replace('/\{(\w+)\}/', '([^/]+)', $key) . '$#';

            if ($method == $value['method'] && ($regex != '#^*$#' && preg_match($regex, $requestUri, $matches))) {
                array_shift($matches); // Remove the first match (full URL)
                $this->handleCallback($value['callback'], $matches);
                return;
            }
        }
        call_user_func($this->routes['*']['callback']);
    }

    public function handleCallback($callback, $params = null)
    {
        switch (gettype($callback)) {
            case 'array':
                $class = new $callback[0]();
                self::$view = $class->{$callback[1]}(...$params);
                break;
            case 'object':
                self::$view = call_user_func_array($callback, $params);
                break;
        }
    }
    public function name($text)
    {
        $this->routes[array_key_last($this->routes)]['name'] = $text;
    }

    public function middleware($middleware, $callback)
    {
        $class = new $middleware();
        if ($class->handle()) {
            return call_user_func($callback);
        }
    }

    public function dispatch()
    {
        if ($this->baseUrl) {
            if (!str_starts_with($this->baseUrl, '/')) {
                throw new Exception(' baseUrl must starts with a slash (/)');
            }
            if (str_ends_with($this->baseUrl, '/')) {
                throw new Exception(' baseUrl must not ends with a slash (/)');
            }
        }
        $requestUri = str_replace($this->baseUrl, "", $_SERVER['REQUEST_URI']);

        $method = $_SERVER['REQUEST_METHOD'];
        $this->handleRequest($requestUri, $method);
    }
}

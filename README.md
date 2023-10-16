# PHPRouter
A simple and efficient PHP routing library.

## Usage
### Setup
1. Make sure you have a index.php file in your root directory.
Put this `.htaccess` file in your root directory.

```apacheconf
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L]
```
2. Composer command
```console
composer require joydeep-bhowmik/php-router
```
### Basic
in your index.php file
```PHP
require __DIR__ . '/vendor/autoload.php';
use JoydeepBhowmik\PHPRouter\Router;
$router = new Router();
$router->get('/', function () {
    return 'Home';
});
$router->dispatch();
echo Router::$view;
```
### get, post, put, delete , any
```PHP

$router->get('/', function () {
    return 'get request';
});
$router->post('/', function () {
    return 'post request';
});
$router->put('/', function () {
    return 'put request';
});
$router->delete('/', function () {
    return 'delete request';
});
$router->any('/', function () {
    return 'anytype of request';
});
$router->dispatch();
```
### Wildcard route
```PHP
$router->get('*', function () {
    return '404 not found';
});
```
## Parameters
```PHP
$router->get('/profile/{user}/{id}', function ($user, $id) {
    return 'Username  ' . $user . ' and user id is ' . $id;
});
```

## Advance

### Change base url
```PHP
$router->baseUrl('/shop');
```
### Calling a controller method
```PHP
$router->get('/profile/{user}/{id}', [ExampleController::class, 'index']);
```

### Add different request method
```PHP
$router->addRoute('profile/{user}/{id}', function () {
    return 'Hello';
}, 'METHOD_NAME');
```
### Middleware

```PHP
class Example_middleware
{
    public function handle()
    {
        return true;
    }
}

$router->middleware(Example_middleware::class, function () use ($router) {

    $router->get('/', function () {
        return 'Home';
    });

});
$router->dispatch()

```

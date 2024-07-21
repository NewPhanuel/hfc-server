<?php
declare(strict_types=1);

namespace DevPhanuel\Core;

use DevPhanuel\Core\Middleware\Authorize;
use PH7\JustHttp\StatusCode;
use Throwable;

class Router
{
    protected array $routes = [];


    /**
     * Adds a route to the array from the http verbs methods
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function registerRoute(string $method, string $uri, string $action, array $middleware = []): void
    {
        list($controller, $controllerMethod) = explode('@', $action);
        $this->routes[] = [
            "method" => $method,
            "uri" => $uri,
            "controller" => $controller,
            "controllerMethod" => $controllerMethod,
            "middleware" => $middleware,
        ];
    }

    /**
     * Adds a GET route
     *
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function get(string $uri, string $action, array $middleware = []): void
    {
        $this->registerRoute("GET", $uri, $action, $middleware);
    }

    /**
     * Adds a POST route
     *
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function post(string $uri, string $action, array $middleware = []): void
    {
        $this->registerRoute("POST", $uri, $action, $middleware);
    }

    /**
     * Adds a PUT route
     *
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function put(string $uri, string $action, array $middleware = []): void
    {
        $this->registerRoute("PUT", $uri, $action, $middleware);
    }

    /**
     * Adds a DELETE route
     *
     * @param string $uri
     * @param string $action
     * @return void
     */
    public function delete(string $uri, string $action, array $middleware = []): void
    {
        $this->registerRoute("DELETE", $uri, $action, $middleware);
    }

    /**
     * Routes the request (Calls the controller if the uri exists)
     *
     * @param string $uri
     * @param string $method
     * @return void
     */
    public function route(string $uri): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        $data = file_get_contents('php://input');
        $data = json_decode($data);


        foreach ($this->routes as $route) {

            // Split the current uri into segments
            $uriSegments = explode('/', trim($uri, '/'));

            // Split the current route method
            $routeSegments = explode('/', trim($route['uri'], '/'));

            $match = true;

            // Check if number of segment matches
            if (count($uriSegments) === count($routeSegments) && (strtoupper($route['method']) === strtoupper($requestMethod))) {
                $match = true;
                $params = [];

                // If the url does not match and there is no param
                for ($i = 0; $i < count($routeSegments); $i++) {
                    if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }

                    // Check for the param and add it to the $params array
                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }

                if ($match) {
                    foreach ($route['middleware'] as $middleware) {
                        (new Authorize())->handle($middleware);
                    }

                    $params['data'] = $data;
                    $controller = 'DevPhanuel\\Controllers\\' . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];

                    // Instantiate and call the method
                    $controllerInstance = new $controller();

                    // try {
                    //     $controllerInstance->$controllerMethod($params);
                    // } catch (Throwable $e) {
                    //     response(StatusCode::BAD_REQUEST, errorMessage(get_class($e), $e->getMessage(), $e->getCode()));
                    // }
                    $controllerInstance->$controllerMethod($params);
                    return;
                }
            }
        }

        response(StatusCode::NOT_FOUND, errorMessage('Not Found', 'The resource was not found on the server', 404));
    }
}

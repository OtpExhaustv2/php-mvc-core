<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use App\Core\Http\Request;
use App\Core\Http\Response;

class Router
{

    public Request $request;
    public Response $response;
    protected array $routes = [];

    /**
     * Router constructor.
     *
     * @param \App\Core\Http\Request  $request
     * @param \App\Core\Http\Response $response
     */
    public function __construct (Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param string $path
     * @param mixed  $callback
     */
    public function get (string $path, $callback)
    {
        $this->routes["get"][$path] = $callback;
    }

    /**
     * @param string $path
     * @param mixed  $callback
     */
    public function post (string $path, $callback)
    {
        $this->routes["post"][$path] = $callback;
    }

    /**
     * @return mixed|string|string[]
     * @throws \App\Core\Exceptions\NotFoundException
     */
    public function resolve ()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        if (!$callback)
        {
            throw new NotFoundException();
        }

        if (is_string($callback))
        {
            return App::$app->view->renderView($callback);
        }

        if (is_array($callback))
        {
            /** @var $controller \App\Core\Controller */
            $controller = new $callback[0]();
            App::$app->controller = $controller;
            $controller->action = $callback[1];
            
            foreach ($controller->getMiddlewares() as $middleware)
            {
                $middleware->execute();
            }

            $callback[0] = $controller;
        }


        return call_user_func($callback, $this->request, $this->response);
    }

}

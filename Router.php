<?php

namespace Svv\Framework;

use Svv\Framework\Exceptions\NotFoundException;
use Svv\Framework\Http\Request;
use Svv\Framework\Http\Response;

class Router
{

    public Request $request;
    public Response $response;
    protected array $routes = [];

    /**
     * Router constructor.
     *
     * @param \Svv\Framework\Http\Request  $request
     * @param \Svv\Framework\Http\Response $response
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
     * @throws \Svv\Framework\Exceptions\NotFoundException
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
            /** @var $controller \Svv\Framework\Controller */
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

<?php

namespace App\Core;

use App\Core\Middlewares\BaseMiddleware;

class Controller
{
    public string $layout = "main";
    public string $action = "";

    /**
     * @var \App\Core\Middlewares\BaseMiddleware[]
     */
    protected array $middlewares = [];

    /**
     * Return the view with the params
     *
     * @param       $view
     * @param array $params
     * @return string|string[]
     */
    public function render ($view, array $params = [])
    {
        return App::$app->view->renderView($view, $params);
    }

    /**
     * Set the layout of the page
     *
     * @param $layout
     */
    public function setLayout ($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Redirect to another page
     *
     * @param string $url
     */
    public function redirect (string $url)
    {
        return App::$app->response->redirect($url);
    }

    /**
     * Return the Session class
     *
     * @return \App\Core\Session
     */
    public function session (): Session
    {
        return App::$app->session;
    }

    /**
     * Return the logged-in user
     *
     * @return \App\Core\UserModel|null
     */
    public function getUser ()
    {
        return App::$app->user;
    }

    /**
     * Add a middleware to the array
     *
     * @param \App\Core\Middlewares\BaseMiddleware $middleware
     */
    public function registerMiddleware (BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return \App\Core\Middlewares\BaseMiddleware[]
     */
    public function getMiddlewares (): array
    {
        return $this->middlewares;
    }

}

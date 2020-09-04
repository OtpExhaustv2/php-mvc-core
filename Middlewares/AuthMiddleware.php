<?php

namespace App\Core\Middlewares;

use App\Core\App;
use App\Core\Exceptions\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{

    public array $actions = [];

    /**
     * AuthMiddleware constructor.
     *
     * @param array $actions
     */
    public function __construct (array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @throws \App\Core\Exceptions\ForbiddenException
     */
    public function execute ()
    {
        if (App::isGuest())
        {
            if (empty($this->actions) || in_array(App::$app->controller->action, $this->actions))
            {
                throw new ForbiddenException();
            }
        }
    }

}

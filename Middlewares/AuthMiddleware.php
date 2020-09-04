<?php

namespace Svv\Framework\Middlewares;

use Svv\Framework\App;
use Svv\Framework\Exceptions\ForbiddenException;

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
     * @throws \Svv\Framework\Exceptions\ForbiddenException
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

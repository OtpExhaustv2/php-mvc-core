<?php

namespace Svv\Framework;

use Svv\Framework\Database\Database;
use Svv\Framework\Http\Response;
use Svv\Framework\Http\Request;

/**
 * Class App
 *
 * @package Svv\Framework
 */
class App
{

    public static string $ROOT_DIR;
    public static App $app;
    public string $layout = "main";
    public string $userClass;
    public Router $router;
    public Database $db;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Session $session;
    public ?UserModel $user;
    public View $view;

    public function __construct (string $rootPath, array $config)
    {
        $this->userClass = $config["userClass"];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->response = new Response();
        $this->request = new Request();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        $this->db = new Database($config["db"]);

        $primaryValue = $this->session->get("user");
        if ($primaryValue)
        {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }
        else
        {
            $this->user = null;
        }
    }

    /**
     * @throws \Exception
     */
    public function run ()
    {
        try
        {
            echo $this->router->resolve();
        } catch (\Exception $e)
        {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView("error/_error", [
                "exception" => $e,
            ]);
        }
    }

    /**
     * @return \Svv\Framework\Controller
     */
    public function getController (): Controller
    {
        return $this->controller;
    }

    /**
     * @param \Svv\Framework\Controller $controller
     */
    public function setController (Controller $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @param \Svv\Framework\UserModel $user
     * @return bool
     */
    public function login (UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set("user", $primaryValue);

        return true;
    }

    public function logout ()
    {
        $this->user = null;
        $this->session->remove("user");
    }

    public function isGuest (): bool
    {
        return !self::$app->user;
    }
}

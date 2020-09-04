<?php

namespace App\Core;

class View
{

    public string $title = "";

    /**
     * @param       $view
     * @param array $params
     * @return string|string[]
     */
    public function renderView ($view, array $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace("{{ content }}", $viewContent, $layoutContent);
    }

    /**
     * Return the content
     *
     * @param $viewContent
     * @return string|string[]
     */
    public function renderContent ($viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace("{{ content }}", $viewContent, $layoutContent);
    }

    /**
     * @return false|string
     */
    protected function layoutContent ()
    {
        $layout = App::$app->layout;
        if (App::$app->controller)
        {
            $layout = App::$app->controller->layout;
        }
        ob_start();
        include_once App::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    /**
     * @param       $view
     * @param array $params
     * @return false|string
     */
    protected function renderOnlyView ($view, array $params = [])
    {
        foreach ($params as $key => $value)
        {
            $$key = $value;
        }
        ob_start();
        include_once App::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

}

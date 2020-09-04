<?php

namespace App\Core\Http;

class Request
{

    /**
     * Return the path for the router
     *
     * @return false|mixed|string
     */
    public function getPath ()
    {
        $path = $_SERVER['REQUEST_URI'] ?? "/";
        $position = strpos($path, "?");

        if (!$position)
        {
            return $path;
        }

        return substr($path, 0, $position);
    }

    /**
     * Return the method of the page
     *
     * @return string
     */
    public function method ()
    {
        return strtolower($_SERVER["REQUEST_METHOD"]);
    }

    /**
     * @return bool
     */
    public function isGet ()
    {
        return $this->method() === "get";
    }

    /**
     * @return bool
     */
    public function isPost ()
    {
        return $this->method() === "post";
    }

    /**
     * Return the params of the page
     *
     * @return array
     */
    public function getBody ()
    {
        $body = [];

        if ($this->method() === "get")
        {
            $body = $this->getBodySanitize($_GET, INPUT_GET);
        }

        if ($this->method() === "post")
        {
            $body = $this->getBodySanitize($_POST, INPUT_POST);
        }

        return $body;
    }

    private function getBodySanitize (array $method, $input)
    {
        $body = [];
        foreach ($method as $key => $value)
        {
            $body[$key] = filter_input($input, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $body;
    }

}

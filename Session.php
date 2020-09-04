<?php

namespace App\Core;

class Session
{

    protected const FLASH_KEY = "flash_messages";

    public function __construct ()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage)
        {
            $flashMessage["remove"] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash (string $key, string $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            "remove" => false,
            "value"   => $message,
        ];
    }

    public function getFlash (string $key)
    {
        return $_SESSION[self::FLASH_KEY][$key]["value"] ?? false;
    }

    public function set (string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get ($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove ($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct ()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage)
        {
            if ($flashMessage["remove"])
            {
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

}

<?php
namespace App\Core;

class Session
{
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate()
    {
        session_regenerate_id(true);
    }

    public static function destroy()
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function setFlash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash($key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
}

<?php
ob_start();
class session
{

    public static function init()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            if (session_id() == '') {
                session_start();
            }
        } else {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
    }
    public static function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }
    public static function chkLogout()
    {
        if (self::get("login") == false) {
            self::destroy();
            header("location:" . SITE_URL . "/");
        }
    }

    public static function chkAdminLog()
    {
        if (self::get("isAdmin") == false) {
            self::destroy();
            header("location:" . SITE_URL . "/system/login.php");
        }
    }

    public static function chkSystemUserLog()
    {
        if (self::get("whoIs") != 'system_user') {
            self::destroy();
            header("location:" . SITE_URL . "/system_user/login.php");
        }
    }

    public static function chkAdmin()
    {
        if (self::get("role") != 'admin') {
            header("location:" . SITE_URL . "/system/login.php");
        }
    }


    public static function destroy()
    {
        session_destroy();
        session_unset();
        header("location:" . SITE_URL . "/index.php");
    }
}

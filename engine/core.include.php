<?php
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

include(__DIR__ . "/Environment.config.php");
include(__DIR__ . "/MajestiCloudAPI.class.php");
include(__DIR__ . "/webviewengine/WebViewEngine.class.php");
include(__DIR__ . "/../vendor/autoload.php");

use chillerlan\QRCode\{QRCode, QROptions};

function require_token()
{
    if (empty($_SESSION["token"])) {
        header("Location: /");
        exit;
    }
}

function set_alert($message, $level = "info")
{
    $_SESSION["alert"] = [
        "message" => trim($message),
        "level" => $level
    ];
}

function display_alert()
{
    $alert = "";
    if (!empty($_SESSION["alert"])) {
        $alert = '<div class="mb-4 container alert alert-' . $_SESSION["alert"]["level"] . '">' . htmlentities($_SESSION["alert"]["message"]) . '</div>';
    }

    clear_alert();
    return $alert;
}

function clear_alert()
{
    unset($_SESSION["alert"]);
}

function qr_code($data)
{
    $qr = new QRCode();
    return $qr->render($data);
}

set_exception_handler(function ($ex) {
    if ($ex instanceof MajestiCloudAPIException) {
        set_alert($ex->getMessage(), "danger");
        header("Refresh: 0");
    } else {
        header("Location: /error.php?error=" . (SHOW_EXCEPTIONS == "on" ? urlencode($ex->__toString()) : urlencode("500 Internal Server Error")));
        exit;
    }
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

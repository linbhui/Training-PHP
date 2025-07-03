<?php
session_start();

if (isset($_SESSION['last_regenerated'])) {
    if (time() - $_SESSION['last_regenerated'] > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regenerated'] = time();
    }
} else {
    $_SESSION['last_regenerated'] = time();
}

require_once "./core/App.php";
require_once "./core/Controller.php";
require_once "./core/Database.php";

$myApp = new App();
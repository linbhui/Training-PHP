<?php
class systemlogout extends Controller
{
    function index()
    {
        if (isset($_SESSION['admin_logged_in'])) {
            session_unset();
            session_destroy();

            header("Location: /system/login");
            exit();
        }
    }
}
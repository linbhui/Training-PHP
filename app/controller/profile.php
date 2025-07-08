<?php
class profile extends Controller {
    function index()
    {
        if (!isset($_SESSION['user_logged_in'])) {
            header("Location: /login");
            exit();
        }

        $this->view("Profile", [

        ]);
    }
}
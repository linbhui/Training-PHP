<?php
class logout extends Controller
{
    function index()
    {
        $user = $this->model("UserModel");
        if (isset($_SESSION['user_logged_in'])) {
            if ($user->setUserOffline($_SESSION['user_id'])) {
                session_unset();
                session_destroy();
                header("Location: /login");
                exit();
            }
        }
    }
}
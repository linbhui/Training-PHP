<?php
class systemdashboard extends Controller
{
    function index()
    {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/login");
            exit();
        }

        $admin = $this->model("AdminModel");
        $name = $admin->getPersonInfo('admin', $_SESSION['admin_id'])['name'];
        $this->view("Dashboard", [
            'role' => $_SESSION['admin_role'],
            'name' => $name
        ]);
    }
}
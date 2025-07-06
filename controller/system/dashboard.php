<?php
class systemdashboard extends Controller {
    function index() {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/login");
            exit();
        }

        $this->view("Dashboard", [
            'role' => $_SESSION['admin_role']
        ]);
    }
}
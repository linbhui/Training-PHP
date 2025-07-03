<?php
class systemdashboard extends Controller {
    function index() {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/login");
            exit();
        }
        $admin = $this->model("AdminModel");
        $role = mysqli_fetch_assoc($admin->getAdminRole($_SESSION['admin_email']))['role_type'];
        if ($role == 1) {
            $_SESSION['admin_role'] = 'Super Admin';
        } elseif ($role == 2) {
            $_SESSION['admin_role'] = 'Admin';
        }

        $this->view("Dashboard", [
            'role' => $_SESSION['admin_role']
        ]);
    }
}
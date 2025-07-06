<?php
class systemlogin extends Controller {
    function index() {
        if (isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/dashboard");
            exit();
        } else {
            $errors = $_SESSION['errors'] ?? [];
            unset($_SESSION['errors']);

            $this->view("Login", array_merge([
                "title" => "Admin Login",
                "sendto" => "/system/login/authenticate"],
                $errors));
        }
    }
    function authenticate() {
        $email = $password = "";
        $errors = [];
        // Validate email
        if (!empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email = $_POST['email'];
        } else {
            $errors['emailErr'] = "Valid email address is required";
        }
        // Display errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /system/login");
            return;
        }

        // Verify password
        $person = $this->model("AdminModel");
        $passwordHash = $person->getAdminPassword($email);

        if ($passwordHash && password_verify($_POST['password'], $passwordHash)) {
            $admin = $this->model("AdminModel");
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_id'] = $admin->getAdminId($email);
            $role = $admin->getAdminInfo($_SESSION['admin_id'])['role_type'];
            if ($role == 1) {
                $_SESSION['admin_role'] = 'Super Admin';
            } elseif ($role == 2) {
                $_SESSION['admin_role'] = 'Admin';
            }

            header("Location: /system/dashboard");
            exit();
        } else {
            $errors['generalErr'] = "Incorrect email/password";
            $_SESSION['errors'] = $errors;
            header("Location: /system/login");
            return;
        }

    }
}
<?php
class systemlogin extends Controller
{
    function index()
    {
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

    function authenticate()
    {
        $email = $password = "";
        $errors = [];
        $admin = $this->model("AdminModel");

            // Validate email
        $emailInput = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
        if (!empty($emailInput) && filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
        if (!$admin->checkExistEmail('admin', $emailInput)) {
        $errors['generalErr'] = "Incorrect email/password";
        }
        $email = $emailInput;
        } else {
            $errors['emailErr'] = "Valid email address is required";
        }

        // Validate password
        $passwordHash = $admin->getPersonPassword('admin', $email);
        if (!$passwordHash || !password_verify($_POST['password'], $passwordHash)) {
            $errors['generalErr'] = "Incorrect email/password";
        } else {
            // Check account status
            $id = $admin->getPersonId('admin', $email);
            $status = $admin->getPersonInfo('admin', $id)['del_flag'];
            if ($status == 1) {
                $errors['generalErr'] = "Your account is suspended";
            }
        }

        // Display errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /system/login");
            return;
        } else {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_id'] = $admin->getPersonId('admin', $email);
            $role = $admin->getPersonInfo('admin', $_SESSION['admin_id'])['role_type'];
            if ($role == 1) {
                $_SESSION['admin_role'] = 'Super Admin';
            } elseif ($role == 2) {
                $_SESSION['admin_role'] = 'Admin';
            }

            header("Location: /system/dashboard");
            exit();
        }
    }
}
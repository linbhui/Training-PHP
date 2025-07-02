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
            echo "stop1";
            $_SESSION['errors'] = $errors;
            header("Location: /system/login");
            return;
        }

        // Verify password
        $person = $this->model("AdminModel");
        $row = mysqli_fetch_assoc($person->getAdminPassword($email));
        $password = $_POST["password"];

        if ($row && password_verify($password, $row['password'])) {
            echo "stop4";
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
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
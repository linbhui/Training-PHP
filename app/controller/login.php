<?php
class login extends Controller
{
    function index()
    {
        if (isset($_SESSION['user_logged_in'])) {
            header("Location: /profile");
            exit();
        } else {
            $errors = $_SESSION['errors'] ?? [];
            unset($_SESSION['errors']);

            $this->view("Login", array_merge([
                "title" => "Login",
                "sendto" => "/login/authenticate"],
                $errors));
        }
    }

    function authenticate()
    {
        $email = $password = "";
        $errors = [];
        $user = $this->model("UserModel");

        // Validate email
        $emailInput = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
        if (!empty($emailInput) && filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
            if (!$user->checkExistUserEmail($emailInput)) {
                $errors['generalErr'] = "Incorrect email/password";
            }
            $email = $emailInput;
        } else {
            $errors['emailErr'] = "Valid email address is required";
        }

        // Validate password
        $passwordHash = $user->getUserPassword($email);
        if (!$passwordHash || !password_verify($_POST['password'], $passwordHash)) {
            $errors['generalErr'] = "Incorrect email/password";
        } else {
            // Check account status
            $id = $user->getUserId($email);
            $status = $user->getUserInfo($id)['del_flag'];
            if ($status == 1) {
                $errors['generalErr'] = "Your account is suspended";
            }
        }

        // Display errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /login");
            return;
        } else {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = (int) $user->getUserId($email);

            if ($user->setUserOnline($_SESSION['user_id'])) {
                header("Location: /profile");
                exit();
            }

        }
    }
}
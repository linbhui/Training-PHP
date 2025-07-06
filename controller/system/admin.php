<?php
class systemadmin extends Controller {
    function checkPerm() {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/login");
            exit();
        }
    }
    function index() {
        $this->checkPerm();
        $this->view ("Manage", ['area' => "Admin"]);
    }

    function add() {
        $this->checkPerm();

        $name = $email = $password = $avatar = $role = "";
        $errors = [];
        $target_dir = "public/img/";
        $upload = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $person = $this->model("AdminModel");

            // Validate name
            if (!empty(trim($_POST['name']))){
                $name = $_POST['name'];
            }    else {
                $errors['nameErr'] = "Name is required";
            }
            // Validate email
            $tempEmail = trim($_POST['email']);
            $row = $person->checkExistAdminEmail($tempEmail);

            if (empty($tempEmail) && !filter_var($tempEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['emailErr'] = "Valid email address is required";
            } elseif ($row) {
                $errors['emailErr'] = "Unavailable email address";
            }else {
                $email = $tempEmail;
            }

            // Validate & hash password
            if (isset($_POST['password']) && strlen($_POST['password']) >= 8) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                $errors['passwordErr'] = "Minimum 8 characters";
            }

            // Save the role
            if ($_POST['role'] == 1 || $_POST['role'] == 2) {
                $role = (int) $_POST['role'];
            } else {
                $errors['roleErr'] = "Please select a role";
            }

            // Validate avatar
            $target_file = $target_dir . $_FILES['avatar']['name'];
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

            if ($_FILES["avatar"]["size"] > 2000000) {
                $errors['fileErr'] = "File is too large";
                $upload = false;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png") {
                $errors['fileErr'] = "Upload JPG, PNG files";
                $upload = false;
            }

            if ($upload) {
                if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    $errors['fileErr'] = "Could not upload file.";
                } else {
                    $avatar = $target_file;
                }
            }

            // Display errors
            if (!empty($errors)) {
                $this->view ("Manage", array_merge([
                    'area' => "Admin",
                    'function' => "Add"
                ], $errors));
            } else {
                // Add to the database
                $newAdmin = $this->model("AdminModel");
                $insertData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'avatar' => $avatar,
                    'role_type' => $role,
                    'ins_id' => $_SESSION['admin_id'],
                    'ins_datetime' => $newAdmin::SQL_NOW
                ];

                if ($newAdmin->addNewAdmin($insertData)) {
                    $this->view ("Manage", [
                        'area' => "Admin",
                        'action' => "Success",
                        'notif' => "Notification",
                        'message' => "Admin added successfully: " . $email
                    ]);
                }
            }


        } else {
            $this->view ("Manage", [
                'area' => "Admin",
                'function' => "Add"
            ]);
        }
    }

    function update() {
        $this->checkPerm();

        $person = $this->model("AdminModel");
        $account = $update = $error = [];
        $target_dir = "public/img/";
        $upload = true;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            // Retrieve account info
            $row = $person->getAdminInfo($_GET['id']);
            $account['id'] = $row['id'];
            $account['name'] = $row['name'];
            $account['email'] = $row['email'];
            $account['role'] = $row['role_type'];

            $this->view ("Manage", array_merge([
                'area' => "Admin",
                'function' => "Update"
            ], $account));
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $person->getAdminInfo($_POST['updateId']);

            // Get posted info
            $account['id'] = $current['id'];
            $account['name'] = $_POST['name'];
            $account['email'] = $_POST['email'];
            $account['role'] = $_POST['role'];

            // Update name
            if (!empty(trim($_POST['name'])) && $_POST['name'] !== $current['name']){
                $update['name'] = $_POST['name'];
            }

            // Update email
            $tempEmail = trim($_POST['email']);
            $row = $person->checkExistAdminEmail($tempEmail);

            if ($row && $row['id'] != $_POST['updateId']) {
                $error['emailErr'] = "Unavailable email address";
            } elseif (filter_var($tempEmail, FILTER_VALIDATE_EMAIL) && $_POST['email'] !== $current['email']) {
                $update['email'] = $tempEmail;
            }

            // Update role
            if ($_POST['role'] == 1 || $_POST['role'] == 2) {
                if ($_POST['role'] != $current['role_type']) {
                    $update['role_type'] = (int) $_POST['role'];
                }
            }

            // Update avatar
            $target_file = $target_dir . $_FILES['avatar']['name'];
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

            if ($_FILES["avatar"]["size"] > 2000000) {
                $upload = false;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png") {
                $upload = false;
            }

            if ($upload && $target_file !== $current['avatar']) {
                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    $update['avatar'] = $target_file;
                }
            }
            // Display errors
            if (!empty($error)) {
                $this->view ("Manage", array_merge([
                    'area' => "Admin",
                    'function' => "Update"
                ], $account, $error));

            } else {
                // Add to the database
                $validUpdate = implode(', ', array_keys($update));
                $successMessage = empty($update) ? 'Nothing changed' : "Update successfully: $validUpdate";
                $update['upd_id'] = $_SESSION['admin_id'];
                $update['upd_datetime'] = $person::SQL_NOW;
                if ($person->updateAdmin($update, $_POST['updateId'])) {
                    $this->view ("Manage", [
                        'area' => "Admin",
                        'action' => "Success",
                        'notif' => "Notification",
                        'message' => $successMessage
                    ]);
                }
            }

        } else {
            $this->view ("Manage", [
                'area' => "Admin",
                'action' => "Empty",
                'notif' => "Notification",
                'message' => "Empty page"
            ]);
        }
    }

    function list() {
        $this->checkPerm();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Pagination
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Retrieve accounts for display
            $accounts = [];
            $admin = $this->model("AdminModel");
            $all = $admin->getMultipleFlaggedAdmin($limit, $offset, 0);
            $totalAccounts = $admin->getTotalFlaggedAdmin(0);
            $ids = [];

            if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
                $all = $admin->getMultipleFlaggedAdmin($limit, $offset, 1);
                $totalAccounts = $admin->getTotalFlaggedAdmin(1);
            }
            $totalPages = ceil($totalAccounts / $limit);

            $i = 0;
            foreach ($all as $row) {
                $accounts[$i]['id'] = $row['id'];
                $accounts[$i]['name'] = $row['name'];
                $accounts[$i]['email'] = $row['email'];
                $accounts[$i]['role'] = $row['role_type'];
                $accounts[$i]['created_by'] = $admin->getAdminInfo($row['ins_id'])['name'];
                if ($row['upd_id']) {
                    $accounts[$i]['updated_by'] = $admin->getAdminInfo($row['upd_id'])['name'];
                }
                $accounts[$i]['status'] = $row['del_flag'];
                $i++;
            }

            // Display table list
            $this->view ("Manage", array_merge([
                'area' => "Admin",
                'function' => "List",
                'total' => count($all),
                'totalPages' => $totalPages
            ], $accounts));

        }
    }

    function search() {
        $this->checkPerm();

    }

    function delete() {
        $this->checkPerm();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = $this->model("AdminModel");
            $deleteMessage = '';

            foreach ($_GET['ids'] as $id) {
                $adminInfo = $admin->getAdminInfo($id);
                if ($adminInfo['del_flag'] == 0) {
                    $admin->softDeleteAdmin($id);
                    $deleteMessage .= "Temporary deleted " . $adminInfo['name'] . " successfully<br>";
                } else {
                    $admin->hardDeleteAdmin($id);
                    $deleteMessage .= "Permanently deleted " . $adminInfo['name'] . " successfully<br>";
                }
            }

            $this->view ("Manage", [
                'area' => "Admin",
                'action' => "Success",
                'notif' => "Notification",
                'message' => $deleteMessage
            ]);
        }
    }

    function recover() {
        $this->checkPerm();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = $this->model("AdminModel");
            $recoverMessage = '';
            if (isset($_GET['id'])) {
                $admin->recoverDeletedAdmin($_GET['id']);
                $name = $admin->getAdminInfo($_GET['id'])['name'];
                $recoverMessage .= "Recovered " . $name . " successfully<br>";
            }
            else {
                foreach ($_GET['ids'] as $id) {
                    $admin->recoverDeletedAdmin($id);
                    $name = $admin->getAdminInfo($id)['name'];
                    $recoverMessage .= "Recovered " . $name . " successfully<br>";
                }
            }

            $this->view ("Manage", [
                'area' => "Admin",
                'action' => "Success",
                'notif' => "Notification",
                'message' => $recoverMessage
            ]);
        }
    }
}
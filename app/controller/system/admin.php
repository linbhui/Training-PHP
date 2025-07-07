<?php
class systemadmin extends Controller
{
    function checkPerm()
    {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/login");
            exit();
        }
    }

    function index()
    {
        $this->checkPerm();
        $this->view("Manage", [
            'controller' => 'admin'
        ]);
    }

    function add()
    {
        $this->checkPerm();

        $name = $email = $password = $avatar = $role = "";
        $errors = [];
        $target_dir = "app/uploads/";
        $upload = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $person = $this->model("AdminModel");

            // Validate name
            if (!empty(trim($_POST['name']))) {
                $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
            } else {
                $errors['nameErr'] = "Name is required";
            }
            // Validate email
            $tempEmail = trim($_POST['email']);
            $row = $person->checkExistEmail('admin', $tempEmail);

            if (empty($tempEmail) || !filter_var($tempEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['emailErr'] = "Valid email address is required";
            } elseif ($row) {
                $errors['emailErr'] = "Unavailable email address";
            } else {
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
                $role = (int)$_POST['role'];
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
                    $avatar = pathinfo($target_file, PATHINFO_BASENAME);
                }
            }

            // Display errors
            if (!empty($errors)) {
                $this->view("Manage", array_merge([
                    'controller' => 'admin',
                    'action' => "add",
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

                if ($newAdmin->addNewPerson('admin', $insertData)) {
                    $this->view("Manage", [
                        'controller' => 'admin',
                        'result' => "Success",
                        'notif' => "Notification",
                        'message' => "Admin added successfully: " . htmlspecialchars($email ?? '')
                    ]);
                }
            }
        } else {
            $this->view("Manage", [
                'controller' => 'admin',
                'action' => "add"
            ]);
        }
    }

    function update()
    {
        $this->checkPerm();

        $person = $this->model("AdminModel");
        $account = $update = $error = [];
        $target_dir = "app/uploads/";
        $upload = true;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            // Retrieve account info
            $row = $person->getPersonInfo('admin', $_GET['id']);
            $account['id'] = $row['id'];
            $account['name'] = $row['name'];
            $account['email'] = $row['email'];
            $account['role'] = $row['role_type'];

            $this->view("Manage", array_merge([
                'controller' => 'admin',
                'action' => "update"
            ], $account));
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $person->getPersonInfo('admin', $_POST['updateId']);

            // Get posted info
            $account['id'] = $current['id'];
            $account['name'] = $_POST['name'];
            $account['email'] = $_POST['email'];
            $account['role'] = $_POST['role'];

            // Update name
            $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
            if (!empty($name) && $name !== $current['name']) {
                $update['name'] = $name;
            }

            // Update email
            $tempEmail = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
            $row = $person->checkExistPersonEmail('admin', $tempEmail);

            if ($row && $row['id'] != $_POST['updateId']) {
                $error['emailErr'] = "Unavailable email address";
            } elseif (filter_var($tempEmail, FILTER_VALIDATE_EMAIL) && $_POST['email'] !== $current['email']) {
                $update['email'] = $tempEmail;
            }

            // Update role
            if ($_POST['role'] == 1 || $_POST['role'] == 2) {
                if ($_POST['role'] != $current['role_type']) {
                    $update['role_type'] = (int)$_POST['role'];
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
                $this->view("Manage", array_merge([
                    'controller' => 'admin',
                    'action' => "update"
                ], $account, $error));

            } else {
                // Add to the database
                $validUpdate = implode(', ', array_keys($update));
                $successMessage = empty($update) ? 'Nothing changed' : "Update successfully: $validUpdate";
                $update['upd_id'] = $_SESSION['admin_id'];
                $update['upd_datetime'] = $person::SQL_NOW;
                if ($person->updatePerson('admin', $update, $_POST['updateId'])) {
                    $this->view("Manage", [
                        'controller' => 'admin',
                        'result' => "Success",
                        'notif' => "Notification",
                        'message' => $successMessage
                    ]);
                }
            }

        } else {
            $this->view("Manage", [
                'controller' => 'admin',
                'result' => "Empty",
                'notif' => "Notification",
                'message' => "Empty page"
            ]);
        }
    }

    function list($searchby = null, $searchpattern = null)
    {
        $this->checkPerm();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Pagination
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Retrieve accounts for display
            $accounts = [];
            $admin = $this->model("AdminModel");
            $all = $admin->getMultipleFlaggedPeople('admin', $limit, $offset, null, $searchby, $searchpattern);
            $totalAccounts = $admin->getTotalFlaggedPeople('admin', $searchby, $searchpattern);
            $ids = [];

            if (isset($_GET['status']) && $_GET['status'] === 'active') {
                $all = $admin->getMultipleFlaggedPeople('admin', $limit, $offset, 0, $searchby, $searchpattern);
                $totalAccounts = $admin->getTotalFlaggedPeople('admin', 0, $searchby, $searchpattern);
            } elseif (isset($_GET['status']) && $_GET['status'] === 'deleted') {
                $all = $admin->getMultipleFlaggedPeople('admin', $limit, $offset, 1, $searchby, $searchpattern);
                $totalAccounts = $admin->getTotalFlaggedPeople('admin', 1, $searchby, $searchpattern);
            }
            $totalPages = ceil($totalAccounts / $limit);

            $i = 0;
            foreach ($all as $row) {
                $accounts[$i]['id'] = $row['id'];
                $accounts[$i]['name'] = $row['name'];
                $accounts[$i]['email'] = $row['email'];
                $accounts[$i]['role'] = $row['role_type'] == 1 ? 'Super Admin' : 'Admin';;
                $accounts[$i]['created_by'] = $admin->getPersonInfo('admin', $row['ins_id'])['name'];
                if ($row['upd_id']) {
                    $accounts[$i]['updated_by'] = $admin->getPersonInfo('admin', $row['upd_id'])['name'];
                }
                $accounts[$i]['status'] = $row['del_flag'];
                $i++;
            }

            // Display table list
            $this->view("Manage", array_merge([
                'controller' => 'admin',
                'action' => 'list',
                'total' => count($all),
                'totalPages' => $totalPages,
                'search-term' => $searchpattern
            ], $accounts));
        }
    }

    function search()
    {
        $this->checkPerm();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $searchby = isset($_GET['search-by']) ? $_GET['search-by'] : '';
            $searchpattern = isset($_GET['search']) ? $_GET['search'] : '';
            if (!empty($searchby) && !empty($searchpattern)) {
                $this->list($searchby, $searchpattern);
            }
        }

    }

    function delete()
    {
        $this->checkPerm();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = $this->model("AdminModel");
            $deleteMessage = '';

            foreach ($_GET['ids'] as $id) {
                $adminInfo = $admin->getPersonInfo('admin', $id);
                if ($adminInfo['del_flag'] == 0) {
                    $admin->softDeletePerson('admin', $id);
                    $deleteMessage .= "Temporary deleted " . $adminInfo['name'] . " successfully<br>";
                } else {
                    $admin->hardDeletePerson('admin', $id);
                    $deleteMessage .= "Permanently deleted " . $adminInfo['name'] . " successfully<br>";
                }
            }

            $this->view("Manage", [
                'controller' => 'admin',
                'result' => "Success",
                'notif' => "Notification",
                'message' => $deleteMessage
            ]);
        }
    }

    function recover()
    {
        $this->checkPerm();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = $this->model("AdminModel");
            $recoverMessage = '';
            if (isset($_GET['id'])) {
                $admin->recoverDeletedPerson('admin', $_GET['id']);
                $name = $admin->getPersonInfo('admin', $_GET['id'])['name'];
                $recoverMessage .= "Recovered " . $name . " successfully<br>";
            } else {
                foreach ($_GET['ids'] as $id) {
                    $admin->recoverDeletedPerson('admin', $id);
                    $name = $admin->getPersonInfo('admin', $id)['name'];
                    $recoverMessage .= "Recovered " . $name . " successfully<br>";
                }
            }

            $this->view("Manage", [
                'controller' => 'admin',
                'result' => "Success",
                'notif' => "Notification",
                'message' => $recoverMessage
            ]);
        }
    }
}
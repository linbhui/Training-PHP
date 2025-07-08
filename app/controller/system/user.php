<?php
class systemuser extends Controller
{
    function checkPerm()
    {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/login");
            exit();
        } else {
            if ($_SESSION['admin_role'] !== 'Super Admin') {
                header("Location: /system/dashboard");
                exit();
            }
        }
    }

    function index()
    {
        $this->checkPerm();
        $this->view("Manage", [
            'controller' => "user"
        ]);
    }

    function add()
    {
        $this->checkPerm();

        $name = $email = $password = $avatar = $role = "";
        $errors = [];
        $target_dir = "app/uploads/user/";
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
            $row = $person->checkExistEmail('user', $tempEmail);

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
                    'controller' => 'user',
                    'action' => "add",
                ], $errors));
            } else {
                // Add to the database
                $newUser = $this->model("AdminModel");
                $insertData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'avatar' => $avatar,
                    'ins_id' => $_SESSION['admin_id'],
                    'ins_datetime' => $newUser::SQL_NOW
                ];

                if ($newUser->addNewPerson('user', $insertData)) {
                    $successMessage = "User added successfully: " . htmlspecialchars($email);
                    header("Location: /system/user/list?" . appendParams(['result' => 1, 'message' => $successMessage]));
                    exit();
                }
            }
        } else {
            $this->view("Manage", [
                'controller' => 'user',
                'action' => "add"
            ]);
        }
    }

    function update()
    {
        $this->checkPerm();

        $person = $this->model("AdminModel");
        $account = $update = $error = [];
        $target_dir = "app/uploads/user/";
        $upload = true;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            // Retrieve account info
            $row = $person->getPersonInfo('user', $_GET['id']);
            $account['id'] = $row['id'];
            $account['name'] = $row['name'];
            $account['email'] = $row['email'];

            $this->view("Manage", array_merge([
                'controller' => 'user',
                'action' => "update"
            ], $account));
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $person->getPersonInfo('user', $_POST['updateId']);

            // Get posted info
            $account['id'] = $current['id'];
            $account['name'] = $_POST['name'];
            $account['email'] = $_POST['email'];


            // Update name
            $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
            if (!empty($name) && $name !== $current['name']) {
                $update['name'] = $name;
            }

            // Update email
            $tempEmail = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
            $row = $person->checkExistEmail('user', $tempEmail);

            if ($row && $row['id'] != $_POST['updateId']) {
                $error['emailErr'] = "Unavailable email address";
            } elseif (filter_var($tempEmail, FILTER_VALIDATE_EMAIL) && $tempEmail !== $current['email']) {
                $update['email'] = $tempEmail;
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
                    'controller' => 'user',
                    'action' => "update"
                ], $account, $error));

            } else {
                // Add to the database
                $validUpdate = implode(', ', array_keys($update));
                $successMessage = empty($update) ? 'Nothing changed' : "Update successfully: $validUpdate";
                $update['upd_id'] = $_SESSION['admin_id'];
                $update['upd_datetime'] = $person::SQL_NOW;
                if ($person->updatePerson('user', $update, $_POST['updateId'])) {
                    header("Location: /system/user/list?" . appendParams(['result' => 1, 'message' => $successMessage]));
                    exit();
                }
            }

        } else {
            $this->list();
        }
    }

    function list($searchby = null, $searchpattern = null, $result = null, $message = null)
    {
        $this->checkPerm();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Pagination
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Retrieve accounts for display
            $accounts = [];
            $user = $this->model("AdminModel");
            $all = $user->getMultipleFlaggedPeople('user', $limit, $offset, null, $searchby, $searchpattern);
            $totalAccounts = $user->getTotalFlaggedPeople('user', $searchby, $searchpattern);
            $ids = [];

            if (isset($_GET['status']) && $_GET['status'] === 'active') {
                $all = $user->getMultipleFlaggedPeople('user', $limit, $offset, 0, $searchby, $searchpattern);
                $totalAccounts = $user->getTotalFlaggedPeople('user', 0, $searchby, $searchpattern);
            } elseif (isset($_GET['status']) && $_GET['status'] === 'deleted') {
                $all = $user->getMultipleFlaggedPeople('user', $limit, $offset, 1, $searchby, $searchpattern);
                $totalAccounts = $user->getTotalFlaggedPeople('user', 1, $searchby, $searchpattern);
            }
            $totalPages = ceil($totalAccounts / $limit);

            $i = 0;
            foreach ($all as $row) {
                $accounts[$i]['id'] = $row['id'];
                $accounts[$i]['name'] = $row['name'];
                $accounts[$i]['email'] = $row['email'];
                $accounts[$i]['current'] = $row['status'] == 1 ? 'Online' : 'Offline';
                $accounts[$i]['created_by'] = $user->getPersonInfo('admin', $_SESSION['admin_id'])['name'];
                if ($row['upd_id']) {
                    $accounts[$i]['updated_by'] = $user->getPersonInfo('admin', $_SESSION['admin_id'])['name'];
                }
                $accounts[$i]['status'] = $row['del_flag'];
                $i++;
            }

            // Display table list
            $this->view("Manage", array_merge([
                'controller' => 'user',
                'action' => 'list',
                'total' => count($all),
                'totalPages' => $totalPages,
                'dif-col' => 'current',
                'search-term' => $searchpattern,
                'result' => $result,
                'message' => $message,
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
            $user = $this->model("AdminModel");
            $deleteMessage = '';

            foreach ($_GET['ids'] as $id) {
                $userInfo = $user->getPersonInfo('user', $id);
                $result = 1;
                if ($userInfo['del_flag'] == 0) {
                    $user->softDeletePerson('user', $id);
                    $deleteMessage .= "Temporary deleted " . $userInfo['name'] . " successfully<br>";
                    $result = 2;
                } else {
                    $user->hardDeletePerson('user', $id);
                    $deleteMessage .= "Permanently deleted " . $userInfo['name'] . " successfully<br>";
                    $result = 0;
                }
            }

            header("Location: /system/user/list?" . appendParams(['result' => $result, 'message' => $deleteMessage]));
            exit();
        }
    }

    function recover()
    {
        $this->checkPerm();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $user = $this->model("AdminModel");
            $recoverMessage = '';
            $result = 0;
            if (isset($_GET['id'])) {
                $name = $user->getPersonInfo('user', $_GET['id'])['name'];
                if ($user->getPersonInfo('user', $_GET['id'])['del_flag'] === 1) {
                    $user->recoverDeletedPerson('user', $_GET['id']);
                    $recoverMessage .= "Recovered " . $name . ".<br>";
                    $result = 1;
                } else {
                    $recoverMessage .= "No change to " . $name . ".<br>";
                    $result = 2;
                }
            } else {
                foreach ($_GET['ids'] as $id) {
                    $name = $user->getPersonInfo('user', $id)['name'];
                    if ($user->getPersonInfo('user', $id)['del_flag'] === 1) {
                        $user->recoverDeletedPerson('user', $id);
                        $recoverMessage .= "Recovered " . $name . ".<br>";
                        $result = 1;
                    } else {
                        $recoverMessage .= "No change to " . $name . ".<br>";
                        $result = 2;
                    }
                }
            }

            header("Location: /system/user/list?" . appendParams(['result' => $result, 'message' => $recoverMessage]));
            exit();
        }
    }
}

/*
 *
 */
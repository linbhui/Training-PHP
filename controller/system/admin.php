<?php
class systemadmin extends Controller {
    function index() {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /system/login");
            exit();
        }

        $this->view ("Manage", ['area' => "Admin"]);
    }

    function add() {
        $name = $email = $password = $avatar = $role = "";
        $errors = [];
        $target_dir = "public/img/";
        $upload = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate name
            if (!empty(trim($_POST['name']))){
                $name = $_POST['name'];
            }    else {
                $errors['nameErr'] = "Name is required";
            }
            // Validate email
            $tempEmail = trim($_POST['email']);
            $person = $this->model("AdminModel");
            $row = mysqli_fetch_assoc($person->checkExistAdmin($tempEmail));

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
            if ($_POST['role'] === 'super-admin') {
                $role = 1;
            } elseif ($_POST['role'] === 'admin') {
                $role = 2;
            } else {
                $errors['roleErr'] = "Please select a role";
            }

            // Validate avatar
            $target_file = $target_dir . $_FILES['avatar']['name'];
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

            if ($_FILES["avatar"]["size"] > 2000000) {
                $errors['fileFail'] = "File is too large.";
                $upload = false;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png") {
                $errors['fileFail'] = "Only JPG, PNG files are allowed.";
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
                $insertAdmin = $this->model("AdminModel");
                $newAdmin = $this->model("AdminModel");

                $row = mysqli_fetch_assoc($insertAdmin->getAdminId($_SESSION['admin_email']));
                $ins_id = $row ? $row['id'] : NULL;

                if (isset($ins_id)) {
                    $newAdmin->addNewAdmin($name, $email, $password, $avatar, $role, $ins_id);
                }

                // Notify success
                $this->view ("Manage", [
                    'area' => "Admin",
                    'notif' => "Adding " . $email . " successfully!"
                ]);
            }

        } else {
            $this->view ("Manage", [
                'area' => "Admin",
                'function' => "Add"
            ]);
        }
    }

    function list() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Pagination
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Retrieve all accounts
            $accounts = [];
            $all = $this->model("AdminModel");
            $totalAccounts = $all->getTotalAdmin();
            $nextRow = $all->getAllAdminId($limit, $offset);
            $ids = [];

            if (isset($_GET['account'])) {
                // Activate admin table
                if ($_GET['account'] === 'active') {
                    $nextRow = $all->getFlaggedAdminId(0, $limit, $offset);
                    $totalAccounts = $all->getTotalFilteredAdmin(0);
                }
                // Deleted admin table
                elseif ($_GET['account'] === 'deleted') {
                    $nextRow = $all->getFlaggedAdminId(1, $limit, $offset);
                    $totalAccounts = $all->getTotalFilteredAdmin(1);
                }
            }

            // Get account info
            while ($row = mysqli_fetch_assoc($nextRow)) {
                $ids[] = $row['id'];
            }
            $totalID = count($ids);

            foreach ($ids as $id) {
                $accounts[$id] = [];
                $person = $this->model("AdminModel");
                $row = $person->getAdminDisplayInfo($id);
                $accounts[$id]['id'] = $row['id'];
                $accounts[$id]['name'] = $row['name'];
                $accounts[$id]['email'] = $row['email'];
                $accounts[$id]['role'] = $row['role_type'];
                $accounts[$id]['created_by'] = $person->getAdminName($row['ins_id'])['name'];
                if (isset($person->getAdminName($row['upd_id'])['name'])) {
                    $accounts[$id]['updated_by'] = $person->getAdminName($row['upd_id'])['name'];
                }
                $accounts[$id]['status'] = $row['del_flag'];
            }

            // Get total pages
            $totalPages = ceil($totalAccounts / $limit);

            // Display table list
            $this->view ("Manage", array_merge([
                'area' => "Admin",
                'function' => "List",
                'total' => $totalID,
                'totalPages' => $totalPages
            ], $accounts));

        }


    }

    function search() {

    }
}
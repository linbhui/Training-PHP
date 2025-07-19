<?php
class profile extends Controller {
    function index()
    {
        if (!isset($_SESSION['user_logged_in'])) {
            header("Location: /login");
            exit();
        }

        $userInfo = [];
        $user = $this->model("UserModel");
        $admin = $this->model("AdminModel");
        $userRow = $user->getUserInfo($_SESSION['user_id']);
        $userInfo['id'] = $userRow['id'];
        $userInfo['name'] = $userRow['name'];
        $userInfo['email'] = $userRow['email'];
        $userInfo['avatar'] = "./app/uploads/user/" . $userRow['avatar'];
        $userInfo['status'] = $userRow['status'] == 1 ? "Online" : "Offline";
        $userInfo['ins_name'] = $admin->getPersonInfo('admin', $userRow['ins_id'])['name'];
        $this->view("Profile", $userInfo);
    }
}
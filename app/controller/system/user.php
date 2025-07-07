<?php
class systemuser extends Controller {
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

    }

    function update()
    {

    }

    function list($action = "list", $searchby = null, $searchpattern = null)
    {

    }

    function search()
    {

    }

    function delete()
    {

    }

    function recover()
    {

    }
}
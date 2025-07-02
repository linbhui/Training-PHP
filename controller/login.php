<?php
class login extends Controller {
    function __construct() {
        $this->view("Login", [
            "title" => "Login",
            "sendto" => "/login/authenticate"]);
    }

    function authenticate() {
        echo "hurray";
    }


}
<?php
class systemadmin extends Controller {
    function index() {
        $this->view ("Manage", [
            'function' => "Admin"
        ]);
    }
}
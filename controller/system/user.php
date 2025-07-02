<?php

class systemuser extends Controller {
    function index()
    {
        $this->view("Manage", [
            'function' => "User"
        ]);
    }
}
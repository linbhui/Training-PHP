<?php
class Controller {
    function model($model) {
        require_once "./model/" . $model . ".php";
        return new $model;
    }

    function view($view, $data=[]) {
        require_once "./view/" . $view . ".php";
    }
}
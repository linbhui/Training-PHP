<?php
class Controller {
    function model($model) {
        require_once "./app/model/" . $model . ".php";
        return new $model;
    }

    function view($view, $data=[]) {
        require_once "./app/view/" . $view . ".php";
    }
}
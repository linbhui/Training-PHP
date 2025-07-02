<?php
class App {
    protected $module = "";
    protected $controller = "printError";
    protected $action = "index";
    protected $params = [];

    function __construct() {
        $arr = $this->urlProcess();

         // Set controller
        if (isset($arr[0])) {
            // Check if it is admin module
            if ($arr[0] == "system") {
                $arr = array_slice($arr, 1);
                if (isset($arr[0])) {
                    if (file_exists("./controller/system/" . $arr[0] . ".php")) {
                        $this->module = "system";
                    }
                }
            }
            if (file_exists("./controller/" . $this->module . "/" . $arr[0] . ".php")) {
                $this->controller = $arr[0];
                unset($arr[0]);
            }
        }
        require_once "./controller/" . $this->module . "/" . $this->controller . ".php";
        $className = $this->module . $this->controller;
        $this->controller = new $className;

        // Set action
        if (isset($arr[1]) && method_exists($this->controller, $arr[1])) {
            $this->action = $arr[1];
            unset($arr[1]);
        } elseif (!method_exists($this->controller, "index")) {
            $this->action = NULL;
        }

        // Set params
        $this->params = $arr? array_values($arr) : [];

        // Call the action
        if (isset($this->action)) {
            call_user_func_array([$this->controller, $this->action], $this->params);
        }


    }

    function urlProcess() {
        if (isset($_GET["url"])) {
            return explode("/", filter_var(trim($_GET["url"], "/")));
        }
    }

}

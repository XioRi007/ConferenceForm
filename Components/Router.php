<?php

namespace Components;

class Router {

    protected $routes = [];
    protected $params = [];
    
    public function __construct() {
        $arr = require './config/routes.php';
        foreach ($arr as $key => $val) {
            $this->add($key, $val);
        }
    }

    public function add($route, $params) {
        $route = '#^'.$route.'$#';
        $this->routes[$route] = $params;
    }

    public function match() {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function run(){
        if ($this->match()) {
            $path = 'Controllers\\'.ucfirst($this->params['controller']).'Controller';
            if (class_exists($path)) {
                $action = ucfirst($this->params['action']).'Action';
                if (method_exists($path, $action)) {
                    $controller = new $path($this->params);
                    $controller->$action();
                }
            }
        }
        else{
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/Views/vue/dist'.$_SERVER['REQUEST_URI'];
            if (file_exists($file_path)) {
                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));
                
                $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                $type = 'application/octet-stream';
                switch($ext){
                    case 'js':
                        $type = 'application/javascript';
                        break;
                    case 'css':
                        $type = 'text/css';
                        break;
                    case 'html':
                        $type = 'text/html';
                        break;
                    case 'ico':
                        $type = 'image/x-icon';
                        break;
                    default:
                }
                header('Content-Type: '.$type);
                readfile($file_path);
                exit;
            } else {
                readfile( $_SERVER['DOCUMENT_ROOT'] . '/Views/vue/dist/index.html');
            }
        }
    }
}
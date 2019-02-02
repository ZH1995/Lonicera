<?php
class Lonicera {

    private $objRoute;

    public function run() {
        require_once _SYS_PATH . 'core/Route.php';
        $this->route();
        $this->dispatch();
    }

    public function route() {
        $this->objRoute = new Route();
        $this->objRoute->init();
    }

    public function dispatch() {
        $strControlName = $this->objRoute->strControl . 'Controller';
        $strActionName  = $this->objRoute->strAction . 'Action';
        $strPath = _APP . $this->objRoute->strGroup . DIRECTORY_SEPARATOR . 'module';
        $strPath .= DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $strControlName . '.php';

        require_once $strPath;
        $arrMethod = get_class_methods($strControlName);
        if (!in_array($strActionName, $arrMethod, TRUE)) {
            throw new Exception(sprintf('方法名%s->%s不存在或非public', $strControlName, $strActionName));
        }

        $objHandler = new $strControlName();
        $objHandler->param = $this->param;
        $objHandler->{$strActionName}();
    }
}

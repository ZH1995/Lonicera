<?php
class IndexController {

    public function indexAction() {
        require_once _SYS_PATH . 'core/DB.php';
        require_once _SYS_PATH . 'core/Model.php';
        require_once _APP . 'model/User.php';

        $objUser = new User();
        $objUser->id  = 2;
        $objUser->age = 20;
        $objUser->save();
    }

    public function hiAction() {
        require_once _SYS_PATH . 'core/DB.php';
        $objDb = DB::getInstance($GLOBALS['_config']['db']);
        $arrRet = $objDb->query('select * from o2o_user where age > :age and id >= :id', array('age' => 10, 'id' => 1));
        var_dump($arrRet);
        echo "hi action\n";
    }
}
<?php
class IndexController {

    public function indexAction() {
        echo "index action\n";
    }

    public function hiAction() {
        require_once _SYS_PATH . 'core/DB.php';
        $objDb = DB::getInstance($GLOBALS['_config']['db']);
        $arrRet = $objDb->query('select * from o2o_user where age > :age and id >= :id', array('age' => 10, 'id' => 1));
        var_dump($arrRet);
        echo "hi action\n";
    }
}
<?php
class Model {

    public function getRealTableName($strTableName, $strPrefix = 'o2o') {
        if (!empty($strPrefix)) {
            $strRealTableName = $strPrefix . "_{$strTableName}";
        } elseif (isset($GLOBALS['_config']['db']['prefix']) && !empty($GLOBALS['_config']['db']['prefix'])) {
            $strRealTableName = $GLOBALS['_config']['db']['prefix'] . "_{$strTableName}";
        } else {
            $strRealTableName = $strTableName;
        }

        return $strRealTableName;
    }

    public function buildPO($strTableName, $strPrefix = '') {
        $objDb = DB::getInstance($GLOBALS['_config']['db']);
        $arrRet = $objDb->query('SELECT * FROM `infomation_schema`.`COLUMNS` WHERE TABLE_NAME=:TABLENAME', array('TABLENAME' => $this->getRealTableName($strTableName, $strPrefix)));
        $strClassName = ucfirst($strTableName);
        $strFile = _APP . 'model/' . $strClassName . '.php';
        $strClassContent = "<?php \r\nclass $strClassName extends Model { \r\n }";
        foreach ($arrRet as $key => $val) {
            $strClassContent .= 'public $' . "{$val['COLUMN_NAME']};";
            if (!empty($val['COLUMN_COMMENT'])) {
                $strClassContent .= "        // {$val['COLUMN_COMMENT']}";
            }
            $strClassContent .= "\r\n";
            $strClassContent .= "}";
            file_put_contents($strFile, $strClassContent);
        }
    }

    public function getTableNameByPO($reflect) {
        return $this->getRealTableName(strtolower($reflect->getShortName()));
    }

    public function save() {
        $reflect = new ReflectionClass($this);
        $arrProp = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        $strSqlTemplate = "INSERT INTO " . $this->getTableNameByPO($reflect) . "(";
        $arrKey = array_column($arrProp, 'name');
        $strKey = implode(',', $arrKey);
        $strPrepareKey = implode(',', array_map(function($key) {return ':' . $key;}, $arrKey));
        $strSqlTemplate = "INSERT INTO " . $this->getTableNameByPO($reflect) . "({$strKey}) VALUES ({$strPrepareKey})";

        $arrData = array();
        foreach ($arrProp as $val) {
            $arrData[$val->name] = $reflect->getProperty($val->name)->getValue($this);
        }

        $objDb  = DB::getInstance($GLOBALS['_config']['db']);
        $arrRet = $objDb->execute($strSqlTemplate, $arrData);

        return $arrRet;
    }
}

<?php
class DB {
    
    private $dbLink;

    protected $intQueryNum = 0;
    
    private static $instance;

    protected $PDOStatement;

    protected $intTransTime = 0;

    protected $arrBind = array();

    public $intRow = 0;

    private function __construct($arrConfig) {
        $this->connect($arrConfig);
    }

    public static function getInstance($arrConfig) {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($arrConfig);
        }
        
        return self::$instance;
    }
    
    public function connect($arrConfig) {
        try {
            if (empty($arrConfig['param'])) {
                $this->dbLink = new PDO($arrConfig['dsn'], $arrConfig['username'], $arrConfig['password']);
            } else {
                $this->dbLink = new PDO($arrConfig['dsn'], $arrConfig['username'], $arrConfig['password'], $arrConfig['param']);
            }
        } catch (\PDOException $e) {
            throw $e;
        }

        return $this->dbLink;
    }

    public function query($strSql, $arrBind = array(), $fetchType = PDO::FETCH_ASSOC) {
        if (!$this->dbLink) {
            throw new Exception('数据库连接失败');
        }
    
        $this->PDOStatement = $this->dbLink->prepare($strSql);
        $this->PDOStatement->execute($arrBind);
        $arrRet = $this->PDOStatement->fetchAll($fetchType);
        $this->intRow = count($arrRet);
        
        return $arrRet;
    }

    public function execute($strSql, $arrBind = array()) {
        if (!$this->dbLink) {
            throw new Exception('数据库连接失败');
        }
        
        $this->PDOStatement = $this->dbLink->prepare($strSql);
        $arrRet = $this->PDOStatement->execute($arrBind);
        $this->intRow = $this->PDOStatement->rowCount();

        return $arrRet;
    }

    public function startTrans() {
        $this->intTransTime ++;
        if (1 === $this->intTransTime) {
            $this->dbLink->beginTransaction();
        } else {
            $this->dbLink->execute("SAVEPOINT tr{$this->intTransTime}");
        }
    }

    public function commit() {
        if (1 === $this->intTransTime) {
            $this->dbLink->commit();
        }

        $this->intTransTime --;
    }

    public function rollback() {
        if (1 === $this->intTransTime) {
            $this->dbLink->rollBack();
        } elseif (1 < $this->intTransTime) {
            $this->dbLink->execute("ROLLBACK TO SAVEPOINT tr($this->intTransTime)");
        }

        $this->intTransTime = max(0, $this->intTransTime - 1);
    }
}
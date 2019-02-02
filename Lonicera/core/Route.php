<?php
class Route {
    /**
     * 分组名，或称为module
     */
    public $strGroup;

    /**
     * 控制器
     */
    public $strControl;

    /**
     * 控制器中的方法
     */
    public $strAction;

    /**
     * 传递给action的参数
     */
    public $strParam;

    public function __construct() {}

    public function init() {
        $arrRoute = $this->getRequest();
        $this->strGroup   = $arrRoute['group'];
        $this->strControl = $arrRoute['control'];
        $this->strAction  = $arrRoute['action'];
        if (!empty($arrRoute['param'])) {
            $this->strParam = $arrRoute['param'];
        }
    }

    /**
     * 实现对不同格式的URL进行解析分类处理
     * 当前只实现了对Path Url方式和传统URL方式的解析
     */
    public function getRequest() {
        //return $this->parseTradition();
        return $this->parseByPathInfo();
    }

    /**
     * 解析传统格式的URL
     */
    public function parseTradition() {
        $arrRoute = array();
        $arrRoute['group'] = isset($_GET[$GLOBALS['_config']['UrlGroupName']]) ? $_GET[$GLOBALS['_config']['UrlGroupName']] : $GLOBALS['_config']['defaultApp'];
        $arrRoute['control'] = isset($_GET[$GLOBALS['_config']['UrlControllerName']]) ? $_GET[$GLOBALS['_config']['UrlControllerName']] : $GLOBALS['_config']['defaultController'];
        $arrRoute['action'] = isset($_GET[$GLOBALS['_config']['UrlActionName']]) ? $_GET[$GLOBALS['_config']['UrlActionName']] : $GLOBALS['_config']['defaultAction'];
        unset($_GET[$GLOBALS['_config']['UrlGroupName']]);
        unset($_GET[$GLOBALS['_config']['UrlControllerName']]);
        unset($_GET[$GLOBALS['_config']['UrlActionName']]);
        $arrRoute['param'] = $_GET;

        return $arrRoute;
    }

    /**
     * 解析PATH_INFO格式的URL
     * 简单实现，没考虑过于复杂繁琐的实际情况
     */
    public function parseByPathInfo() {
        $arrFilterParam = array(
            '<',
            '>',
            '"',
            "'",
            '%3C',
            '%3E',
            '%22',
            '%27',
            '%3c',
            '%3e',
        );
        $strUri  = str_replace($arrFilterParam, '', $_SERVER['REQUEST_URI']);
        $arrPath = parse_url($strUri);
        if (0 === strpos($arrPath['path'], 'index.php')) {
            $strUrl = $arrPath['path'];
        } else {
            $strUrl = substr($arrPath['path'], strlen('index.php') + 1);
        }

        $strUrl = ltrim($strUrl, '/');
        if ('' === $strUrl) {
            $arrRoute = $this->parseTradition();
            return $arrRoute;
        }

        // 去除空白
        $arrReq = explode('/', $strUrl);
        foreach ($arrReq as $key => $val) {
            if (empty($val)) {
                unset($arrReq[$key]);
            }
        }

        if (stripos($arrReq[0], ':')) {
            $arrGroupControl = explode(':', $arrReq[0]);
            $arrRoute['group']   = $arrGroupControl[0];
            $arrRoute['control'] = $arrGroupControl[1];
            $arrRoute['action']  = $arrReq[1];
        } else {
            $arrRoute['group']   = $GLOBALS['_config']['defaultApp'];
            $arrRoute['control'] = empty($arrReq[0]) ? $GLOBALS['_config']['defaultController'] : $arrReq[0];
            $arrRoute['action']  = empty($arrReq[1]) ? $GLOBALS['_config']['defaultAction'] : $arrReq[1];
        }

        return $arrRoute;
    }
}

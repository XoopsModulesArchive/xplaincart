<?php

define('dbHost', XOOPS_DB_HOST);
define('dbUser', XOOPS_DB_USER);
define('dbPass', XOOPS_DB_PASS);
define('dbName', XOOPS_DB_NAME);

class database_class
{
    //var $sqlresult;

    //var $result;

    public $dbConn;

    public function __construct()
    {
        $this->dbConn = mysql_connect(dbHost, dbUser, dbPass) or die('MySQL connect failed. ' . $GLOBALS['xoopsDB']->error());

        mysqli_select_db($GLOBALS['xoopsDB']->conn, dbName, $this->dbConn) || die('Cannot select database. ' . $GLOBALS['xoopsDB']->error());
    }

    public function dbQuery($sql)
    {
        //$this->sqlresult = $GLOBALS['xoopsDB']->queryF($sql) || die($GLOBALS['xoopsDB']->error());

        return $GLOBALS['xoopsDB']->queryF($sql);
    }

    public function dbAffectedRows()
    {
        //$this->result=$GLOBALS['xoopsDB']->getAffectedRows($this->dbConn);

        return $GLOBALS['xoopsDB']->getAffectedRows($this->dbConn);
    }

    public function dbFetchArray($result)
    {
        //$this->result=$GLOBALS['xoopsDB']->fetchBoth($this->sqlresult,MYSQL_NUM);

        return $GLOBALS['xoopsDB']->fetchBoth($result, MYSQL_NUM);
    }

    public function dbFetchAssoc($result)
    {
        //$this->result=$GLOBALS['xoopsDB']->fetchArray($this->sqlresult);

        return $GLOBALS['xoopsDB']->fetchArray($result);
    }

    public function dbFetchRow($result)
    {
        //$this->result=$GLOBALS['xoopsDB']->fetchRow($this->sqlresult);

        return $GLOBALS['xoopsDB']->fetchRow($result);
    }

    public function dbFreeResult($result)
    {
        $GLOBALS['xoopsDB']->freeRecordSet($result);

        //$GLOBALS['xoopsDB']->freeRecordSet($this->sqlresult);
    }

    public function dbNumRows($result)
    {
        //$this->result=$GLOBALS['xoopsDB']->getRowsNum($this->sqlresult);

        return $GLOBALS['xoopsDB']->getRowsNum($result);
    }

    public function dbSelect()
    {
        return mysqli_select_db($GLOBALS['xoopsDB']->conn, dbName);
    }

    public function dbInsertId()
    {
        $this->result = $GLOBALS['xoopsDB']->getInsertId();

        return $this->result;
    }
}

<?php

namespace News\Model\Base;


class Db
{
    private static $instance;
    protected $dbh;


    private function __construct()
    {
        $conf = include_once(__DIR__.'/_db.conf.php');
        $this->dbh = new \PDO($conf['db'], $conf['user'], $conf['password']);
    }


    private function __clone() {}
    public function __wakeup() {}


    public static function getInstance()
    {
        if ( empty (self::$instance) ) {
            self::$instance = new self;
        }
        return self::$instance;
    }



    /**
     * @param string $sql
     * @param array $params
     * @param string $class
     * @param string $sort
     * @return array
     * @throws \Exception
     */
    public function query($sql, $params = [], $class = null, $sort = 'id')
    {
        $result = [];
        // счётчик для индексации
        $count = 0;

        $sth = $this->dbh->prepare($sql);

        /* return objects */
        if (null != $class) {
            $sth->setFetchMode(\PDO::FETCH_CLASS, $class);

            if (!$sth->execute($params)) {
                throw new \Exception( 'Error query: ' . implode( '  |  ', $sth->errorInfo() ) );
            }

            // формируем необходимый нам массив
            while ($obj = $sth->fetch()) {
                // если колонки $sort нет в выборке, делаем индексы
                if ( empty($obj->$sort) ) {
                    $result[$count++] = $obj;
                }
                else {
                    $result[$obj->$sort] = $obj;
                }
            }
        }
        else {
            $sth->setFetchMode(\PDO::FETCH_ASSOC);

            if (!$sth->execute($params)) {
                throw new \Exception( 'Error query: ' . implode( '  |  ', $sth->errorInfo() ) );
            }
            // формируем необходимый нам массив
            while ($arr = $sth->fetch()) {
                // если колонки $sort нет в выборке, делаем индексы
                if ( empty($arr[$sort]) ) {
                    $result[$count++] = $arr;
                }
                else {
                    $result[$arr[$sort]] = $arr;
                }
            }
        }

        return $result;

    }



    /*
     * does not return a values (Create-Update-Delete)
     */
    public function execute($sql, $params = [])
    {
        $sth = $this->dbh->prepare($sql);
        if ( !$sth->execute($params) ) {
            var_dump($sth->errorInfo());
            throw new \Exception( 'Error execute: ' . $sth->errorInfo() );
        }
        
    }



    /*
     * return last insert id
     */
    public function getLastId()
    {
        if ( !$id = $this->dbh->lastInsertId() ) {
            throw new \Exception( 'Error getLastId' );
        }

        return $id;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Toha
 * Date: 28.03.2017
 * Time: 22:44
 */

namespace News\Model\Base;


use News\MyLib\Pagination;

abstract class Model
{
    protected static $table;

    public $id;



    /**
     * @param array $data
     */
    public function arrayToObject(array $data)
    {
        foreach ($data as $key => $value) {
            if( property_exists($this, $key) ) {
                $this->{$key} = $value;
            }
        }
    }



    /**
     * @param int $id
     * @return object
     * @throws \Exception
     */
    public static function findById($id)
    {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE id = :id';

        $db = Db::getInstance();
        $result = $db->query( $sql, [':id' => $id], static::class );

        if ( empty($result) ) {
            throw new \Exception('Error: findById');
        }

        return array_shift($result);
    }



    /**
     * @param string $order (for sql)
     * @param string $sort (for result array index)
     * @return array
     * @throws \Exception
     */
    public static function findAll($order = 'id', $sort = 'id', $limit = null)
    {
        $sql = 'SELECT * FROM ' . static::$table .
                ' ORDER BY ' .$order. ' DESC'.
                $limit;

        $db = Db::getInstance();
        $result = $db->query($sql, [], static::class, $sort);

        if ( empty($result) ) {
            throw new \Exception('Error: findAll');
        }

        return $result;
    }




    /**
     * @return bool
     * @throws \Exception
     */
    public function checkId()
    {
        $sql = 'SELECT id FROM '. static::$table .
            ' WHERE id = :id';

        $db = Db::getInstance();
        return (bool)$db->query($sql, [':id' => $this->id]);
    }




    /**
     * @return int (count items)
     * @throws \Exception
     */
    public static function totalCounter()
    {
        $sql = 'SELECT COUNT(id) AS cnt FROM '.static::$table;

        $db = Db::getInstance();
        return (int)$db->query($sql)[0]['cnt'];
    }




    /***** CRUD *****/


    /**
     * вставляем данные из объекта в таблицу
     */
    protected function insert()
    {
        // свойства объекта ( 'article' )
        $properties = [];
        // подстановка ( ':article' )
        $binds = [];
        // параметры для передачи ( [':article' => $val] )
        $params = [];

        /*
            INSERT INTO $table(article, date, author)
                VALUES(:article, :date, :author);
        */
        foreach ($this as $key => $val) {
            if ($key === 'id') continue;

            $properties[] = $key;
            $binds[] = ':' . $key;
            $params[':' . $key] = $val;
        }

        $sql = 'INSERT INTO ' . static::$table .
            '('. implode(', ', $properties) .')' .
            ' VALUES('. implode(', ', $binds) . ')';

        $db = Db::getInstance();
        // новый объект заполнен, вставляем данные в БД
        $db->execute($sql, $params);
        // и присваиваем сразу id записи
        $this->id = $db->getLastId();
    }



    protected function update()
    {
        $cols = [];
        $params = [];

        /*
            UPDATE $table SET article = :article, author = :author
                WHERE id = $this->id
        */
        foreach ($this as $key => $val) {
            /* [0] => 'article = :article' */
            $cols[] = $key .'=:'. $key;
            /* [':article'] = $val */
            $params[':' . $key] = $val;
        }

        $sql = 'UPDATE ' . static::$table .
            ' SET ' . implode(', ', $cols) .
            ' WHERE id=' . $this->id;

        $db = Db::getInstance();
        $db->execute($sql, $params);
    }



    public function delete()
    {
        $sql = 'DELETE FROM ' . static::$table .
            ' WHERE id = :id';

        $db = Db::getInstance();
        $db->execute($sql, [':id' => $this->id]);
    }



    public function save()
    {
        if ( empty($this->id) or !$this->checkId() ) {
            $this->insert();
        }
        else {
            $this->update();
        }
    }

}
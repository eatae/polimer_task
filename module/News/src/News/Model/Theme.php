<?php
namespace News\Model;

use News\Model\Base\Db;
use News\Model\Base\Model;

class Theme extends Model
{
    public $theme_title;
    protected static $table = 'Themes';

    public function delete()
    {
        throw new \Exception('Constraint: foreign key in the news table');
    }


    /** Список по темам
     * return array of objects
     * @throws \Exception
     */
    public static function getThemeList()
    {
        $sql = 'SELECT T.theme_title, T.id, count(N.id) as cnt
	              FROM Themes as T, News as N
	              WHERE N.theme_id = T.id
	              GROUP BY T.theme_title, T.id
	              ORDER BY T.theme_title;';

        $db = Db::getInstance();
        $result = $db->query($sql, [], self::class);

        if ( empty($result) ) {
            throw new \Exception('Error: getMonthList empty $result');
        }

        return $result;
    }


}
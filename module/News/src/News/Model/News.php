<?php
namespace News\Model;

use News\Model\Base\Db;
use News\Model\Base\Model;
use Zend\InputFilter\InputFilterInterface;

class News extends Model
{

    public static $monthName = [
        1  => 'январь',
        2  => 'февраль',
        3  => 'март',
        4  => 'апрель',
        5  => 'май',
        6  => 'июнь',
        7  => 'июль',
        8  => 'август',
        9  => 'сентябрь',
        10 => 'октябрь',
        11 => 'ноябрь',
        12 => 'декабрь'
    ];

    protected static $table = 'News';

    /* @var int $id from the Model */
    public $date;
    public $theme_id;
    public $text;
    public $title;



    /** Список по месяцам
     * @return array of arrays
     * @throws \Exception
     */
    public static function getMonthList()
    {
        $sql = 'SELECT YEAR(date) as year, MONTH(date) as month, COUNT(*) as cnt
                    FROM News
                    GROUP BY year, month
                    ORDER BY 1, 2';

        $db = Db::getInstance();
        $result = $db->query($sql);

        if ( empty($result) ) {
            throw new \Exception('Error: getMonthList empty $result');
        }

        /* добавляем название месяца на русском */
        foreach ($result as $key => $arr) {
            $result[$key]['month_name'] = self::$monthName[(int)$arr['month']];
        }

        return $result;
    }


    /** Все записи одного месяца
     * @param $year string
     * @param $month string
     * @return array of objects
     * @throws \Exception
     */
    public static function relatedMonth($year, $month, $limit = null)
    {
        /* номер месяца с нулём */
        if (count($month) === 1) $month = '0'.$month;

        /* промежуток дат */
        $firstDay = date($year .'-'. $month .'-01');
        $lastDay = date('Y-m-d', strtotime($firstDay . "+1 month"));

        $params = array(
            ':firstDay' => $firstDay,
            ':lastDay' => $lastDay,
        );

        $sql = 'SELECT * FROM News
                  WHERE date >= :firstDay AND date < :lastDay
                  ORDER BY date DESC'.
                    $limit;

        $db = Db::getInstance();
        $result = $db->query($sql, $params, self::class);

        if ( empty($result) ) {
            throw new \Exception('Error: relatedMonth empty $result');
        }

        return $result;
    }


    /** Все записи одной темы
     * @param $theme_id
     * @return array
     * @throws \Exception
     */
    public static function relatedTheme($theme_id, $limit = null)
    {
        $sql = 'SELECT * FROM News
                  WHERE theme_id = :theme_id
                  ORDER BY date'.
                    $limit;

        $db = Db::getInstance();
        $result = $db->query($sql, [':theme_id' => $theme_id], self::class);

        if ( empty($result) ) {
            throw new \Exception('Error: relatedTheme empty $result');
        }
        return $result;
    }



    /** Краткий текст новости
     * @return string
     */
    public function clipText()
    {
        $clip = $this->text;
        $clip = iconv_substr($clip, 0, 180);
        $position = iconv_strrpos($clip, ' ');
        // если позиция 0
        $position = $position ?: strlen($clip);
        $clip = iconv_substr($clip, 0, $position) . '...';
        return $clip;
    }

}
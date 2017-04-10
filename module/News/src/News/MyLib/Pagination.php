<?php

namespace News\MyLib;


class Pagination
{
    /**
    * Properties:
    *
    * @var string      $url                String 'REQUEST_URI'
    * @var integer     $totalItems         Number of records in the database - COUNT(id)
    * @var integer     $start              Start position for the query LIMIT
    * @var integer     $interval           Interval for the query LIMIT
    * @var integer     $maxQuantityB       Max number of buttons
    * @var integer     $quantityB          Number of buttons
    * @var integer     $offset             Offset of displayed pages
    * @var boolean     $flag               Offset of displayed pages
    *
    */

    protected $url;
    protected $totalItems;
    protected $start;
    protected $interval;
    protected $maxQuantityB;
    protected $quantityB;
    protected $offset;


    /**
     * Pagination constructor
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $this->cleanUrl($url);
    }



    /**
     * Set Properties
     * --------------
     * @param $totalItems
     * @param $start
     * @param $interval
     * @param int $maxQuantityButton
     */
    public function setProperties($totalItems, $start, $interval, $maxQuantityButton = 5)
    {
        $this->totalItems = (int)$totalItems;
        $this->start = (int)$start ?: 0;
        $this->interval = (int)$interval;
        $this->maxQuantityB = (int)$maxQuantityButton;

        $this->quantityB = $this->createQuantityB();
        $this->offset = $this->createOffset();
    }


    /**
     * @return int
     * @throws \Exception
     */
    protected function createOffset()
    {
        if ( empty($this->quantityB) || empty($this->interval) ) {
            $e = self::class .'::'. __METHOD__ .' - empty required properties.';
            throw new \Exception($e);
        }
        // умножаем кол-во кнопок на интервал
        // интервал делим на полученное число от умножения
        // округляем в меньшую сторону - это и будет офсет (0, 1, 2...)
        $offset = (int)floor( $this->start / ($this->quantityB * $this->interval) );
        return $offset;
    }


    /**
     * @return int
     * @throws \Exception
     */
    protected function createQuantityB()
    {
        if ( empty($this->totalItems) || empty($this->interval) ) {
            $e = self::class .'::'. __METHOD__ .' - empty required properties.';
            throw new \Exception($e);
        }

        $pages = (int) ceil($this->totalItems / $this->interval);

        if ($pages > $this->maxQuantityB) {
            $pages = $this->maxQuantityB;
        }
        return $pages;
    }



    /**
     * Clean URL
     * ---------
     * @param string $url
     * @return string
     */
    protected function cleanUrl($url)
    {
        if ( (false == strpos($url, '?')) || ($pos = strpos($url, '?start_limit')) ) {
            $delimiter = '?';
            if ($pos) {
                $url = substr($url, 0, $pos);
            }
        }
        else {
            $delimiter = '&';
            if ($pos = strpos($url, '&start_limit')) {
                $url = substr($url, 0, $pos);
            }
        }
        return $url .= $delimiter;
    }


    /**
     * @return string
     */
    public function getSqlLimit()
    {
        $string = ' LIMIT '. $this->start .','. $this->interval;
        return $string;
    }


    /**
     * @param string $url
     * @return null|string
     * @throws \Exception
     */
    public function buildPagination()
    {
        if (!isset($this->offset)) {
            throw new \Exception('buildPagination: property offset empty');
        }
        if ( $this->totalItems <= $this->interval ) {
            return null;
        }

        /* indent */
        function i($quantity)
        {
            $string = "\n\t";
            for ($cnt = 1; $cnt < $quantity; $cnt++){
                $string .= "\t";
            }
            return $string;
        }


        /* $offset = (int)floor( $this->start / ($this->quantityB * $this->interval) ); */
        $count = $this->interval * $this->offset;
        $countEnd = $count + $this->quantityB;

        /* PREV */
        $start = ($count * $this->interval) - $this->interval;
        $rout = $this->url . 'start_limit=' . $start;
        $class = '';

        if (0 > $start) {
            $class = 'class="disabled"';
            $rout = '#';
        }

        $html = '<nav aria-label="Page navigation">'.i(1).
                    '<ul class="pagination">'.i(2).
                        '<li '. $class .'>'.i(3).
                            '<a href="'. $rout .'" aria-label="Previous">'.i(4).
                                '<span aria-hidden="true">&laquo;</span>'.i(3).
                            '</a>'.i(2).
                        '</li>'.i(2);

        /* BUTTONS */
        for ($cnt = $count; $cnt < $countEnd; $cnt++)
        {
            $start = $cnt * $this->interval;
            /* NOT FINISH $rout */
            $rout = $this->url . 'start_limit='. $start;
            $class = ($start == $this->start) ? 'class="active"' : '';

            if ($this->totalItems <= $start) {
                break;
            }

            $html .= "<li $class><a href='$rout'>" . ($cnt + 1) . '</a></li>'.i(2);
        }

        /* NEXT */
        $start += $this->interval;
        //$class = ($this->totalItems <= $start) ? 'class="disabled"' : '';

        $rout = $this->url . 'start_limit=' . $start;
        $class = '';

        if ($this->totalItems <= $start) {
            $class = 'class="disabled"';
            $rout = '#';
        }

        $html .= '<li '. $class .'>'.i(3).
                    '<a href="'. $rout .'" arial-label="Next">'.i(4).
                        '<span arial-hidden="true">&raquo;</span>'.i(3).
                    '</a>'.i(2).
                 '</li>'.i(1).
                '</ul>'. "\n" .
            '</nav>';

        return $html;
    }

}
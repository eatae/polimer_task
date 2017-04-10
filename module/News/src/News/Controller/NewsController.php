<?php

namespace News\Controller;

use News\MyLib\Filter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use News\Model\News;
use News\Model\Theme;
use News\MyLib\Pagination;


class NewsController extends AbstractActionController
{


    /**Все новости
     *------------
     * @return array
     * @throws \Exception
     */
    public function indexAction()
    {
        // получаем и проверяем GET
        $params = $this->params()->fromQuery();
        Filter::checkFilter('getFilterIndexLimit', $params);

        // всего записей
        $totalItems = News::totalCounter();

        $pagination = $this->createPaginate(new Pagination($_SERVER['REQUEST_URI']), $totalItems);
        // sql LIMIT
        $sqlLimit = $pagination->getSqlLimit();
        // новости
        $news = News::findAll('date', 'id', $sqlLimit);
        // html pagination
        $htmlPaginate = $pagination->buildPagination();

        return [
            'title' => 'Все новости',
            'news' => $news,
            'themes' => Theme::getThemeList(),
            'byMonth' => News::getMonthList(),
            'htmlPaginate' => $htmlPaginate,
        ];
    }


    /** Новости за месяц
     *------------------
     * template: index.phtml
     * @return ViewModel
     * @throws \Exception
     */
    public function byMonthAction()
    {
        // получаем и проверяем GET
        $params = $this->params()->fromQuery();
        Filter::checkFilter('getFilterByMonth', $params);

        // всего записей
        $totalItems = $params['cnt'];

        $pagination = $this->createPaginate(new Pagination($_SERVER['REQUEST_URI']), $totalItems);
        // sql LIMIT
        $sqlLimit = $pagination->getSqlLimit();

        // получаем название месяца
        $month_name = News::$monthName[(int)$params['month']];

        // формируем title
        $title = 'Новости за '.$month_name;
        // массив новостей
        $news = News::relatedMonth($params['year'], $params['month'], $sqlLimit);
        // html pagination
        $htmlPaginate = $pagination->buildPagination();

        // список новостей по темам
        $themes = Theme::getThemeList();
        // список новостей по месяцу
        $byMonth = News::getMonthList();

        // передаём во view
        $view = new ViewModel([
            'title' => $title,
            'news'  => $news,
            'themes' => $themes,
            'byMonth' => $byMonth,
            'htmlPaginate' => $htmlPaginate,
        ]);

        // устанавливаем шаблон index
        $view->setTemplate('news/news/index');

        return $view;
    }


    /** Новости по теме
     *-----------------
     * template: index.phtml
     * @return ViewModel
     * @throws \Exception
     */
    public function byThemeAction()
    {
        // получаем и проверяем GET
        $params = $this->params()->fromQuery();
        Filter::checkFilter('getFilterByTheme', $params);

        // всего записей
        $totalItems = $params['cnt'];
        // объект пагинации
        $pagination = $this->createPaginate(new Pagination($_SERVER['REQUEST_URI']), $totalItems);
        // sql LIMIT
        $sqlLimit = $pagination->getSqlLimit();


        // список новостей по темам
        $themes = Theme::getThemeList();

        // получаем название темы
        $theme_name = $themes[(int)$params['theme_id']]->theme_title;
        // формируем title
        $title = 'Новости темы '.$theme_name;
        // массив новостей
        $news = News::relatedTheme($params['theme_id'], $sqlLimit);
        // html pagination
        $htmlPaginate = $pagination->buildPagination();

        // список новостей по месяцу
        $byMonth = News::getMonthList();

        // передаём во view
        $view = new ViewModel([
            'title' => $title,
            'news'  => $news,
            'themes' => $themes,
            'byMonth' => $byMonth,
            'htmlPaginate' => $htmlPaginate,
        ]);

        // устанавливаем шаблон index
        $view->setTemplate('news/news/index');

        return $view;
    }



    /** One News
     *-----------
     * @return array
     * @throws \Exception
     */
    public function oneNewsAction()
    {
        $id = $this->params()->fromRoute('id');
        return [
            //'x' => $this->url('news', ['action' => 'index']),
            'themes' => Theme::getThemeList(),
            'news'   => News::findById($id)
        ];
    }




    /* PAGINATE
     *---------
    */

    /**Create Paginate
     *----------------
     * @param Pagination $p
     * @param $totalItems
     * @return Pagination
     */
    public function createPaginate(Pagination $p, $totalItems)
    {
        // стартовая позиция выборки
        $start = $this->params()->fromQuery('start_limit') ?: 0;
        // интервал выборки (по сколько записей на странице)
        $interval = 3;
        // максимальное кол-во показа кнопок
        $maxQuantityButton = 3;
        // заполняем значения
        $p->setProperties($totalItems, $start, $interval, $maxQuantityButton);

        return $p;
    }



    public function testAction()
    {

        return [
//            'c' => new \StdClass,
        ];
    }


}
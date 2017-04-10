<?php

namespace News\Controller;

use News\MyLib\Filter;
use News\MyLib\Pagination;
use News\MyLib\UserException;
use Zend\Mvc\Controller\AbstractActionController;
use News\Model\News;
use News\Model\Theme;
use Zend\Http\Request;


class NewsAdminController extends AbstractActionController
{

    public function indexAction()
    {
        $params = $this->params()->fromQuery();
        Filter::checkFilter('getFilterIndexLimit', $params);

        /* Pagination */
        $pagination = new Pagination($_SERVER['REQUEST_URI']);
        $totalItems = News::totalCounter();
        $start = $this->params()->fromQuery('start_limit') ?: 0;
        $interval = 10;
        $maxQuantityButton = 5;
        $pagination->setProperties($totalItems, $start, $interval, $maxQuantityButton);
        $limit = $pagination->getSqlLimit();
        $htmlPaginate = $pagination->buildPagination();


        $news = News::findAll('date', 'id', $limit);
        $theme = Theme::findAll();


        return [
            'title' => '',
            'themes' => $theme,
            'news' => $news,
            'htmlPaginate' => $htmlPaginate,
        ];
    }




    public function addAction()
    {
        try {
            $themes = Theme::findAll();
            $request = $this->getRequest();

            if ($request->isPost()) {

                $post = $request->getPost();

                // кнопка назад
                if ($post['button'] == 'back') {
                    return $this->redirect()->toRoute('news-admin');
                }

                // получаем theme_id
                foreach ($themes as $obj) {
                    if ($obj->theme_title == $post['theme_title']) {
                        $post['theme_id'] = $obj->id;
                    }
                }

                // проверка даты
                if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $post['date'])) {
                    throw new UserException('Неверный формат даты.');
                }

                // проверка остальных полей
                Filter::checkFilter('filterAdminNews', $post, 'user');

                // создаём и заполняем экземпляр из поста
                $news = new News();
                $news->arrayToObject((array)$post);

                $news->save();

                return $this->redirect()->toRoute('news-admin');
            }

            return [
                'theme' => $themes,
                'title' => 'Добавление новости',
            ];

        } catch (UserException $e) {
            return ['except' => $e->getMessage()];
        }

    }



    public function editAction()
    {
        try {
            $themes = Theme::findAll();
            $id = $this->params()->fromRoute('id');
            Filter::checkFilter('filterId', compact('id'), 'user');
            $news = News::findById($id);

            $request = $this->getRequest();

            if ( $request->isPost() ) {

                $post = $request->getPost();

                // кнопка назад
                if ($post['button'] == 'back') {
                    return $this->redirect()->toRoute('news-admin');
                }

                // получаем theme_id
                foreach ($themes as $obj) {
                    if ($obj->theme_title == $post['theme_title']) {
                        $post['theme_id'] = $obj->id;
                    }
                }

                // проверка даты
                if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $post['date'])) {
                    throw new UserException('Неверный формат даты.');
                }

                // проверка остальных полей
                Filter::checkFilter('filterAdminNews', $post, 'user');

                // заполняем экземпляр из поста
                $news->arrayToObject((array)$post);

                $news->save();

                return $this->redirect()->toRoute('news-admin');
            }


            return [
                'news' => $news,
                'themes' => $themes,
                'title' => 'Изменение новости',
            ];

        } catch (UserException $e) {
            return ['except' => $e->getMessage()];
        }

    }





    public function deleteAction()
    {
        try {
            // получаем новость
            $id = $this->params()->fromRoute('id');
            Filter::checkFilter('filterId', compact('id'), 'user');
            $news = News::findById($id);

            $request = $this->getRequest();

            if ($request->isPost()) {
                $post = $request->getPost();

                if ($post['button'] == 'del') {
                    $news->delete();
                    return $this->redirect()->toRoute('news-admin');
                }
                else {
                    return $this->redirect()->toRoute('news-admin');
                }

            }

            return [
                'themes' => Theme::getThemeList(),
                'news'   => $news,
        ];
        } catch (UserException $e) {
            return ['except' => $e->getMessage()];
        }
    }


}
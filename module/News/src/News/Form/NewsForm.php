<?php
namespace News\Form;

use Zend\Form\Form;

class NewsForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->add(
            [
            'name' => 'id',
            'type' => 'Hidden'
            ]
        );
        $this->add(
            [
            'name' => 'title',
            'type' => 'Text',
            'options' => [
                'label' => 'Заголовок: '
                ]
            ]
        );
        $this->add(
            [
            'name' => 'date',
            'type' => 'Text',
            'options' => [
                'label' => 'Дата: '
                ]
            ]
        );
        $this->add(
            [
            'name' => 'theme',
            'type' => 'Text',
            'options' => [
                'label' => 'Тема: '
                ]
            ]
        );
        $this->add(
            [
            'name' => 'theme_id',
            'type' => 'Hidden'
            ]
        );
        $this->add(
            [
            'name' => 'text',
            'type' => 'Text',
            'options' => [
                'label' => 'Содержание: '
                ]
            ]
        );

    }
}
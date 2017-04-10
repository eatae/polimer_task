<?php

namespace News\MyLib;


use News\MyLib\UserException;
use \Exception as Exception;
use Zend\InputFilter\Factory;
use News\MyLib\Pagination;

class Filter
{
    const EXC_PATH = 'News\MyLib\\';

    /**
     * @param string $name
     * @param $params
     * @param string $except
     * @return bool
     * @throws \Exception
     */
    public static function checkFilter($name, $params, $except = 'Exception')
    {
        if (!is_string($name) || !method_exists(self::class, $name)) {
            throw new \Exception ('Filters::__construct incorrect param $name');
        }

        // отправляем в фильтр
        $filter = self::$name()->setData($params);
        $str = '';

        // проверяем валидность
        if ( !$filter->isValid() ) {

            foreach ($filter->getInvalidInput() as $key => $error) {
                $str .= ' | ' . $key . ' | ' .implode(', ', $error->getMessages());
            }

            // Динамическое имя Exception
            if ('Exception' != $except) {
                $except = ucfirst($except);
                $except = self::EXC_PATH . $except . 'Exception';
                if (!class_exists($except)) {
                    throw new \Exception('Not found class: '.$except);
                }
            }
            throw new $except('Неверно введены данные ' . $str);
        }
        return true;
    }


    /** GET by month
     *---------------------
     *  NewsController::byMonthAction
     *
     * @return \Zend\InputFilter\InputFilterInterface
     */
    protected static function getFilterByMonth()
    {
        $factory = new Factory();

        $inputFilter = $factory->createInputFilter(array(
            'year' => array(
                'name' => 'year',
                'required' => true,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 4, 'max' => 4]
                    )
                )
            ),
            'month' => array(
                'name' => 'month',
                'required' => true,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 2]
                    )
                )
            ),
            'cnt' => array(
                'name' => 'cnt',
                'required' => false,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 4]
                    )
                )
            ),
            'start_limit' => array(
                'name' => 'start_limit',
                'required' => false,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 2]
                    )
                )
            ),
        ));

        return $inputFilter;
    }


    /** GET by theme
     *----------------------
     *  NewsController::byThemeAction
     *
     * @return \Zend\InputFilter\InputFilterInterface
     */
    protected static function getFilterByTheme()
    {
        $factory = new Factory();

        $inputFilter = $factory->createInputFilter(array(
            'theme_id' => array(
                'name' => 'theme_id',
                'required' => true,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 2]
                    )
                )
            ),
            'cnt' => array(
                'name' => 'cnt',
                'required' => false,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 4]
                    )
                )
            ),
            'start_limit' => array(
                'name' => 'start_limit',
                'required' => false,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 2]
                    )
                )
            )
        ));
        return $inputFilter;
    }


    /** GET start_limit
     *------------------------
     *  NewsController::indexAction
     *  NewsAdminController::indexAction
     *
     * @return \Zend\InputFilter\InputFilterInterface
     */
    protected static function getFilterIndexLimit()
    {
        $factory = new Factory();

        $inputFilter = $factory->createInputFilter(array(
            'start_limit' => array(
                'name' => 'start_limit',
                'required' => false,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 2]
                    )
                )
            )
        ));
        return $inputFilter;
    }




    /** Admin News
     *-----------------------
     *  NewsAdminController::addAction
     *  NewsAdminController::editAction
     *
     * @return \Zend\InputFilter\InputFilterInterface
     */
    protected static function filterAdminNews()
    {
        $factory = new Factory();

        $inputFilter = $factory->createInputFilter(array(
                'id' => array(
                    'name' => 'theme_id',
                    'required' => false,
                    'validators' => array(
                        array('name' => 'not_empty'),
                        array(
                            'name' => 'string_length',
                            'options' => ['min' => 1, 'max' => 10]
                        )
                    )
                ),
                'theme_title' => array(
                    'name' => 'theme_title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'not_empty'),
                        array(
                            'name' => 'string_length',
                            'options' => array(
                                'min' => 2,
                                'max' => 40
                            )
                        )
                    )
                ),
                'theme_id' => array(
                    'name' => 'theme_title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'not_empty'),
                        array(
                            'name' => 'string_length',
                            'options' => array(
                                'min' => 1,
                                'max' => 2
                            )
                        )
                    )
                ),
                'title' => array(
                    'name' => 'title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'not_empty'),
                        array(
                            'name' => 'string_length',
                            'options' => array(
                                'min' => 2,
                                'max' => 200
                            )
                        )
                    )
                ),
                'date' => array(
                    'name' => 'date',
                    'required' => true,
                    'filters' => array(
                            ['name' => 'DateSelect']
                    ),
                    'validators' => array(
                        array('name' => 'not_empty'),
                        array(
                            'name' => 'date',
                        ),
                    )
                ),
                'text' => array(
                    'name' => 'text',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'not_empty'),
                        array(
                            'name' => 'string_length',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 2,
                                'max' => 500
                            )
                        )
                    )
                ),
            )
        );
        return $inputFilter;
    }



    protected static function filterId()
    {
        $factory = new Factory();

        $inputFilter = $factory->createInputFilter(array(
            'id' => array(
                'name' => 'id',
                'required' => true,
                'validators' => array(
                    array('name' => 'not_empty'),
                    array(
                        'name' => 'string_length',
                        'options' => ['min' => 1, 'max' => 10]
                    )
                )
            )
        ));
        return $inputFilter;
    }


}
<?php
namespace More;

/**
 * Формирование списка страниц для постраничной навигации.
 * @property string limit SQL значение для LIMIT
 * @property int start Индекс первого пункта на странице
 * @property int end Индекс последнего пункта на странице
 * @property int items_per_page Кол-во пунктов на страницу
 * @property int pages Кол-во страниц всего
 * @property int this_page Текущая страница
 */
class pages
{

    public $pages = 0; // количество страниц
    public $this_page = 1; // текущая страница

    protected $_items_count = 0; // количество пунктов всего
    protected $_items_per_page = 10; // количество пунктов на одну страницу

    /**
     * @param int $items_count Кол-во пунктов
     */
    function __construct($items_count = 0)
    {
        $this->posts = $items_count;
    }

    /**
     * Рассчет кол-ва страниц и текущей страницы
     */
    protected function _recalcPage()
    {
        if (!$this->_items_count) {
            $this->pages = 1;
        } else {
            $this->pages = ceil($this->_items_count / $this->_items_per_page);
        }

        if (self::getThisPage()) {
            if (self::getThisPage() == 'end') {
                $this->this_page = $this->pages;
            } elseif (is_numeric(self::getThisPage())) {
                $this->this_page = max(1, min($this->pages, intval(self::getThisPage())));
            } else {
                $this->this_page = 1;
            }
        } elseif (isset($_GET['postnum'])) {
            if ($_GET['postnum'] == 'end') {
                $this->this_page = $this->pages;
            } elseif (is_numeric($_GET['postnum'])) {
                $this->this_page = max(1, min($this->pages, ceil($_GET['postnum'] / $this->_items_per_page)));
            } else {
                $this->this_page = 1;
            }
        } else {
            $this->this_page = 1;
        }
    }

    /**
     * Для подстановки в MYSQL LIMIT
     */
    function limit(): string
    {
        return $this->my_start() . ', ' . $this->_items_per_page;
    }

    /**
     * старт извлечения из базы
     */
    function my_start(): string
    {
        return $this->_items_per_page * ($this->this_page - 1);
    }

    /**
     * конец
     */
    function end(): int
    {
        return $this->_items_per_page * $this->this_page;
    }
    function __set($name, $value)
    {
        switch ($name) {
            case 'posts':
                $this->_items_count = $value;
                break;
            case 'items_per_page':
                $this->_items_per_page = $value;
                break;
        }
        $this->_recalcPage();
    }

    function __get($name)
    {
        switch ($name) {
            case 'items_per_page':
                return $this->_items_per_page;
            case 'limit':
                return $this->limit();
            case 'my_start':
                return $this->my_start();
            case 'end':
                return $this->end();
            case 'posts':
                return $this->_items_count;
        }
    }
    /*
     * Получаем текущую страницу
     */
    static function getThisPage(): int
    {
        $this_page = (int) preg_replace('/^.+page([0-9]+).*/i', '$1', $_SERVER['REQUEST_URI']);
        return $this_page ? $this_page : 1;
    }
    /*
     * Получаем адрес ссылки для вставки в пагинацию
     */
    static function getLink(): string
    {
        return preg_replace('/^(.+\/)page.*/i', '$1', $_SERVER['REQUEST_URI']) ?? '/';
    }
}

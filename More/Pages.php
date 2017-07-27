<?php
namespace More;

class Pages
{
    /*
     * Получаем текущую страницу
     */
    static function getThisPage()
    {
        $this_page = (int) preg_replace('/^.+page([0-9]+).*/i', '$1', $_SERVER['REQUEST_URI']);
        return $this_page ? $this_page : 1;
    }
    /*
     * Получаем адрес ссылки для вставки в пагинацию
     */
    static function getLink()
    {
        return preg_replace('/^(.+\/)page.*/i', '$1', $_SERVER['REQUEST_URI']) ?? '/';
    }
}

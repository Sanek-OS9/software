<?php
namespace Core;

use \More\{Text,Pages};
use \Core\{Twig,App};
use \Models\Software;

class Controller{
    protected $params = [];
    protected $template_dir = 'software';

    public function __construct()
    {
        # если по какой либо причине константа не опредена
        # например этот файл не был открыв в корневом index.php
        # то ничего не должно работать, защита 80lvl =)
        if (!defined('SOFTWARE_ACCESS')) {
            App::access_denied('ACCESS DENIED');
        }
        $this->_inicializationParams();
    }

    protected function access_denied(string $msg)
    {
        $this->params['message'] = $msg;
        $this->display('access_denied');
        exit;
    }
    private function _inicializationParams()
    {
        # статистика количества файлов
        $this->params['statistic'] = Software::getCountPlatform();

    }
    protected function display(string $filename)
    {
        $this->params['server_name'] = $_SERVER['SERVER_NAME'];

        $loader = new \Twig_Loader_Filesystem(H . '/Views/' . $this->template_dir);
        $twig = new \Twig_Environment($loader);

        $function = new \Twig_Function('toOutput', function (string $text) {
          return Text::toOutput($text);
        });
        $twig->addFunction($function);

        $function = new \Twig_Function('linkSorting', function (string $sort, string $link) {
            $sortings = 'views|new|update|popular';
            if (!preg_match('/' . $sortings . '/i', $link)) {
                $link = preg_replace('#page.+#', '', $link);
                return $link . 'sort-by/' . $sort . '/page1/';
            }
            $link = preg_replace('#page.+#', 'page1/', $link);
            return preg_replace('(' . $sortings . ')', $sort, $link);
        });
        $twig->addFunction($function);


        $template = $twig->loadTemplate('/' . $filename . '.twig');
        echo $template->render($this->params);
    }

    protected function pagesDisplay(int $pages)
    {
        $this->params['pages_all'] = $pages;
        $this->params['pages_this'] = Pages::getThisPage();
        $this->params['pages_link'] = Pages::getLink();
    }
}

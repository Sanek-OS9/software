<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\Modules\torrent\{Parsing,Software};
use \Models\Sitemap;
use \More\Pages;
use \Libraries\R;

/*
innertext - Читает или записывает внутренний HTML элемента
outertext - Читает или записывает весь HTML элемента, включая его самого.
plaintext - Читает или записывает простой текст элемента, это эквивалентно функции strip_tags($e->innertext).
*/

class RutrackerController extends Controller{
    protected $template_dir = 'torrent';

    public function actionRandom()
    {
        $links = [];
        $files = R::findAll('torrent', 'ORDER BY `id` ASC LIMIT 200');
        foreach ($files as $file) {
            $links[] = 'http://' . $_SERVER['HTTP_HOST'] . '/torrent/' . $file['name'] . '.htm';
        }
        $key = mt_rand(0, count($links) - 1);
        header('Location: ' . $links[$key]);
        exit;
    }
    public function actionSitemap()
    {
        $links = Software::getFilesViewLinks();

        $sitemap = new Sitemap();
        $sitemap->setLinks($links);
        $sitemap->save('torrent');
    }
    # просмотр файла, файл берется непосредственно с нашей базы данных
    public function actionFile(string $filename)
    {
        //$_SESSION['test'] = true;
        if (isset($_SESSION['test'])) {
            $second = mt_rand(5, 15);
            $newfile = R::findOne('torrent', 'ORDER BY rand()');
            header('Refresh: ' . $second . '; /torrent/' . $newfile['name'] . '.htm');
        }
        $file = R::findOne('torrent', '`name` = ?', [$filename]);
        if (!isset($file->id)) {
            App::access_denied('Файл не найден');
        }
        $this->params['title'] = $file->runame;
        $this->params['navigation'] = Parsing::getNavigation();
        $this->params['file'] = $file;
        $this->display('rutracker/file');
    }
    public function actionIndex()
    {
        $files = R::findAll('torrent', 'LIMIT 16');

        $this->params['navigation'] = Parsing::getNavigation();
        $this->params['files'] = $files;

        $this->params['title'] = 'Скачайте файлы с торрента бесплатно';
        $this->display('rutracker/category');
    }
    # парсер просмотра файлов в каталоге с сайта rutracker.co.ua
    # после парсинга они все запишутся на наш сервер и братся уже будут с него
    public function actionCategory()
    {
        # принимаем переданные параметры и убираем пустые значения
        list($platform, $genre, $number, $sort, $page) = func_get_args();

        $rutracker = new Parsing('/load/' . $platform . '/' . $genre . '/' . $number . '-' . Pages::getThisPage() . '-' . $sort);
        $rutracker->platform = $platform;
        $rutracker->number = $number;
        $rutracker->sort = $sort;
        $rutracker->page = $page;
        $rutracker->genre = $genre;

        $this->params['breadcrumbs'] = $rutracker->getBreadCrumbs();

        $this->params['files'] = $rutracker->getAllFiles();
        $this->pagesDisplay($rutracker->countPages()); // показ.пагинации
        if (!$this->params['files']) {
            if ($genre) {
                $pages = new Pages(R::count('torrent', '`platform` = ? AND `genre` = ?', [$platform, $genre]));
                $files = R::findAll('torrent', '`platform` = ? AND `genre` = ? LIMIT ' . $pages->limit, [$platform, $genre]);
            } else {
                $pages = new Pages(R::count('torrent', '`platform` = ?', [$platform]));
                $files = R::findAll('torrent', '`platform` = ? LIMIT ' . $pages->limit, [$platform]);
            }
            $this->pagesDisplay($pages->pages); // показ.пагинации
            $this->params['files'] = $files;
        }

        $this->params['sorting'] = $rutracker->getSorting();
        $this->params['sort'] = $sort;
        $this->params['title'] = $rutracker->getTitle();
        $this->params['navigation'] = $rutracker->getNavigation();
        if (!$this->params['navigation']) {
            $rutracker->setNavigation();
        }

        $this->display('rutracker/category');
    }
}

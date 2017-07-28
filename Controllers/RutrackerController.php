<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Software,Rutracker,Sitemap};
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
        $files = R::findAll('torrent', 'ORDER BY `id` ASC LIMIT 200');
        foreach ($files as $file) {
            echo 'http://' . $_SERVER['HTTP_HOST'] . '/torrent/' . $file['name'] . '.htm<br />';
        }
    }
    public function actionSitemap()
    {
        $file_links = [];
        $files = R::findAll('torrent');
        foreach ($files as $file) {
            $file_links[] = '/torrent/' . $file['name'] . '.htm';
        }
        $sitemap = new Sitemap();
        $sitemap->setLinks($file_links);
        $sitemap->save('torrent');

        $_SESSION['test'] = true;
    }
    # просмотр файла, файл берется непосредственно с нашей базы данных
    public function actionFile(string $filename)
    {
        // require_once H . '/Snoopy.class.php';
        // $snoopy = new \Snoopy(); // создаём объект
        // $snoopy->cookies["sid"] = '1389345741879739';
        // //$snoopy->cookies["user_id"] = "4905972";
        // $snoopy->proxy = "147.135.209.190:1080";
        // $snoopy->referer = 'https://www.google.com.ua/webhp?hl=ru&sa=X&ved=' . bin2hex(random_bytes(36));
        // $snoopy->agent = "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";
        // //$snoopy->rawheaders["Host"] = "google.com.ua";
        // $snoopy->submit('http://torrent-freelax.net/test.php', ['pn_nums' => '2363']); // отправляем форму
        // $page = $snoopy->results; // выводим результат
        // $text = str_get_html($page);
        // $array = $text->find('a');
        // foreach($array AS $link){
        //     echo $link->plaintext . '<br />';
        // }
        // printr(json_decode($page));
        // exit;
        $file = R::findOne('torrent', '`name` = ?', [$filename]);
        if (!isset($file->id)) {
            App::access_denied('Файл не найден');
        }
        $this->params['title'] = $file->runame;
        $this->params['navigation'] = Rutracker::getNavigation();
        $this->params['file'] = $file;
        $this->display('rutracker/file');
    }
    public function actionIndex()
    {
        $files = R::findAll('torrent', 'LIMIT 16');

        // $rutracker = new Rutracker('/load/');
        // $rutracker->base_add = false;
        $this->params['navigation'] = Rutracker::getNavigation();
        $this->params['files'] = $files;

        $this->params['title'] = 'Скачайте файлы с торрента бесплатно';
        $this->display('rutracker/category');
    }
    # парсер просмотра файлов в каталоге с сайта rutracker.co.ua
    # после парсинга они все запишутся на наш сервер и братся уже будут с него
    public function actionCategory()
    {
        # принимаем переданные параметры и убираем пустые значения
        list($platform, $number, $sort, $page, $genre) = func_get_args();

        $rutracker = new Rutracker('/load/' . $platform . '/' . $genre . '/' . $number . '-' . Pages::getThisPage() . '-' . $sort);
        $rutracker->platform = $platform;
        $rutracker->number = $number;
        $rutracker->sort = $sort;
        $rutracker->page = $page;
        $rutracker->genre = $genre;
        $this->params['files'] = $rutracker->getAllFiles();
        $this->params['sorting'] = $rutracker->getSorting();
        $this->params['sort'] = $sort;
        $this->params['title'] = $rutracker->getTitle();
        $this->params['navigation'] = $rutracker->getNavigation();
        if (!$this->params['navigation']) {
            $rutracker->setNavigation();
        }
        $this->pagesDisplay($rutracker->countPages()); // показ.пагинации
        $this->display('rutracker/category');
    }
}

<?php
namespace Controllers;

use \Core\{Controller,DB,R,App};
use \Models\{Software,Sitemap};
use \More\Ini;

class MainController extends Controller{

    /*
     * Проводим тесты
     */
    public function actionTest()
    {
        include_once H . '/test.php';
    }
    /*
     * Главная страница сайта
     */
    public function actionIndex()
    {
        $this->params['files']['ios'] = Software::getFiles('smartphone', 'ios', 6);
        $this->params['files']['android'] = Software::getFiles('smartphone', 'android', 6);
        $this->params['files']['psp'] = Software::getFiles('smartphone', 'psp', 4);
        $this->params['files']['windows'] = Software::getFiles('smartphone', 'windows', 4);
        $this->display('main/index');
    }
    /*
     * Поиск файлов
     */
    public function actionSearch(string $search)
    {
        $search = urldecode($search);
        $this->params['search'] = $search;
        $this->params['files'] = Software::getSearchFiles('smartphone', $search);
        $this->display('main/search');

    }
    public function actionSearchajax(string $search)
    {
        $search = urldecode($search);
        if ($files = Software::getSearchFiles('smartphone', $search, true, 15)) {
            echo json_encode($files);
        }
    }
    /*
     * Генерируем sitemap.xml
     */
    public function actionSitemap()
    {
        $file_links = Software::getFilesViewLinks();

        $sitemap = new Sitemap();
        $sitemap->setLinks($file_links);
        $sitemap->save();

        $_SESSION['test'] = true;
        $this->params['file_links'] = sizeof($file_links);
        $this->display('main/sitemap');
    }
    /*
     * Показываем скриншот
     */
    public function actionScreen(int $id_user, int $id_file, string $screen)
    {
        header("Content-type: image/jpeg");
        readfile('https://pdacdn.com/userfiles/screens/' . $id_user . '/' . $id_file . '/' . $screen . '.jpg');
    }
}

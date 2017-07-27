<?php
namespace Controllers;

use \Core\{Controller,DB,R,App};
use \Models\{Software,Sitemap};
use \More\Ini;

class MainController extends Controller{
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
}

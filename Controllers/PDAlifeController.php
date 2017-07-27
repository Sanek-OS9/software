<?php
namespace Controllers;

use \Core\{Controller,App};
use \More\Pages;
use \Models\{PDAlife,FilePDAlife};

class PDAlifeController extends Controller{

    public function actionRandom()
    {
        $files = R::findAll('smartphone', 'LIMIT 200');
        $links = [];
        foreach ($files as $v) {
            $file = new FilePDAlife('smartphone', $v['path']);
            $links[] = 'http://скачай-ка.рф' . $file->link_view;
        }
        $id = mt_rand(0, count($links));
        header('Location: ' . $links[$id]);
    }
    # просмотр файла
    public function actionView(string $file_path)
    {
        $file = new FilePDAlife($file_path);
        if (!isset($file->id)) {
            App::access_denied('Файл не найден');
        }
        $this->params['title'] = $file->runame;
        $this->params['file'] = $file;
        $this->display('pdalife/view');
    }
    # просмотр файлов по тегам
    public function actionTag(string $tag, string $page = 'page1')
    {
        $pdalife = new PDAlife('https://pdalife.ru/tag/' . $tag . '/' . $page . '/');

        $this->params['files'] = $pdalife->getAllFiles();
        $this->params['title'] = $pdalife->getTitle();
        $this->params['link'] = '/' . $tag;

        $pages = $pdalife->getPages();
        if ($pages < Pages::getThisPage()) {
            $pages = Pages::getThisPage();
        }

        $this->pagesDisplay($pages);
        $this->display('pdalife/tag');
    }
    # просмотр списка файлов
     public function actionFiles()
     {
        # принимаем переданные параметры и убираем пустые значения
        $segments = App::clear_array(func_get_args());
        #list($platform, $genre, $sort_str, $sort, $page) = func_get_args();
        # формируем ссылку которую будем парсить
        $link = '/' . implode('/', $segments) . '/';

        if (isset($_SESSION['test'])) {
            $second = mt_rand(1, 8);
            //header('Refresh: ' . $second . '; /smartphone/' . $platform . '/page' . (Pages::getThisPage() + 1) . '/');
        }
        $pdalife = new PDAlife('http://pdalife.ru' . $link);

        $this->params['files'] = $pdalife->getAllFiles();
        $this->params['title'] = $pdalife->getTitle();
        $this->params['link'] = $link;

        $pages = $pdalife->getPages();
        if ($pages < Pages::getThisPage()) {
            $pages = Pages::getThisPage();
        }
        $this->pagesDisplay($pages);
        $this->display('pdalife/files');
     }
     # поиск файлов
     public function actionSearch(string $search)
     {
         $search = urldecode($search);
         $this->params['search'] = $search;
         $this->params['files'] = Software::getSearchFiles('smartphone', $search);
         $this->display('main/search');

     }
     # поиск файлов для ajax
     public function actionSearchajax(string $search)
     {
         $search = urldecode($search);
         if ($files = Software::getSearchFiles('smartphone', $search, true, 15)) {
             echo json_encode($files);
         }
     }
    # подменяем вывод скриншота со стороннего сайта, делаем вывод с нашего сайта
    public function actionScreen(int $id_user, int $id_file, string $namescreen)
    {
        $image_url = 'https://pdacdn.com/userfiles/screens/' . $id_user . '/' . $id_file . '/' . $namescreen . '.jpg';
        header("Content-type: image/jpeg");
        readfile($image_url);
    }
    # загрузка файла
    public function actionDownload(string $code)
    {
        # отправляем код (POST запросом)
        $pdalife = new PDAlife('http://mobdisc.com/get_key/', ['forms' => ['dwn' => $code]]);
        # принимаем ответ
        # @status - статус ответа (ok - все хорошо, error - что то пошло не так)
        # @link - прямая ссылка на скачивание (если статус == ok)
        # @msg - сообщение (если статус == error)
        $data = json_decode($pdalife->loadPage(), true);
        if ($data['status'] == 'error') {
            App::access_denied('Файл не найден');
        }
        $link = 'https://mobdisc.com' . $data['link'];
        # временно отдаем файл с таким же названием с каким отдает его pdalife.ru
        header('Content-Disposition: attachment; filename=' . basename($link));
        readfile($link);
    }
}

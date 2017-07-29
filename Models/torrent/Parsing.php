<?php
namespace Models\torrent;

use \More\{Pages,Text,Ini};
use \Core\{App,Loadpage};
use \Libraries\R;

class Parsing extends Loadpage{
    // private $url; // URL страницы
    // private $data; // содержимое страницы
    public $platform; // платформа (windows, android)
    public $number; // какой то номер =)
    public $sort; // выбранная сортировка
    public $page; // текущая страница
    public $genre; // жанр файла (опционально)
    public $base_add = true; // нужно ли заносить данные в бд
    const DIR = 'torrent'; // адрес который настроен в роутах для этого граббера
    const DOMAIN = 'http://rutracker.co.ua';

    public function __construct(string $url)
    {
        parent::__construct(self::DOMAIN . $url);
    }
    # "хлебные крошки"
    public function getBreadCrumbs()
    {
        $links = [];
        $breadcrumbs = $this->data->find('div#speedbar a');
        foreach ($breadcrumbs as $value) {
            if ($value->innertext == 'Главная') {
                continue;
            }
            $value->href = str_replace('load', 'torrent', $value->href) . '/page1/';
            $links[$value->href] = $value->innertext;
        }
        return $links;
    }
    # заголовок страницы
    public function getTitle(): string
    {
        return $this->data->find('h1')[0]->plaintext ?? '';
    }
    # получаем данные для сортировки
    public function getSorting(): array
    {
        return [
            ['key' => 2, 'title' => 'Дата', 'link' => $this->getLinkSotring(2)],
            ['key' => 4, 'title' => 'Название', 'link' => $this->getLinkSotring(4)],
            ['key' => 6, 'title' => 'Рейтингу', 'link' => $this->getLinkSotring(6)],
            ['key' => 10, 'title' => 'Загрузкам', 'link' => $this->getLinkSotring(10)],
            ['key' => 12, 'title' => 'Просмотрам', 'link' => $this->getLinkSotring(12)]
        ];
    }
    private function getLinkSotring(int $sorting): string
    {
        $genre = $this->genre ? $this->genre . '/' : '';
        return '/' . self::DIR . '/' . $this->platform . '/' . $genre . $this->number . '/' . $sorting . '/page' . Pages::getThisPage() . '/';
    }
    # получаем список файлов (листинг файлов например с категории, главной страницы...)
    public function getAllFiles(): array
    {
        $files = []; // сюда будем ложить готовые данные
        $content = $this->data->find('#allEntries div[id]');
        foreach($content as $file){
            # название файла
            $name = $file->find('div.article-film-title a'); // название
            # название файла оригинал (без лишних слов)
            $runame = trim(str_replace(['торрент', 'Скачать', 'через'], '', $name[0]->title));
            # название файла на латыни
            $filename = Text::for_filename($runame);
            # ищем файл в базе, если находим то возвращаем его и ничего дальше не делаем
            $load = R::findOne('torrent', '`name` = ?', [$filename]);
            if (isset($load->id)) {
                $files[] = $load->export();
                continue;
            }
            $image = $file->find('div.article-film-image img'); // скриншот
            $time = $file->find('div.article-film-date'); // дата добавления
            $info = $file->find('div.article-film-info'); // размер и просмотры
            $info = explode('<br />', $info[0]->innertext); // размер и просмотры в массиве
            $views = preg_replace('/.+\:(.+)/i', '$1', $info[1]); // просмотры
            $size = preg_replace('/.+\:(.+)/i', '$1', $info[0]); // размер
            $rating = $file->find('div.article-film-rating-stars ul'); // рейтинг
            # рейтинг (только нужное)
            $rating = preg_replace('/.+\:(.+)\/.+/i', '$1', $rating[0]->title);
            # описание файла и и ссылка на его скачивание
            # return array(description,link)
            $file_data = $this->getFileDescription($name[0]->href);
            # сохранием скриншот на сервер
            $this->saveFile(self::DOMAIN . $image[0]->src, $filename . '.jpg');
            # сохраняем файл на сервер (если он есть)
            if ($file_data['link']) {
                $path_download = $this->saveFile(self::DOMAIN . $file_data['link'], $filename . '.torrent');
            }
            # кладем готовый продукт в массив
            # также делаем запись в БД
            $load = R::dispense('torrent');
            $load->name = $filename;
            $load->runame = $runame;
            $load->add_date = $time[0]->plaintext;
            $load->views = $views;
            $load->size = $size;
            $load->rating = $rating;
            $load->rating_percent = $rating / 5 * 100;
            $load->description = $file_data['description'];
            $load->platform = $this->platform;
            $load->genre = $this->genre;
            $load->path_download = $path_download ?? '';
            if ($this->base_add) {
                R::store($load);
            }
            $files[] = $load->export();
        }
        return $files;
    }
    # файл занимает очень мало места
    # потому тоже имеет смысл его сохранить
    # файлы есть не всегда на сервере у rutracker
    # поэтому если какого-то файла нету, это нормально
    private function saveFile(string $url, string $filename): string
    {
        $path_file = $this->getPathFiles() . $filename;
        if (file_exists(H . $path_file)) {
            return $path_file;
        }
        copy($url, H . $path_file);
        return $path_file;
    }
    # путь по кторому будет сохранен файл и доступен для скачивания
    private function getPathFiles(): string
    {
        $path_dir = '/Static/files/' . self::DIR . '/' . $this->platform . '/';
        if ($this->genre) {
            $path_dir .= $this->genre . '/';
        }
        if (!is_dir(H . $path_dir)) {
            App::mkdir(H . $path_dir);
        }
        return $path_dir;
    }
    # описание файла
    public function getDescription(): string
    {
        /*
        я лазил в исходном коде многих сайтов, хуже этого я пока еще не видел)
        как спарсить описание в хорошем виде пока не вижу способа, поэтому
        будем доставать хоть что то
        P.S данные мы будем класть взятые с "outertext", так оно будет хоть в каком то
        читабельном виде, они будут содержать html код который мы у себя покажем
        что крайне не безопасно, в будущем надо что то будет придумать
        пока админ сайта не увидел что мы его парсим =)
        */
        $description = [];
        # у сайта нету даже намека на нормальну структуру страницы поэтому
        # берем все данные с тегов <p> потом будем отбрасывать не нужное
        $opis = $this->data->find('p');
        # блокируем все до тех пор пока не встретим слово "описание"
        $block = true;
        foreach ($opis AS $p) {
            # снимаем блокировку если встретили описание
            if ($p->plaintext == 'Описание:') {
                $block = false;
            }
            # если блокировка включена игнорируем сбор информации
            # заодно отсеиваем не нужные данные
            if ($block || $p->plaintext == 'Скриншоты:' || empty($p->plaintext)) {
                continue;
            }
            # все что сюда попало как правило относится к описанию файла
            $description[] = $p->outertext;
        }
        # в div'ах с классом headerbar можно поискать полезное
        $headerbar = $this->data->find('div.headerbar');
        foreach ($headerbar AS $text) {
            $description[] = $text->innertext . '<br />';
        }
        # в споилерах иногда можно найти что то интересное
        $spoiler = $this->data->find('div.uSpoilerText');
        foreach ($spoiler AS $text) {
            $description[] = $text->innertext . '<br />';
        }
        return implode('<br />', $description);
    }
    # прямая ссылка на скачивание файла
    public function getLinkDownload(): string
    {
        $link = $this->data->find('#divDLStart a');
        $link = $link[0]->href ?? '';
        return $link;
    }
    # получаем описание файла
    private function getFileDescription(string $url): array
    {
        $description = [];
        $file = new Parsing($url);
        $description['link'] = $file->getLinkDownload();
        $description['description'] = $file->getDescription();
        return $description;
    }
    # получаем нашу навигацию
    public static function getNavigation(): array
    {
        $navs = [];
        $files = glob(H . '/sys/ini/' . self::DIR . '/*.ini');
        for ($i = 0; $i < count($files); $i++) {
            $ini = new Ini($files[$i]);
            $array = $ini->readAll();
            # получем данные категории (находится первым елементов в массиве)
            $navs[$i] = array_shift($array);
            # подкатегории
            foreach ($array AS $link) {
                $navs[$i]['links'][] = $link;
            }
        }
        return $navs;
    }
    /*
     записываем ссылки навигации таким образом чтобы под каждую категорию
     создать свой *.ini файл в котором хранить описание категории (ключ info)
     и подкатегории даной катерогии с описаниями (нужно для meta тегов)
     */
    public function setNavigation()
    {
        $navigation = $this->data->find('.block-menu a');
        $dir_path = H . '/sys/ini/' . self::DIR . '/';
        App::mkdir($dir_path);
        $i = 1; // количество категорий
        foreach ($navigation AS $li) {
            # название категории содержит её номер, вырезаем этот номер
            $li->plaintext = trim(preg_replace('(^[0-9]+\.)', '$1', $li->plaintext));
            # пропускаем ссылки которые ведуть не к категориям а на другие страницы сайта
            if ($li->href == '/' || $li->href == '/gb/' || strripos($li->href, 'index')) {
                continue;
            }
            # на сайте с которого парсим есть бага
            # отсуствует название категории с документальными фильмами
            if (strripos($li->href, 'dokumentalnyj')) {
                $li->plaintext = 'Документальный';
            }
            # если нет ссылки то это категория
            # для нее сделаем описание, название, номер и ключ
            # по этому ключу мы будем закидывать ссылки в эту категорию
            if (!$li->href) {
                $i++; // категория начинается с двойки
                $title = trim(str_replace('торрент', '', $li->plaintext));
                $file_name = Text::for_filename($title);
                $ini = new Ini($dir_path . $file_name . '.ini');
                $ini->write('info', 'title', $title);
                $ini->write('info', 'num', sprintf("%'.02d", $i));
                $ini->write('info', 'description', $title . ' только лучшее');
                $ini->write('info', 'keywords', $title . ' скачать, бесплатно');
            } else {
                # если это не категория записываем ссылку по известному ключу
                # все ссылки попадут в категорию для которой формировался ключ выше
                if (strripos($li->href, 'android_igry')) {
                    # опять бага на сайте, нет слэша вначале страницы для ссылки на андроид игры
                    $li->href = '/' . $li->href;
                }
                # наши ссылки имеют вконце номер сортировки и номер страницы, добавляем
                # сортировка по умолчанию пусть будет по Дате
                $li->href .= '/2/page1/';
                # наши ссылки начинаются с torrent, заменяем
                $li->href = str_replace('load', self::DIR, $li->href);
                # добавляем в навигацию готовый продукт

                $section = Text::for_filename($li->plaintext);
                $ini->write($section, 'title', $li->plaintext);
                $ini->write($section, 'link', $li->href);
                $ini->write($section, 'description', $li->plaintext . ' скачать торрент бесплатно без регистрации');
                $ini->write($section, 'keywords', $li->plaintext . ' скачать, торрент, бесплатно, без регистрации');
            }
        }
    }
    # количество файлов что находятся в категории
    public function getFilesRows(): int
    {
        return $this->data->find('td[width=60%] b')[0]->plaintext ?? 0;
    }
    # количество страниц для пагинации
    public function countPages(): int
    {
        $pages = floor($this->getFilesRows() / 16);
        if ($this->getFilesRows() % 16 != 0) {
            $pages++;
        }
        return $pages;
    }
}

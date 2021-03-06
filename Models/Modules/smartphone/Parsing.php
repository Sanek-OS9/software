<?php
/*

*/
namespace Models\Modules\smartphone;

use \More\{Text,Ini};
use \Core\{App,Loadpage};
use \Libraries\R;
use \Models\Modules\smartphone\File;

class Parsing extends Loadpage{

    public $url;
    const DIR = 'smartphone';

    public function __construct(string $url, array $params = [])
    {
        parent::__construct($url, $params);
    }

    public function getAllFiles(): array
    {
        $array = $this->data->find('a[class=b-application__image-link]');
        $links = [];
        foreach ($array AS $link) {
            $links[] = $link->href;
        }
        # убираем повторяющиеся файлы
        $links = array_values(array_unique($links));
        $files = []; // сюда складываем данные о приложениях
        $screens = []; // сюда складываем скршоты для подальшего сохранения
        for ($i = 0; $i < count($links); $i++) {
            $filename = basename($links[$i]);
            $file = R::findOne(self::DIR, '`path` = ?', [$filename]);
            if (isset($file->id)) {
                $files[$i] = new File($file->path);
                continue;
            }
            $data = new Parsing($links[$i]);
            $file = R::dispense(self::DIR);
            $file->path = $filename;
            $file->runame = $data->getTitle();
            $file->name = $data->getName();
            $file->views = $data->getViews();
            $file->version = $data->getVersion();
            $file->links = $data->getLinks();
            $file->description = $data->getDescription();
            $file->video = $data->getVideo();
            $file->screen = $data->getScreen();
            $file->screens = $data->getScreens();
            $file->rating = $data->getRating();
            $file->rating_percent = $data->getRatingPercent();
            $file->platform = $data->getInfo('platform');
            $file->type = $data->getInfo('type');
            $file->genre = $data->getInfo('genre');
            R::store($file);
            $files[$i] = $file->export();
            $screens[] = [$files[$i], $data->getScreen()];
            #$this->saveScreen($files[$i], $data->getScreen());
        }
        $this->saveScreens($screens);
        return $files;
    }
    # сохраняем скриншот
    private function saveScreen(array $file, string $screen)
    {
        $dir_path = H . '/Static/files/' . self::DIR . '/' . $file['platform'] . '/' . $file['type'] . '/' . $file['genre'] . '/';
        App::mkdir($dir_path);
        $screen_path = $dir_path . $file['name'] . '.jpg';
        if (file_exists($screen_path)) {
            return;
        }
        copy($screen, $screen_path);
        return;
    }
    # сохраняем скриншоты кучей
    private function saveScreens(array $screens)
    {
        for ($i = 0; $i < count($screens); $i++) {
            $this->saveScreen($screens[$i][0], $screens[$i][1]);
        }
    }
    private function getIcon(string $platform): string
    {
        switch ($platform) {
            case 'Android' : return 'android';
            default : return 'mobile';
        }
    }
    public function getInfo(string $type): string
    {
        $types = ['platform', 'type', 'genre'];
        $data = $this->data->find('.b-application__navigation a');
        $navigation = [];
        $i = 0;
        foreach ($data AS $text) {
            $test = explode('/', $text->href);
            $navigation[$types[$i]] = $test[sizeof($test)-2];
            $i++;
        }
        $runame = $navigation[$type];
        $name = strtolower(Text::for_filename($runame));
        # записали в файлик
        $this->setInfo($type, $name, $runame);
        return $name;
    }
    # сохраним данные о платформе, жанре и типу приложения
    # для дальнейшей русификации
    private function setInfo(string $section, string $key, string $value)
    {
        static $ini;
        $dir_path = H . '/sys/ini/' . self::DIR . '/';
        App::mkdir($dir_path);
        if (!$ini) {
            $ini = new Ini($dir_path . 'navigation.ini');
        }
        $name = $ini->read($section, $key);
        # если ранее не добавляли
        if (!$name) {
            $ini->write($section, $key, $value);
        }
    }
    # рейтинг файла по пяти бальной шкале
    public function getRating(): float
    {
        return $this->getRatingPercent() * 0.05;
    }
    # рейтинг файла в процентах
    public function getRatingPercent(): int
    {
        return $this->data->find('meta[itemprop=ratingValue]')[0]->content ?? 0;
    }
    # основной скриншот файла
    public function getScreen(): string
    {
        $screen_url = $this->data->find('.b-application__image-link img')[0]->src;
        return $screen_url ?? '';
    }
    # дополнительные скриншоты
    public function getScreens(): string
    {
        $screens = [];
        $data = $this->data->find('.b-image-gallery__list-item a');
        foreach ($data AS $screen) {
            $screens[] = str_replace('https://pdacdn.com', '/' . self::DIR, $screen->href);
        }
        return serialize($screens);
    }
    # количество страниц
    public function getPages(): int
    {
        $links = [];
        $data = $this->data->find('.b-pager__panel a');
        foreach ($data AS $link) {
            $links[] = $link->plaintext;
        }
        # берем послдний элемент
        $page = array_pop($links);
        # если последний элемент не число сдвигаем еще на один уровень
        if ($page == 'Следующая') {
            $page = array_pop($links);
        }
        return $page ?? 0;
    }
    # видео обзор файла
    public function getVideo(): string
    {
        return $this->data->find('a[class=play-icon]')[0]->href ?? '';
    }
    # описание файла
    public function getDescription(): string
    {
        return $this->data->find('.b-user-content')[0]->innertext ?? '';
    }
    # ссылки на скачивание
    public function getLinks(): string
    {
        $links = [];
        $number = 0;
        $data = $this->data->find('.b-dwn-spoiler__link-line');
        foreach ($data AS $link) {
            $more_text = $link->find('span');
            $links[$number]['href'] = str_replace(['https://pdalife.ru/dwn/', '.html'], ['/smartphone/download/', ''], $link->find('a')[0]->href) . '/';
            $links[$number]['title'] = $link->find('a')[0]->plaintext;
            foreach ($more_text AS $text) {
                $links[$number]['text'][] = $text->plaintext;
            }
            $number++;
        }
        return serialize($links);
    }
    # заголовок страницы
    public function getTitle(): string
    {
        return $this->data->find('h1[class=b-header__label]')[0]->plaintext ?? '2';
    }
    # заголовок страницы на латыни и без спец.символов
    public function getName()
    {
        return Text::for_filename($this->getTitle());
    }
    # для версии какой платформы подходит приложение
    public function getVersion(): string
    {
        return html_entity_decode($this->data->find('span[class=b-application__info-value_type_os]')[0]->plaintext) ?? '';
    }
    # количество просмотров
    public function getViews(): string
    {
        return $this->data->find('span[class=b-application__info-value]')[0]->plaintext ?? '';
    }
}

<?php
namespace Models\Modules\smartphone;

use \Libraries\R;
use \More\Ini;
use \Models\Modules\smartphone\Software;

class File{
    private $table_name;
    private $file_path;
    private $data;
    private $info;

    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
        $this->data = $this->getData();
        $this->info = $this->getInfo();
    }
    private function getInfo(): array
    {
        $ini = new Ini(H . '/sys/ini/' . Software::$table_name . '/navigation.ini');
        return $ini->readAll();
    }
    private function getData()
    {
        return Software::getFile($this->file_path);
    }
    private function getScreen(): string
    {
        $screen_path =  '/Static/files/' . Software::$table_name . '/' . $this->data['platform'] . '/' . $this->data['type'] . '/' . $this->data['genre'] . '/' . $this->data['name'] . '.jpg';
        if (!file_exists(H . $screen_path)) {
            return $this->data['screen'];
        }
        return $screen_path;
    }
    private function getLinkView()
    {
        return '/' . Software::$table_name . '/' . $this->data['path'];
    }
    public function __get($name)
    {
        switch ($name) {
            case 'rugenre' : return $this->info['genre'][$this->data['genre']];
            case 'rutype' : return $this->info['type'][$this->data['type']];
            case 'screen' : return $this->getScreen();
            case 'link_view' : return $this->getLinkView();
            case 'links' : return $this->getLinks();
            case 'screens' : return unserialize($this->data[$name]);
            default : return $this->data[$name] ?? 'w';
        }
    }
    private function getLinks(): array
    {
        $links = [];
        $array = unserialize($this->data['links']);
        for ($i = 0; $i < sizeof($array); $i++) {
            $text = isset($array[$i]['text']) ? implode(' ', $array[$i]['text']) : '';
            $links[] = [
                'href' => $array[$i]['href'],
                'title' => $array[$i]['title'] . ' ' . $text
            ];
        }
        return $links;
    }
    public function __isset($name)
    {
        $array_fields = ['screen','rugenre','rutype','link_view'];
        return isset($this->data[$name]) || in_array($name, $array_fields) ? true : false;
    }
}

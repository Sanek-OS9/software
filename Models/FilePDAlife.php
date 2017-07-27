<?php
namespace Models;

use \Libraries\R;
use \More\Ini;

class FilePDAlife{
    private $table_name;
    private $file_path;
    private $data;
    private $info;
    const DIR = 'smartphone';

    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
        $this->data = $this->getData();
        $this->info = $this->getInfo();
    }
    private function getInfo(): array
    {
        $ini = new Ini(H . '/sys/ini/' . self::DIR . '/navigation.ini');
        return $ini->readAll();
    }
    private function getData()
    {
        $file = R::findOne(self::DIR, '`path` = ?', [$this->file_path]);
        if (isset($file->id)) {
            return $file->export();
        }
        return [];
    }
    private function getScreen(): string
    {
        return '/Static/files/' . self::DIR . '/' . $this->data['platform'] . '/' . $this->data['type'] . '/' . $this->data['genre'] . '/' . $this->data['name'] . '.jpg';
    }
    private function getLinkView()
    {
        return '/' . self::DIR . '/' . $this->data['path'];
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

<?php
namespace Models;

use \Libraries\R;
use \Models\FilePDAlife;
use \Core\DB;

abstract class Software{
    # получаем файлы определенной платформы
    static function getFiles(string $table_name, string $platform, $limit = 15): array
    {
        $items = R::findAll($table_name, '`platform` = ? LIMIT ?', [$platform, $limit]);
        $files = [];
        foreach ($items AS $file) {
            $files[] = new FilePDAlife($file['path']);
        }
        return $files;
    }
    # получаем файлы по запросу поиска
    # для запроса ajax отдаем только названия файлов
    static function getSearchFiles(string $table_name, string $search, $ajax = false, $limit = 200): array
    {
        $items = R::findAll($table_name, '`runame` LIKE ? LIMIT ?', ['%' . $search . '%', $limit]);
        $files = [];
        foreach ($items as $file) {
            $files[] = !$ajax ? new FilePDAlife($table_name, $file['path']) : $file['runame'];
        }
        return $files;
    }
    # общее количество файлов, и сколько их в каждой платформе
    static function getCountPlatform():array
    {
        $array = [];
        $array['all']['files'] = 0;
        $counts = R::getAll("SELECT COUNT(`platform`) AS 'count', `platform` FROM `smartphone` GROUP BY `platform` ORDER BY `count` DESC");
        foreach ($counts AS $file) {
            # записываем данные для платформы
            $array['platform'][$file['platform']] = [
                'files' => $file['count'],
            ];
            # для общей статистики
            $array['all']['files'] += $file['count'];
        }
        return $array;
    }
    # получить ссылки на просмотр файлов
    static function getFilesViewLinks(): array
    {
        $links = [];
        $files = R::findAll('smartphone');
        foreach ($files AS $item) {
            $file = new FilePDAlife($item->path);
            $links[] = $file->link_view;
        }
        return $links;
    }
}

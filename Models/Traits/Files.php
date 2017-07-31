<?php
namespace Models\Traits;

use \Libraries\R;

/**
 * Трэйт для работы со спарсенными файлами
 */
trait Files
{
    # получаем данные файла по его имени или пути
    static function getFile(string $name)
    {
        return R::findOne(self::$table_name, '`path` = :name OR `name` = :name', [':name' => $name]);
    }
    # получаем файлы определенной платформы
    static function getFiles(string $platform, $limit = 15): array
    {
        $items = R::findAll(self::$table_name, '`platform` = ? LIMIT ?', [$platform, $limit]);
        $files = [];
        foreach ($items AS $file) {
            $files[] = new self::$class_import($file['path']);
        }
        return $files;
    }
    # получаем файлы по запросу поиска
    # для запроса ajax отдаем только названия файлов
    static function getSearchFiles(string $search, $ajax = false, $limit = 200): array
    {
        $items = R::findAll(self::$table_name, '`runame` LIKE ? LIMIT ?', ['%' . $search . '%', $limit]);
        $files = [];
        foreach ($items as $file) {
            $files[] = !$ajax ? new self::$class_import($file['path']) : $file['runame'];
        }
        return $files;
    }
    # общее количество файлов, и сколько их в каждой платформе
    static function getCountPlatform():array
    {
        $array = [];
        $array['all']['files'] = 0;
        $counts = R::getAll("SELECT COUNT(`platform`) AS 'count', `platform` FROM `" . self::$table_name . "` GROUP BY `platform` ORDER BY `count` DESC");
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

}

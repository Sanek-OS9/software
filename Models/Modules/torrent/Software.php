<?php
namespace Models\Modules\torrent;

use \Libraries\R;
use \Models\torrent\File;
use \Models\Traits\Files;

abstract class Software{
    use Files;
    public static $class_import = '\Models\Modules\torrent\File';
    public static $table_name = CURRENT_SITE;

    # получить ссылки на просмотр файлов
    static function getFilesViewLinks(): array
    {
        $links = [];
        $files = R::findAll(self::$table_name);
        foreach ($files AS $item) {
            $links[] = '/torrent/' . $item['name'] . '.htm';
        }
        return $links;
    }
}

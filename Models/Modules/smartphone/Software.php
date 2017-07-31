<?php
namespace Models\Modules\smartphone;

use \Libraries\R;
use \Models\smartphone\File;
use \Models\Traits\Files;

abstract class Software{
    use Files;
    public static $class_import = '\Models\Modules\smartphone\File';
    public static $table_name = CURRENT_SITE;

    # получить ссылки на просмотр файлов
    static function getFilesViewLinks(): array
    {
        $links = [];
        $files = R::findAll(self::$table_name);
        foreach ($files AS $item) {
            $links[] = '/smartphone/' . $item['path'];
        }
        return $links;
    }
}

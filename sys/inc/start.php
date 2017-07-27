<?php
require_once 'require.php';
require_once 'initialization.php';

use \Libraries\R;

# подлючение к базе данных для ReadBeanPHP
$config_bd = require_once H . '/sys/config/db.php';
R::setup('mysql:host=' . $config_bd['host'] . ';dbname=' . $config_bd['db_name'] . '', $config_bd['user'], $config_bd['password']);
unset($config_bd); # больше эта переменная не нужна
if (!R::testConnection()) {
    die('Нет подключения к базе данных');
}
# заморозка (выключить когда скрипт будет в процессе доработки)
// R::freeze(true);

<?php
/*
  Класс для подключения к БД
  Можно использовать в любом месте движка
  $db = DB::me();
 */
namespace Core;

abstract class DB
{
    protected static $host;
    protected static $user;
    protected static $password;
    protected static $db_name;
    protected static $_instance;


    protected function __construct()
    {
    }
    protected function __clone()
    {
    }
    /*
     * Получаем данные от БД
     */
    private static function getConfig()
    {
        return require(H . '/sys/config/db.php');
    }

    /**
     * @return PDO
     * @throws ExceptionPdoNotExists
     * @throws Exception
     */
    public static function me()
    {
        if (!class_exists('pdo') || array_search('mysql', \PDO::getAvailableDrivers()) === false) {
            die("Отсутствует драйвер PDO");
        }

        $args = self::getConfig();
        self::$host = $args['host'];
        self::$db_name = $args['db_name'];
        self::$user = $args['user'];
        self::$password = $args['password'];

        if (!self::$_instance) {
            if (!self::$db_name || !self::$user || !self::$host) {
                die('Укажите параметры соединения');
            }

            $dsn = 'mysql:host=' . self::$host . ';dbname=' . self::$db_name . ';charset=utf8';
            $opt = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
              self::$_instance = new \PDO($dsn, self::$user, self::$password, $opt);
            } catch (Exception $e) {
              echo 'Данные вы ввели так себе';
            }

        }
        return self::$_instance;
    }
}

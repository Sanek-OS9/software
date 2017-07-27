<?php
namespace More;

class Ini
{
    private $filename;
    private $arr;

    public function __construct(string $file)
    {
        $this->filename = $file;
        $this->loadFromFile();
    }

    private function value_encode(string $str): string
    {
        $str = str_replace(array("\r", "\n", "\t"), array('\r', '\n', '\t'), $str);
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    private function value_decode(string $str): string
    {
        $str = str_replace(array('\r', '\n', '\t'), array("\r", "\n", "\t"), $str);
        return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    }
    /*
     * парсим данные файла
     */
    private function initArray()
    {
        $this->arr = parse_ini_file($this->filename, true);
    }
    /*
     * проверяем доступность файла и загружаем данные
     */
    private function loadFromFile()
    {
        if (file_exists($this->filename) && is_readable($this->filename)) {
            return $this->initArray();
        }
        return [];
    }
    /*
     * Считываем данные
     * $def - значение по умолчанию
     */
    public function read(string $section, string $key, string $def = ''): string
    {
        if (isset($this->arr[$section][$key])) {
            return self::value_decode($this->arr[$section][$key]);
        } else {
            return self::value_decode($def) ;
        }
    }
    /*
     * Записываем данные
     */
    public function write(string $section, string $key, string $value)
    {
        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
        $this->arr[$section][$key] = $value;
    }
    /*
     * удаляем секцию
     */
    public function eraseSection(string $section)
    {
        if (isset($this->arr[$section])) {
            unset($this->arr[$section]);
        }
    }
    /*
     * удаляем значение по ключу
     */
    public function deleteKey(string $section, string $key)
    {
        if (isset($this->arr[$section][$key])) {
            unset($this->arr[$section][$key]);
        }
    }
    /*
     * получем данные однойй секции
     */
    public function readSection(string $section)
    {
        if (isset($this->arr[$section])) {
            return $this->arr[$section];
        }
        return [];
    }
    public function readAll(): array
    {
        return $this->arr;
    }
    /*
     * обновляем данные
     */
    private function updateFile()
    {
        $result = [];
        if ($this->arr) {
            foreach ($this->arr as $sname => $section) {
                $result[] = '[' . $sname . ']' ;
                foreach ($section as $key => $value) {
                    $result[] = $key . ' = "' . self::value_encode($value) . '";' ;
                }
            }
        }
        file_put_contents($this->filename, implode("\r\n", $result)) ;
    }
    public function __destruct()
    {
        $this->updateFile();
    }
}

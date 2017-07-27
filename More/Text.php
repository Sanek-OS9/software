<?php
namespace More;

abstract class Text
{
    public static function input_text(string $text): string
    {
        return trim(htmlspecialchars($text));
    }
    public static function toValue(string $text): string
    {
        return ($text);
    }
    public static function toOutput(string $text): string
    {
        //$text = str_replace([':)', ':('], ['*улыбаюсь*', '*подмигиваю*'], $text);
        //bbcode
        // преобразование ссылки на youtube ролик в BBCode
        $text = preg_replace('#(^|\s|\(|\])((https?://)?www\.youtube\.com/watch\?(.*?&)*v=([^ \r\n\t`\'"<]+))(,|\[|<|\s|$)#iuU', '\1[youtube]\5[/youtube]\6', $text);
        //преобразовываем URL ссылку
        $text = preg_replace('#(^|\s|\(|\])([a-z]+://([^ \r\n\t`\'"<]+))(,|\[|<|\s|$)#iuU', '\1<a href="\2" target="_blank">\2</a>\4', $text);

        $text = self::bbcode($text);

        return $text;
    }
    /**
     * Получение подстроки
     * Корректная работа с UTF-8
     * @param string $text Исходная строка
     * @param integer $len Максимальная длина возвращаемой строки
     * @param integer $start Начало подстроки
     * @param string $mn Текст, подставляемый в конец строки при условии, что возхвращаемая строка меньще исходной
     * @return string
     */
    public static function substr(string $text, int $len, int $start = 0, $mn = ' (...)'): string
    {
        $text = trim($text);
        if (function_exists('mb_substr')) {
            return mb_substr($text, $start, $len) . (mb_strlen($text) > $len - $start ? $mn : null);
        }
        if (function_exists('iconv')) {
            return iconv_substr($text, $start, $len) . (iconv_strlen($text) > $len - $start ? $mn : null);
        }

        return $text;
    }
    /*
     * BBCode
     */
    public static function bbcode(string $str): string
    {
        $id = mt_rand(1111, 9999);
        $str_search = [
          "#\\\n#is",
          "#\[b\](.+?)\[\/b\]#is",
          "#\[youtube\](.+?)\[\/youtube\]#is",
          "#\[i\](.+?)\[\/i\]#is",
          "#\[u\](.+?)\[\/u\]#is",
          "#\[quote\](.+?)\[\/quote\]#is",
          "#\[url=(.+?)\](.+?)\[\/url\]#is",
          "#\[img\](.+?)\[\/img\]#is",
          "#\[size=(.+?)\](.+?)\[\/size\]#is",
          "#\[color=(.+?)\](.+?)\[\/color\]#is",
          "#\[spoiler=(.+?)\](.+?)\[\/spoiler\]#is"
        ];
        $str_replace = [
          "<br />",
          "<b>\\1</b>",
          self::youtube("\\1"),
          "<i>\\1</i>",
          "<span style='text-decoration:underline'>\\1</span>",
          "<blockquote>\\1</blockquote>",
          "<a href='\\1' target='_blank'>\\2</a>",
          "<img src='\\1' alt = 'Изображение' />",
          "<span style='font-size:\\1%'>\\2</span>",
          "<span style='color:\\1'>\\2</span>",
          "<a data-toggle='collapse' href='#collapse$id'>\\1</a><div id='collapse$id' class='collapse'>\\2</div>"
        ];
        return preg_replace($str_search, $str_replace, $str);
    }
    public static function youtube(string $text): string
    {
        return '<div class="youtube"><iframe src="https://www.youtube.com/embed/' . self::toValue($text) . '?iv_load_policy=3;rel=0;showinfo=0;modestbranding=1;autohide=1;" allowfullscreen></iframe></div>';
    }
    /**
     * Возврат строки, разрешенной для названий файлов
     */
    public static function for_filename(string $text): string
    {
        return trim(preg_replace('#(^\.)|[^a-z0-9_\-\(\)\.]+#ui', '_', self::translit($text)));
    }

    /**
     * Транслитерация русского текста в английский
     */
    static function translit(string $string): string
    {
        $table = array(
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Ґ' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Є' => 'YE',
            'Ё' => 'YO',
            'Ж' => 'ZH',
            'З' => 'Z',
            'И' => 'I',
            'І' => 'I',
            'Ї' => 'YI',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ў' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'CSH',
            'Ь' => '',
            'Ы' => 'Y',
            'Ъ' => '',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'ґ' => 'g',
            'д' => 'd',
            'е' => 'e',
            'є' => 'ye',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'і' => 'i',
            'ї' => 'yi',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ў' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'csh',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
        );
        return str_replace(array_keys($table), array_values($table), $string);
    }
}

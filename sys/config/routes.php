<?php
/*
 @param pattern - адрес по которому доступна страница
 @param run - вызываемый класс/метод/параметр1/параметр2...
 @param method - способ передачи данных POST/GET
*/
return [
    # главная страница
    ['pattern' => '','run' => 'main/index','method' => 'GET'],
    # перенаправление на случайный файл
    ['pattern' => 'pdalife/random','run' => 'PDAlife/random','method' => 'GET'],
    # генерация файла sitemap.xml
    ['pattern' => 'sitemap','run' => 'main/sitemap','method' => 'GET'],
    # файлы с сайта rutracker.co.ua (главная страница)
    ['pattern' => 'torrent','run' => 'rutracker/index','method' => 'GET'],
    # просмотр файла с rutracker.co.ua
    ['pattern' => 'torrent/(.+).htm','run' => 'rutracker/file/$1','method' => 'GET'],
    [
        'pattern' => 'torrent/(torrent_igry)/([0-9]+)/(2|4|6|10|12)/(page[0-9]+)',
        'run' => 'rutracker/index/$1/$2/$3/$4',
        'method' => 'GET'
    ],
    # файлы с сайта rutracker.co.ua (категория)
    [
        'pattern' => 'torrent/([a-z0-9|_]+)/([a-z0-9|_]+)/([0-9]+)/(2|4|6|10|12)/(page[0-9]+)',
        'run' => 'rutracker/category/$1/$3/$4/$5/$2',
        'method' => 'GET'
    ],
    # поиск по сайту
    ['pattern' => 'search/\?q=(.+)','run' => 'main/search/$1','method' => 'GET'],
    # поиск по сайту
    ['pattern' => 'search/\?term=(.+)','run' => 'main/searchajax/$1','method' => 'GET'],
    # просмотр файлов с PDAlife по тегу
    ['pattern' => 'tag/([a-z0-9|\-|\+|\!]+)','run' => 'PDAlife/tag/$1','method' => 'GET'],
    # просмотр файлов с PDAlife по тегу
    ['pattern' => 'tag/([a-z0-9|\-|\+|\!]+)/(page[0-9]+)','run' => 'PDAlife/tag/$1/$2','method' => 'GET'],
    # просмотр файла с PDAlife
    ['pattern' => 'smartphone/([a-z0-9|\-|\+|\!]+\.html)','run' => 'PDAlife/view/$1/$2','method' => 'GET'],
    # загрузка файла с PDAlife
    ['pattern' => 'smartphone/download/([a-z0-9]+)','run' => 'PDAlife/download/$1','method' => 'GET'],
    # просмотр категорий файлов с PDAlife
    [
        'pattern' => 'smartphone/(android|ios|psp|windows|symbian|bada)/?([a-z|-]+)?/?(sort-by)?/?(new|update|popular|views)?/([page0-9]+)',
        'run' => 'PDAlife/files/$1/$2/$3/$4/$5',
        'method' => 'GET',
    ],
    # передаем параметры скриншота который находится на PDAlife
    # что бы отобразить его с нашего сервера
    [
        'pattern' => 'smartphone/userfiles/screens/([0-9]+)/([0-9]+)/([A-Za-z0-9|\_\-|\(|\)]+)\.jpg',
        'run' => 'PDAlife/screen/$1/$2/$3',
        'method' => 'GET',
    ]
];

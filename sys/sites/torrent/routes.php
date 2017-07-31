<?php
/*
 @param pattern - адрес по которому доступна страница
 @param run - вызываемый класс/метод/параметр1/параметр2...
 @param method - способ передачи данных POST/GET
*/
return [
    # главная страница
    ['pattern' => '','run' => 'rutracker/index','method' => 'GET'],
    # перенаправление на случайный файл
    ['pattern' => 'sitemap','run' => 'rutracker/sitemap','method' => 'GET'],
    # перенаправление на случайный файл
    ['pattern' => 'random','run' => 'rutracker/random','method' => 'GET'],
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
        'pattern' => 'torrent/([a-z0-9|_]+)/?([a-z|_]+)?/([0-9]+)/?(2|4|6|10|12)?/(page[0-9]+)',
        'run' => 'rutracker/category/$1/$2/$3/$4/$5',
        'method' => 'GET'
    ],
];

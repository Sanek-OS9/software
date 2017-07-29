<?php
/*
 @param pattern - адрес по которому доступна страница
 @param run - вызываемый класс/метод/параметр1/параметр2...
 @param method - способ передачи данных POST/GET
*/
return [
    # главная страница
    ['pattern' => '','run' => 'PDAlife/index','method' => 'GET'],
    # перенаправление на случайный файл
    ['pattern' => 'pdalife/random','run' => 'PDAlife/random','method' => 'GET'],
    # генерация файла sitemap.xml
    ['pattern' => 'sitemap','run' => 'PDAlife/sitemap','method' => 'GET'],
    # поиск по сайту
    ['pattern' => 'search/\?q=(.+)','run' => 'PDAlife/search/$1','method' => 'GET'],
    # поиск по сайту
    ['pattern' => 'search/\?term=(.+)','run' => 'PDAlife/searchajax/$1','method' => 'GET'],
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

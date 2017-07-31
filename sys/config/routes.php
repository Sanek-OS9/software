<?php
use \Core\App;

# взависимости от доменного имени подключаем нужные роуты
switch ($_SERVER['HTTP_HOST']) {
    # роуты для домена http://скачай-ка.рф
    case 'xn----7sbab7bde4c0c.xn--p1ai':
        return App::getRoutes('smartphone');
        break;
    # роуты для домена http://softiki.pw
    case 'softiki.pw':
        return App::getRoutes('torrent');
        break;
    # роуты по умолчанию
    default:
        return App::getRoutes('smartphone');
        break;
}

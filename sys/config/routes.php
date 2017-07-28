<?php
# взависимости от доменного имени подключаем нужные роуты

switch ($_SERVER['HTTP_HOST']) {
    case 'xn----7sbab7bde4c0c.xn--p1ai':
        return require_once 'routes/ska4ayka.php';
        break;
    case 'softiki.pw':
        return require_once 'routes/softiki.php';
        break;
    default:
        return require_once 'routes/softiki.php';;
        break;
}

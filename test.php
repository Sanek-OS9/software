<?php
// # алгоритм сортировки мыльного пузыря
// $array = [5,6,4,8,7,9];
// $size = sizeof($array) - 1;
// for ($i = $size; $i >= 0; $i--) {
//     for ($b = 0; $b <= ($i - 1); $b++) {
//         if ($array[$b] > $array[$b + 1]) {
//             $key = $array[$b];
//             $array[$b] = $array[$b + 1];
//             $array[$b + 1] = $key;
//         }
//     }
// }
// print_r($array);
// echo '<hr />';
// # алгоритм сортировки выбором
// $array = [5,6,4,8,7,9];
// $size = sizeof($array) - 1;
// for ($i = $size; $i >= 0; $i--) {
//     for ($b = 0; $b <= ($i - 1); $b++) {
//         if ($array[$b] < $array[$b + 1]) {
//             $key = $array[$b];
//             $array[$b] = $array[$b + 1];
//             $array[$b + 1] = $key;
//         }
//     }
// }
// print_r($array);
//
// exit;
$login2 = $_POST['login2'] ?? 'tey';
echo $login2;
exit;
if (isset($_POST['test'])) {
    echo '<div class="test">' . json_encode(['msg' => 'Вы прислали ' . $_POST['test']]) . '</div>';
    exit;
}
foreach ($_SERVER AS $k => $v) {
    echo $k . ' = ' . $v . '<br />';
}
//echo json_encode($_SERVER);
$f = fopen('text.txt', 'w+');
fwrite($f, "Меня посетили с такимим данными:\n");
fwrite($f, implode("\n", $_SERVER));
fclose($f);
exit;
use \Core\R;

/*
 * CRUD - Create Read Update Delete
 */
# подлючение к базе данных
R::setup('mysql:host=localhost;dbname=softiki', 'root', '8z9jr32q');

# заморозка
R::freeze(false);

# создание плагина
# в данном примере плагин позволяет задавать названия таблиц
# в snake_case и camelCase стилях (не рекомендуется)
R::ext('xdispense', function ($table_name){
    return R::getRedBean()->dispense($table_name);
});

# проверка подключения
if (!R::testConnection()) {
    exit('Нет подключения к базе данных');
}
# очистить таблицу
//R::wipe('user');

# очистить базу данных
//R::nuke();

$users = R::findAll('user', 'LIMIT 2,3');
foreach($users AS $user){
    echo $user->name . '<br />';
}

exit;
/*
 * Delete
 */
# что бы удалить данные с таблицы достаточно получить их
# вызвать R::trash(array)
$user = R::load('user', 4);
R::trash($user);
exit;
/*
 * Update
 */
# что бы обновить данные в таблице достаточно получить их
# изменить нужное свойство и вызвать R::store(array)
# если изменения проводились для нескольких записей то нужно один раз вызвать R::storeAll(array)
$user = R::load('user', 1);
$user->name = 'Sanek_OS9';
R::store($user);
printr($user);
exit;
/*
 * Read
 */
# получить данные из таблицы по ID
$user = R::load('user', 1);

# получить несколько данных из таблицы по ID
$users = R::loadAll('user', [1,2,3,9]);

# конвертировать полученные данные в массив
$array = $user->export();

# получить все записи по определенным параметрам (WHERE, ORDER BY и LIMIT)
$users = R::find('user', '`admin_id` = ? ORDER BY `id` DESC', [3]);

# получить все записи отсортировав их (ORDER BY и LIMIT) при необходимости
$users = R::findAll('user', 'ORDER BY `id` DESC');

# получить одну запись по определенным параметрам (WHERE и ORDER BY)
$user = R::findOne('user', '`admin_id` = ? ORDER BY `id` DESC', [3]);

# получить все данные загружая их по одному (можно сортировать)
# для оптимизации загрузки большого количества данных
$users = R::findCollection('user');
while($user = $users->next()){
    echo $user->name . '<br />';
}

# ищем пользователей с определенными именами или другими параметрами
$users = R::findLike('user', ['name' => ['Санек', 'Боб']], 'ORDER BY `id` DESC');

# ищем пользователя по параметрам, если такого нету, создаем его
$user = R::findOrCreate('user', ['name' => 'Люся']);

# получаем количество записей в таблице, можно использовать WHERE
$count = R::count('user');

printr($count);
exit;
/*
 * Create
 */
# заполнить базу данных данными
# если выбранной таблицы нету или нету поля
# таблица или поле будет созданно автоматически
# при условии что выключена заморозка (по умолчанию выключена)
# если название поля содержит _id то для него будет создан index
$user = R::dispense('user');
$user->name = 'Санек';
$user->age = 23;
$user->admin_id = 3;
R::store($user); //сохранить


# выполнить произвольный запрос
R::exec("DELETE FROM `files` WHERE `id` = ?", [1353]);

# пример работы с неколькими входящими данными ( R::genSlots(array) )
$ids = [1,2,55,889];
R::exec("DELETE FROM `files` WHERE `id` IN(" . R::genSlots($ids) . ")", $ids);

# закрыть подключение
R::close();

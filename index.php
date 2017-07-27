<?php
require_once 'sys/inc/start.php';


/**
 * Случайная выборка с учетом веса каждого элемента.
 * @param array $data Массив, в котором ищется случайный элемент
 * @param string $column Параметр массива, содержащий «вес» вероятности
 * @return int Индекс найденного элемента в массиве $data
 */
 /*
function getRandomIndex($data, $column = 'ver') {
  $rand = mt_rand(1, array_sum(array_column($data, $column)));
  $cur = $prev = 0;
  for ($i = 0, $count = count($data); $i < $count; ++$i) {
    $prev += $i != 0 ? $data[$i-1][$column] : 0;
    $cur += $data[$i][$column];
    if ($rand > $prev && $rand <= $cur) {
      return $i;
    }
  }
  return -1;
}

// Использование
$games = [
  ['name' => 'Игра 1', 'ver' => 3], // вероятность 3/15
  ['name' => 'Игра 2', 'ver' => 1], // вероятность 1/15
  ['name' => 'Игра 3', 'ver' => 2], // вероятность 2/15
  ['name' => 'Игра 4', 'ver' => 4], // вероятность 4/15
  ['name' => 'Игра 5', 'ver' => 5], // вероятность 5/15
];
$key = getRandomIndex($games);
echo $games[$key]['name'];
exit;
*/
use \Core\{Router,ErrorHandler};

$ErrorHandler = new ErrorHandler();
$ErrorHandler->register();

$router = new Router();
$router->run();

<?php
namespace Controllers;

use \Core\Controller;
use \More\Text;

class AuthorizeController extends Controller{



    public function actionRegister()
    {
        $name = Text::for_rusname($_POST['name']); # Имя
        $user = Text::for_filename($_POST['user']); # Логин
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); # E-mail
        $password = Text::for_password($_POST['password']); # Пароль
        $password1 = Text::for_password($_POST['password1']); # Пароль для подстверждения

        $city = Text::for_rusname($_POST['city']); # Город проживания
        $id_country = (int) $_POST['country']; # Страна проживания
        $state = Text::for_state($_POST['state']); # Операционная система смартфона
        $by = (int) $_POST['by']; # Год рождения
        $bm = (int) $_POST['bm']; # Месяц рождения
        $bd = (int) $_POST['bd']; # День рождения
        $gender = $_POST['gender'] === 1 ? 1 : 2; # Пол (1-мужской/2-женский)
        echo $state;
        if (!$name) {
            $this->params['error'] = 'Введите Ваше имя';
        }
        if (!$user ) {
            $this->params['error'] = 'Введите Ваш логин';
        }
        if (!$email ) {
            $this->params['error'] = 'Введите Ваш E-Mail';
        }
        if (!$password || $password != $password1) {
            $this->params['error'] = 'Пароль не верный или пароли не совпадают';
        }
        # если из даты рождения хоть что то выбрано (день,месяц,год)
        # проверяем корректность
        if (($by || $bm || $bd) && !checkdate($bm, $bd, $by)) {
            $this->params['error'] = 'Не верный формат даты рождения';
        }

        printr($_POST);
        printr($_FILES);
        $this->display('authorize/register');
    }
}

<?php
namespace Core;

use \Libraries\Snoopy;

abstract class Loadpage{
    protected $url;
    protected $data;
    protected $table_name;
    protected $form_params = [];

    public function __construct(string $url, array $params = [])
    {
        $this->url = $url;
        $this->snoopy = new Snoopy();
        $this->snoopy->cookies = $params['cookies'] ?? [];
        $this->form_params = $params['forms'] ?? [];
        // $this->setCookies();
        // $this->setFormParams();
        $this->data = $this->getContent();
    }
    public function getContent()
    {
        return str_get_html($this->loadPage());
    }
    public function loadPage()
    {
        if ($_SERVER['HTTP_HOST'] == 'softiki.pw') {
            // $this->snoopy->proxy_host = '154.66.122.130';
            // $this->snoopy->proxy_port = '53281';
        }

        # шифруемся, пусть думают что мы пришли с гугла
        //$this->snoopy->referer = 'https://www.google.com.ua/webhp?hl=ru&sa=X&ved=' . bin2hex(random_bytes(36));
        # шифруемся, пусть думают что к ним пришел бот от google
        //$this->snoopy->agent = "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";
        # данные получаем отправляя пост запрос
        # на тот случай если понадобится ввести форму
        # например что то отправить для подверждения личности
        if ($this->form_params) {
            $this->snoopy->submit($this->url, $this->form_params);
        } else {
            $this->snoopy->fetch($this->url);
        }
        return $this->snoopy->results;
    }
    public function __destruct()
    {
        $this->data->clear();
        unset($this->data);
    }
}

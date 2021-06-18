<?php
// авторизация и проверка текущей сессии авторизиорван или нет
// работа с _csrf
namespace App\Service;

// подключаем Guzzle
use ErrorException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DribbbleAuth
{
    public $jar;
    public $client;
    public $current_content;
    public $current_code_response;
    public $curl_debug = 1;
    public $file_cookies = 'cookies.txt';

    public $headersPost = [
        'authority' => 'dribbble.com',
        'pragma' => 'no-cache',
        'cache-control' => 'no-cache',
        'upgrade-insecure-requests' => '1',
        'origin' => 'https://dribbble.com',
        'content-type' => 'application/x-www-form-urlencoded',
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'sec-fetch-site' => 'same-origin',
        'sec-fetch-mode' => 'navigate',
        'sec-fetch-user' => '?1',
        'sec-fetch-dest' => 'document',
        'referer' => 'https://dribbble.com/session/new',
        'accept-language' => 'en,ru;q=0.9,uk;q=0.8',
    ];

    public $headersGet = [
        'authority' => 'dribbble.com',
        'pragma' => 'no-cache',
        'cache-control' => 'no-cache',
        'upgrade-insecure-requests' => '1',
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'sec-fetch-site' => 'same-origin',
        'sec-fetch-mode' => 'navigate',
        'sec-fetch-user' => '?1',
        'sec-fetch-dest' => 'document',
        'referer' => 'https://dribbble.com/session/new',
        'accept-language' => 'en,ru;q=0.9,uk;q=0.8',
    ];

    public function __construct()
    {
        //https://docs.guzzlephp.org/en/stable/quickstart.html#cookies
        $this->jar = new \GuzzleHttp\Cookie\FileCookieJar($this->file_cookies, true);
        $this->client = new \GuzzleHttp\Client(['cookies' => true]);
    }

    // нулевой шаг читаем первую страницу для авторизации
    public function get_first_page_content($url)
    {
        try {
            // читаем первую страницу, собираем куки
            $response = $this->client->request('GET', $url, [
                    'headers' => $this->headersGet,
                    'cookies' => $this->jar,
                    //'debug' => $this->curl_debug ? true : false
                ]
            );
            $this->current_content = $response->getBody();
            $this->current_code_response = $response->getStatusCode();

        } catch (RequestException $e) {
            $error['error'] = $e->getMessage();
            $error['request'] = ($e->getRequest());
            if ($e->hasResponse()) {
                $error['response'] = ($e->getResponse());
            }

            // вылетаем фатал еррор
            MyHelper::warning('ошибка чтения ' . $url . ' страниц.' . print_r($error['error'], true), true);
            if ($this->curl_debug) {
                echo "\n*********************!!!!!! ошибка чтения $url страниц !!!! ********************\n";
            }
        }

    }

    // находим _csrf
    public function find_csrf_in_first_page_content()
    {
        $csrf_key = '';
        if (preg_match('~csrf-token" content="(.*?)"~', $this->current_content, $b))
            $csrf_key = $b[1];
        else {
            MyHelper::warning("Не вижу _csrf ***** " . $this->current_content, true);
            if ($this->curl_debug) echo "\n*********************!!!!!! Не вижу _csrf !!!! ********************\n";
        }
        return $csrf_key;
    }

    public function check_content_if_authorized()
    {
        // мы авторизированы? ищем признак в куках
        if ($this->jar->getCookieByName('has_logged_in') &&
            $this->jar->getCookieByName('has_logged_in')->getValue() === 'true') {
            echo "authorized";
            return true;
        }
        echo "not authorized";
        return false;
    }

    // аутентификация
    public function auth()
    {
        // читаем первую страницу
        // Очень важно что без JS клиент долже сначала сюда зайт ане неглавную
        $this->get_first_page_content('https://dribbble.com/session/new');

        echo "1";

        // - если авторизированы - проверим слетела ли сессия
        // - если НЕ авторизированы  получим csrf
//        if ($this->check_content_if_authorized()) return true;

        echo "2";

        // нет признаков авторизации на главной - авторизируемся и куки складываем в Jar
        $csrf_key = $this->find_csrf_in_first_page_content();

        echo "3";

        try {
            $response = $this->client->request(
                'POST',
                'https://dribbble.com/session/',
                [
                    'headers' => $this->headersPost,
                    'form_params' => [
                        'utf8' => '✓',
                        'authenticity_token' => $csrf_key,
                        'login' => 'tsergiiv@gmail.com',
                        'password' => '123456',
                    ],
                    'cookies' => $this->jar,
                    //'debug' => $this->curl_debug ? true : false
                ]);
            //echo $this->current_content = $response->getBody();

        } catch (Exception $e) {
            MyHelper::warning("Нас заблокировали?" . print_r($e, true), true);
        }

        echo "4";

        if ($this->check_content_if_authorized()) return true;

        echo "5";

        if ($this->curl_debug) echo "\n********************* НЕ МОГУ АВТОРИЗОВАТЬСЯ (возможно пароль не верный) ********************\n";
        MyHelper::warning("Проблемы авторизации - проверь логин пароль ****" . $this->current_content, true);
        return false;

    }
}

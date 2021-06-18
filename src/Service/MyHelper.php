<?php

namespace App\Service;
use Exception;

class MyHelper
{
    static function warning($message, $need_exeption = false)
    {
        echo date('Y-m-d H:i:s') . " " . $message . "\n";

        try {
            if ($need_exeption) throw new Exception($message);
        } catch (Exception $e) {
            echo "Не падаем - логируем ошибку в Sentry\n" . $e;
            //экспешен не выкидываем но засыпаем минимум на полчаса
            sleep(1300);
        }

    }
}

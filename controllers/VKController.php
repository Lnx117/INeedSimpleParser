<?php
require_once 'AbstractController.php';
require_once 'simple_html_dom.php';
//Логгер
use Mpakfm\Printu;
use Sunra\PhpSimple\HtmlDomParser;
use GuzzleHttp\Client;

class VKController extends AbstractController {

    public function getHtml($links) {

        //Создаем папку
        $folderPath = 'images/';
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        //Заголовки как для запроса из браузера
        //ВК ругается если думает что это скрипт
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru-RU,ru;q=0.9',
            'Cache-Control' => 'max-age=0',
            'Cookie' => 'remixlang=0; remixstlid=9122011710328198963_ZCPODM6NOlGmjKxXq3jZjia6c3JBcooZtJMw70Jq4Ps; remixlgck=02b28e9be08e9688fa; remixstid=882573531_cbV6JLhKRz5vOV1tWlYZQhuKADhy91iYCssxvOCeC5P; remixnp=0; remixscreen_width=1920; remixscreen_height=1080; remixscreen_dpr=1; remixscreen_depth=24; remixscreen_orient=1; remixscreen_winzoom=1; remixdark_color_scheme=0; remixcolor_scheme_mode=auto; remixdt=0; remixgp=ad65a25eb25d46af9f9f32f0f8e96179; tmr_lvid=0e3e3302255e1f4b7e3bffcc1e8d31a0; tmr_lvidTS=1694853779239; tmr_detect=0%7C1694853794642',
            'Sec-Ch-Ua' => '"Chromium";v="116", "Not)A;Brand";v="24", "Google Chrome";v="116"',
            'Sec-Ch-Ua-Mobile' => '?0',
            'Sec-Ch-Ua-Platform' => '"Windows"',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Sec-Fetch-User' => '?1',
            'Upgrade-Insecure-Requests' => '1',
        ];

        // Создаем клиент Guzzle
        $client = new Client();

        $chapterCounter = 0;

        foreach ($links as $url) {
            
            try {
                // Отправляем GET-запрос с заданными заголовками
                $response = $client->get($url, ['headers' => $headers]);
            
                // Получаем содержимое ответа
                $body = $response->getBody();

            } catch (Exception $e) {
                // Обработка ошибок, если они возникнут
                echo 'Произошла ошибка: ' . $e->getMessage();
            }

            $html = str_get_html( (string) $body, false, $context );
            $images = $html->find( "img" );

            //Создаем папку
            $folderPath = 'images/' . $chapterCounter . '/';
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $pageCounter = 1;
            foreach ($images as $img) {
                // Получаем URL изображения из атрибута src
                $imageUrl = $img->src;
            
                // Генерируем уникальное имя файла для изображения
                $filename = $pageCounter . '.png';
            
                // Полный путь к файлу на сервере
                $filePath = $folderPath . $filename;
            
                try {
                    // Скачиваем изображение и сохраняем его в папку
                    $fileContent = file_get_contents($imageUrl);
                    if ($fileContent !== false) {
                        file_put_contents($filePath, $fileContent);
                    }
                } catch (Exception $e) {
                    // Обработка ошибок, если они возникнут
                    $pageCounter ++;
                    continue;
                }

                $pageCounter ++;
                // Опционально, вы можете вывести имя сохраненного файла
                Printu::obj("Сохранено: глава - $chapterCounter, страница $pageCounter")->dt();
                sleep(1);
            }
            $chapterCounter++;
        }
    }
}
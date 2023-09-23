<?php
require_once __DIR__ . '\vendor\autoload.php';
require_once 'controllers/VKController.php';
require_once 'links.php';

//Логгер
use Mpakfm\Printu;  

//Устанавливаем путь до логгера
Printu::setPath('.\log');
set_time_limit(10000);


$VKController = new VKController;

$links = $linksArray;
$html = $VKController->getHtml($links);
?>

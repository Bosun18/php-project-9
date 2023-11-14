<?php

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Flash;
use Carbon\Carbon;
use Valitron\Validator;
use Bosun\PhpProject9\Database;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use DiDom\Document;
use Illuminate\Support;

// Путь который будет использован при глобальной установке пакета
$autoloadPath1 = __DIR__ . '/../../../autoload.php';
// Путь для локальной работы с проектом
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});
$container->set('pdo', function () {
    return Database::get()->connect();
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);
$router = $app->getRouteCollector()->getRouteParser();

// Обработчик
$app->get('/', function ($request, $response) {
    $this->get('pdo')->exec("CREATE TABLE urls (
                id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                name varchar(255) NOT NULL UNIQUE,
                created_at timestamp
            );");
    $this->get('pdo')->exec("CREATE TABLE url_checks (
                id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                url_id bigint REFERENCES urls (id),
                status_code int,
                h1 text,
                title text,
                description text,
                created_at timestamp
            );");
    return $this->get('renderer')->render($response, 'main.phtml');
})->setName('main');

$app->get('/urls', function ($request, $response) {
    return $this->get('renderer')->render($response, 'urls.phtml');
})->setName('urls');

$app->get('/urls/{id:[0-9]+}', function ($request, $response) {
    return $this->get('renderer')->render($response, 'show.phtml');
})->setName('show');

$app->run();

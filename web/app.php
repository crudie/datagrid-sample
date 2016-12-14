<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app['env'] = 'dev';
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src',
));

// Define services and repo
$app['service.mongo'] = function () use ($app) {
    return new \MongoDB\Client();
};

$app['repository.user'] = function () use ($app) {
    return new \Main\Repository\MongoUserRepository($app['service.mongo']);
};

$app['repository.transaction'] = function () use ($app) {
    return new \Main\Repository\MongoTransactionRepository($app['service.mongo']);
};

$app['form.handler.transaction'] = function () use ($app) {
    return new \Main\Form\Handler\TransactionFormHandler($app['form.factory'], $app['repository.transaction'], $app['repository.user']);
};

$app['controller.transaction'] = function () use ($app) {
    return new \Main\Controller\TransactionController($app['repository.transaction'], $app['form.handler.transaction']);
};

$app['controller.main'] = function () use ($app) {
    return new \Main\Controller\MainController($app['twig']);
};

$app->get('/api/transactions', 'controller.transaction:listAction');
$app->post('/api/transactions', 'controller.transaction:createAction');
$app->put('/api/transactions/{id}', 'controller.transaction:updateAction');
$app->delete('/api/transactions/{id}', 'controller.transaction:deleteAction');
$app->get('/', 'controller.main:indexAction');


$app->run();
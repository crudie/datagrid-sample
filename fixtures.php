<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();


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


$users = [
    new \Main\Model\User(null, 'Andrew'),
    new \Main\Model\User(null, 'Lisa'),
];
$transactions = [
    new \Main\Model\Transaction(null, $users[array_rand($users)], 1000, 'Earned'),
    new \Main\Model\Transaction(null, $users[array_rand($users)], -200, 'Bought some clothes'),
    new \Main\Model\Transaction(null, $users[array_rand($users)], -50, 'Bought some food'),
    new \Main\Model\Transaction(null, $users[array_rand($users)], 500, 'Gift from my mom')
];

foreach ($users as $user) {
    $app['repository.user']->save($user);
}

foreach ($transactions as $transaction) {
    $app['repository.transaction']->save($transaction);
}

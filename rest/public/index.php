<?php

require __DIR__ . '/../app/app.php';


$app->get('/', function ($request, $response) {
    return "Available services: ";
});

$app->run();

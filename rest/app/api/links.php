<?php

$app->get('/links/', function ($request, $response) {
    $testLink = new stdClass();
    $testLink->url="url";
    $testLink->hash="hash";
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($testLink));
});
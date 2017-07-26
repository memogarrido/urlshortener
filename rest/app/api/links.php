<?php

require __DIR__ . '/../models/ResponseStatus.php';
require __DIR__ . '/../utils/IntegerHash.php';
require __DIR__ . '/../models/DatabaseEntity.php';
require __DIR__ . '/../models/Link.php';

//Route to insert link on post
$app->post('/links/', function ($request, $response) {
    $link = new Link();
    if ($request->getParam('url')&&filter_var($request->getParam('url'), FILTER_VALIDATE_URL)) {
        $link->setUrlOrig($request->getParam('url'));
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($link->insertLink()));
    } else {
        $responseStatus = new ResponseStatus();
        $responseStatus->setStatus(-1);
        $responseStatus->setMessage("Necesitas ingresar una url valida a convertir");
        return $response->withStatus(400)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($responseStatus));
    }
});

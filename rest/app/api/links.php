<?php

require __DIR__ . '/../models/ResponseStatus.php';
require __DIR__ . '/../utils/IntegerHash.php';
require __DIR__ . '/../utils/Validator.php';
require __DIR__ . '/../models/Error.php';
require __DIR__ . '/../models/DatabaseEntity.php';
require __DIR__ . '/../models/Link.php';

//Route to insert link on post
$app->post('/links/', function ($request, $response) {
    $link = new Link();
    if ($request->getParam('hash') != null) {
        $link->setHash($request->getParam('hash'));
    }
    if ($request->getParam('url') && Validator::is_valid_url($request->getParam('url'))) {
        $link->setUrlOrig($request->getParam('url'));
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($link->insertLink()));
    } else {
        $responseStatus = new ResponseStatus();
        $responseStatus->setStatus(Error::ERROR_DATOS_ERRONEOS);
        $responseStatus->setMessage("Necesitas ingresar una url valida a convertir");
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($responseStatus));
    }
});

//Route to redirect link
$app->get('/{hash}', function ($request, $response) {
    $link = new Link();
    $link->setHash($request->getAttribute('hash'));
    $responseStatus = $link->fetchDestinationURL();
    if ($responseStatus->getStatus() == 0) {
        return $response->withRedirect($responseStatus->getLink()->getUrlOrig(), 301);
    } else {
        return $response->withStatus(404)
                        ->withHeader('Content-Type', 'text/html')
                        ->write('Page not found ' . $responseStatus->getMessage());
    }
});


//Route to get list of link pairs
$app->get('/links/', function ($request, $response) {
    $link = new Link();
    if ($request->getParam('offset') != null) {
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($link->getURLs($request->getParam('offset'))));
    } else {
        $responseStatus = new ResponseStatus();
        $responseStatus->setStatus(ERROR::ERROR_FALTARON_PARAMETROS);
        $responseStatus->setMessage("No se ha recibido el offset para mostrar resultados");
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($responseStatus));
    }
});

<?php

require __DIR__ . '/../app/app.php';


$app->get('/', function ($request, $response) {
    return "<div> <h2>Available Services</h2> <ul> <li> POST /links/ Insert new link </li> <li> GET /{hash} get hash and redirect </li> <li> GET /links/ get list of link pairs </li> </ul></div>";
});

$app->run();

<?php
require_once 'vendor/autoload.php';

$app = new\Slim\Slim();

$app->get("/primeraruta", function() use($app){
	echo "hola mundo desde la primera ruta";
});

$app->get("/segundaruta", function() use($app){
	echo "hola mundo desde la primera ruta";
});

$app->run();
<?php
require_once 'vendor/autoload.php';

$app = new\Slim\Slim();

$db = new mysqli('localhost', 'root', 'root', 'angularapirest');

$app->get("/primeraruta", function() use($app, $db){
	echo "hola mundo desde la primera ruta";
	var_dump($db);
});

$app->get("/segundaruta", function() use($app){
	echo "hola mundo desde la segunda ruta";
});

$app->post('/productos', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

		if(!isset($data['nombre'])){
		$data['nombre']=null;
	}

	if(!isset($data['descripcion'])){
		$data['descripcion']=null;
	}

	if(!isset($data['precio'])){
		$data['precio']=null;
	}

	if(!isset($data['imagen'])){
		$data['imagen']=null;
	}


	$query = "INSERT INTO productos VALUES(NULL,".
	"'{$data['nombre']}',".
	"'{$data['descripcion']}',".
	"'{$data['precio']}',".
	"'{$data['imagen']}'".
	");";

	$insert = $db->query($query);

		$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'El producto NO se ha creado correctamente'
		);

	if($insert){
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'El producto creado correctamente'
			);
	}
	
	echo json_encode($result);
});


$app->run();
<?php
require_once 'vendor/autoload.php';

$app = new\Slim\Slim();

$db = new mysqli('localhost', 'root', 'root', 'angularapirest');

// CONFIGURACION DE CABECERAS PARA PERMITIR ACCESO CORS

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

$app->get("/primeraruta", function() use($app, $db){
	echo "hola mundo desde la primera ruta";
	var_dump($db);
});

$app->get("/segundaruta", function() use($app){
	echo "hola mundo desde la segunda ruta";
});


// LISTAR TODOS LOS PRODUCTOS

$app->get('/productos', function() use($db, $app){
	$sql = 'SELECT * FROM productos ORDER BY id DESC;';
	$query = $db->query($sql);

	$productos = array();
	while ($producto = $query->fetch_assoc()) {
		$productos[] = $producto;
	}

	$result = array(
		'status' 	=> 'success',
		'code' 		=> 200,
		'data'		=> $productos
		);

	echo json_encode($result);

});


// DEVOLVER UN SOLO PRODUCTO

$app->get('/productos/:id', function($id) use($db, $app){
	$sql = 'SELECT * FROM productos WHERE id = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' 	=> 'error',
		'code' 		=> 404,
		'data' 		=> 'Producto no disponible'
		);

	if ($query->num_rows == 1) {
		$producto = $query->fetch_assoc();

	$result = array(
		'status' 	=> 'success',
		'code' 		=> 200,
		'data' 		=> $producto
		);
	}

	echo json_encode($result);

});


// ELIMINAR UN SOLO PRODUCTO

$app->get('/delete-productos/:id', function($id) use($db, $app){
	$sql = 'DELETE FROM productos WHERE id = '.$id;
	$query = $db->query($sql);

		if($query){
			$result = array(
			'status' 	=> 'success',
			'code' 		=> 200,
			'data' 		=> 'El producto se ha eliminado correctamente!!'
			);
		}else{
			$result = array(
			'status' 	=> 'error',
			'code' 		=> 404,
			'data' 		=> 'El producto no se ha eliminado!!'
			);
		}

		echo json_encode($result);
});


// ACTUALIZAR UN PRODUCTO

$app->post('/update-productos/:id', function($id) use($db, $app){

	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$sql = "UPDATE productos SET ".
		   "nombre = '{$data["nombre"]}',".
		   "descripcion = '{$data["descripcion"]}',";

		   if (isset($data['imagen'])) {
		   $sql .= "imagen = '{$data["imagen"]}',";
		   }

		   $sql .= "precio = '{$data["precio"]}' WHERE id = {$id}";

    $query = $db->query($sql);

		if($query){
				$result = array(
				'status' 	=> 'success',
				'code' 		=> 200,
				'data' 		=> 'El producto se ha actualizado correctamente!!'
				);
		}else{
				$result = array(
				'status' 	=> 'error',
				'code' 		=> 404,
				'data' 		=> 'El producto no se ha actualizado!!'
				);
			}

			echo json_encode($result);
});


// SUBIR UNA IMAGEN A UN PRODUCTO

$app->post('/upload-file', function() use($db, $app){

	$result = array(
				'status' 	=> 'error',
				'code' 		=> 404,
				'data' 		=> 'El archivo no ha podido subirse'
				);

	if (isset($_FILES['uploads'])) {
		$piramideUploader = new PiramideUploader();


		$upload = $piramideUploader->upload('image', 'uploads', 'uploads', array('image/jpeg', 'image/png', 'image/gif'));
		$file = $piramideUploader->getInfoFile();
		$file_name = $file['complete_name'];

		if (isset($upload) && $upload['uploaded'] == false) {
			$result = array(
				'status' 	=> 'error',
				'code' 		=> 404,
				'data' 		=> 'El archivo no ha podido subirse'
				);
		}else{
			$result = array(
				'status' 	=> 'success',
				'code' 		=> 200,
				'data' 		=> 'El archivo se ha subido correctamente',
				'filename'  => $file_name
				);
		}

	}

	echo json_encode($result);

});


// GUARDAR PRODUCTOS EN LA BASE DE DATOS

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
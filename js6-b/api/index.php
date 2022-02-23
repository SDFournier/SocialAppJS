<?php

require_once '../config/jwt_helper.php';
require_once '../config/db.php';

define('ALGORITMO', 'HS512'); // Algoritmo de codificación/firma
define('SECRET_KEY', 'AS..-.DJKLds·ak$dl%Ll!3kj12l3k1sa4_ÑÑ312ñ12LK3Jj4DK5A6LS7JDLK¿?asDqiwUEASDL,NMQWIEUIO'); //String largo y "complicado"

if (!isset($_GET['accion'])) {
    outputError();
}

$metodo = strtolower($_SERVER['REQUEST_METHOD']);
$accion = explode('/', strtolower($_GET['accion']));
$funcionNombre = $metodo . ucfirst($accion[0]);
$parametros = array_slice($accion, 1);
if (count($parametros) >0 && $metodo == 'get') {
    $funcionNombre = $funcionNombre.'ConParametros';
}
if (function_exists($funcionNombre)) {
    call_user_func_array ($funcionNombre, $parametros);
} else {
    outputError(400);
}

function outputJson($data, $codigo = 200)
{
    header('', true, $codigo);
    header('Content-type: application/json');
    print json_encode($data);
}

function outputError($codigo = 500, $mensaje=false)
{
    switch ($codigo) {
        case 400:
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad request", true, 400);
            break;
        case 404:
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            break;
        case 401:
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized", true, 401);
            break;
        default:
            header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
            break;
    }
    if ($mensaje!==false) {
        print json_encode(['error'=>$mensaje]);
    }
    die;
}

function conectarBD()
{
    $link = mysqli_connect(DBHOST, DBUSER, DBPASS, DBBASE);
    if ($link === false) {
        print "Falló la conexión: " . mysqli_connect_error();
        outputError(500);
    }
    return $link;
}

function requireLogin () {
    $authHeader = getallheaders();
    try
    {
        list($jwt) = @sscanf( $authHeader['Authorization'], 'Bearer %s');
        $datos = JWT::decode($jwt, SECRET_KEY, ALGORITMO);
        $datos = [
            'id' => $datos->id,
            'expira' => $datos->expira,
        ];
        if (time() > $datos['expira']) {
            postLogout();
            throw new Exception("Token expirado", 1);
        }
        $link = conectarBD();
        $resultado = mysqli_query($link, "SELECT 1 FROM tokens WHERE token = '$jwt'");
        if (!($resultado && mysqli_num_rows($resultado)==1)) {
            throw new Exception("Token inválido", 1);
        }
        mysqli_close($link);
    } catch(Exception $e) {
        outputError(401);
    }
    return $datos;
}

function postLogin() {
    $loginData = json_decode(file_get_contents("php://input"), true);
    $link = conectarBD();

    $email = mysqli_real_escape_string($link, $loginData['email']);
    $clave = mysqli_real_escape_string($link, $loginData['clave']);

    $sql = "SELECT id FROM usuarios WHERE email='$email' AND clave='$clave'";
    $resultado = mysqli_query($link, $sql);
    if($resultado && mysqli_num_rows($resultado)==1) {
        $res = mysqli_fetch_assoc($resultado);
        $data = [
            'expira'    => time() + 3600,
            'id'        => $res['id']+0,
        ];
        $jwt = JWT::encode($data, SECRET_KEY, ALGORITMO);
        if (mysqli_query($link, "INSERT INTO tokens (token) VALUES ('$jwt')")) {
            outputJson(['jwt' => $jwt]);
            die;
        }
    }
    outputError(401);
}

function postLogout() {
    $link = conectarBD();
    $authHeader = getallheaders();
    list($jwt) = @sscanf( $authHeader['Authorization'], 'Bearer %s');
    if (!mysqli_query($link, "DELETE FROM tokens WHERE token = '$jwt'")) {
        throw new Exception("Token inválido", 1);
    }
    mysqli_close($link);
}

function getContactos()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    $sql = "SELECT id, nombre, apellido, email, compartido, 1 AS editable FROM contactos WHERE id_usuario=$idUsuario
    UNION ALL 
    SELECT id, nombre, apellido, email, compartido, 0 AS editable FROM contactos WHERE id_usuario!=$idUsuario AND compartido=1";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    $ret = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $ret[] = [
            'id'         => $fila['id'],
            'apellido'   => $fila['apellido'],
            'nombre'     => $fila['nombre'],
            'compartido' => $fila['compartido']+0 ? true : false,
            'editable'   => $fila['editable']+0 ? true : false,
            'email'      => $fila['email']
        ];
    }
    mysqli_free_result($resultado);
    mysqli_close($link);
    outputJson($ret);
}

function getContactosConParametros($id)
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];
    $id+=0;
    $link = conectarBD();
    $sql = "SELECT * FROM contactos WHERE id=$id AND id_usuario=$idUsuario";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    if (mysqli_num_rows($resultado) == 0) {
        outputError(404);
    }

    $ret = mysqli_fetch_assoc($resultado);

    mysqli_free_result($resultado);
    mysqli_close($link);
    outputJson($ret);
}

function postContactos()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    $dato = json_decode(file_get_contents('php://input'), true);
    
    $nombre = mysqli_real_escape_string($link, $dato['nombre']);
    $apellido = mysqli_real_escape_string($link, $dato['apellido']);
    $email = mysqli_real_escape_string($link, $dato['email']);
    $domicilio = isset($dato['domicilio']) ? ("'" . mysqli_real_escape_string($link, $dato['domicilio']) . "'") : 'NULL';
    if (isset($dato['fecha_de_nacimiento'])) {
        list($anio, $mes, $dia) = explode('-', substr($dato['fecha_de_nacimiento'], 0, 10));
        settype($anio, 'integer');
        settype($mes, 'integer');
        settype($dia, 'integer');
        if(!checkdate($mes, $dia, $anio)) {
            outputError(400);
        }
        $fdn = "'".substr($dato['fecha_de_nacimiento'], 0, 10)."'";
    } else {
        $fdn = "NULL";
    }
    
    $sql = "INSERT INTO contactos (nombre, apellido, email, fecha_de_nacimiento, domicilio, id_usuario) VALUES ('$nombre', '$apellido', '$email', $fdn, $domicilio, $idUsuario)";
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }

    $ret = [
        'id' => mysqli_insert_id($link)
    ];

    mysqli_close($link);
    outputJson($ret, 201);
}

function patchContactos($id)
{
    $datosUsuario = requireLogin();
    $id += 0;
    $link = conectarBD();
    $idUsuario = $datosUsuario['id'];

    // Verifica si el usuario tiene permiso de modificar este contacto
    $sql = "SELECT 1 FROM contactos WHERE id=$id AND id_usuario=$idUsuario LIMIT 1";
    $resultado = mysqli_query($link, $sql);
    if (mysqli_num_rows($resultado)!=1) {
        outputError(401);
    }

    $dato = json_decode(file_get_contents('php://input'), true);
    
    $nombre = mysqli_real_escape_string($link, $dato['nombre']);
    $apellido = mysqli_real_escape_string($link, $dato['apellido']);
    $email = mysqli_real_escape_string($link, $dato['email']);
    $domicilio = isset($dato['domicilio']) ? ("'" . mysqli_real_escape_string($link, $dato['domicilio']) . "'") : 'NULL';
    if (isset($dato['fecha_de_nacimiento'])) {
        list($anio, $mes, $dia) = explode('-', substr($dato['fecha_de_nacimiento'], 0, 10));
        settype($anio, 'integer');
        settype($mes, 'integer');
        settype($dia, 'integer');
        if(!checkdate($mes, $dia, $anio)) {
            outputError(400);
        }
        $fdn = "'".substr($dato['fecha_de_nacimiento'], 0, 10)."'";
    } else {
        $fdn = "NULL";
    }
    
    $sql = "UPDATE contactos SET nombre = '$nombre', apellido = '$apellido', email = '$email', fecha_de_nacimiento = $fdn, domicilio = $domicilio WHERE id = $id";
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }

    $ret = [];

    mysqli_close($link);
    outputJson($ret, 201);
}

function deleteContactos($id)
{
    $datosUsuario = requireLogin();
    $id += 0;
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    // Verifica si el usuario tiene permiso de modificar este contacto
    $sql = "SELECT 1 FROM contactos WHERE id=$id AND id_usuario=$idUsuario LIMIT 1";
    $resultado = mysqli_query($link, $sql);
    if (mysqli_num_rows($resultado)!=1) {
        outputError(401);
    }

    $sql = "SELECT id FROM contactos WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    if (mysqli_num_rows($resultado) == 0) {
        outputError(404);
    }
    mysqli_free_result($resultado);
    $sql = "DELETE FROM contactos WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    mysqli_close($link);
    outputJson([]);
}

function patchPerfil($id)
{
    $payload = requireLogin();
    settype($id, 'integer');

    if($id !== $payload->id) { // Impide modificar perfil no propio
        outputError(401);
    }
    $link = conectarBD();
    $dato = json_decode(file_get_contents('php://input'), true);
    
    $clave = mysqli_real_escape_string($link, $dato['clave']);
    $repetirClave = mysqli_real_escape_string($link, $dato['repetirClave']);
    if($clave=='' || $repetirClave!=$clave) {
        outputError(400);
    }

    $sql = "UPDATE usuarios SET clave = '$clave' WHERE id = $id";
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }

    $ret = [];

    mysqli_close($link);
    outputJson($ret, 201);
}

function postUsuarios()
{
    $link = conectarBD();
    $dato = json_decode(file_get_contents('php://input'), true);
    
    $email = mysqli_real_escape_string($link, $dato['email']);
    $clave = mysqli_real_escape_string($link, $dato['clave']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        outputError(400, 'El e-mail ingresado es incorrecto');
    }

    if ($clave=='') {
        outputError(400, 'La clave no puede estar vacía');
    }
    
    $sql = "SELECT 1 FROM usuarios WHERE email='$email' LIMIT 1";
    $resultado = mysqli_query($link, $sql);
    if (mysqli_num_rows($resultado)==1) {
        outputError(400, 'El e-mail ingresado ya está registrado');
    }

    $sql = "INSERT INTO usuarios (email, clave) VALUES ('$email', '$clave')";

    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }

    $ret = [
        'id' => mysqli_insert_id($link)
    ];

    mysqli_close($link);
    outputJson($ret, 201);
}


function patchCompartir($id)
{
    $datosUsuario = requireLogin();
    $id += 0;
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    // Verifica si el usuario tiene permiso de modificar este contacto
    $sql = "SELECT 1 FROM contactos WHERE id=$id AND id_usuario=$idUsuario LIMIT 1";
    $resultado = mysqli_query($link, $sql);
    if (mysqli_num_rows($resultado)!=1) {
        outputError(401);
    }

    $sql = "UPDATE contactos SET compartido=1 WHERE id=$id AND id_usuario=$idUsuario";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        print mysqli_error($link);
        outputError(500);
        die;
    }
    mysqli_close($link);
    outputJson([]);
}

function deleteCompartir($id)
{
    $datosUsuario = requireLogin();
    $id += 0; //id contacto
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    // Verifica si el usuario tiene permiso de modificar este contacto
    $sql = "SELECT 1 FROM contactos WHERE id=$id AND id_usuario=$idUsuario LIMIT 1";
    $resultado = mysqli_query($link, $sql);
    if (mysqli_num_rows($resultado)!=1) {
        outputError(401);
    }

    $sql = "SELECT id FROM contactos WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    if (mysqli_num_rows($resultado) == 0) {
        outputError(404);
    }
    mysqli_free_result($resultado);
    $sql = "UPDATE contactos SET compartido=0 WHERE id=$id AND id_usuario=$idUsuario";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    mysqli_close($link);
    outputJson([]);
}


function getMensajes()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();

    if($idUsuario==99){
        $sql = "select * from mensajes group by mensajes.id";
    }else{
    $sql = "SELECT mensajes.id as id, mensajes.tema as tema, mensajes.texto as texto, mensajes.fecha as fecha, mensajes_destinatarios.id_usuario as destinatarios_id, usuarios.nombre as destinatarios_nombre FROM mensajes left join mensajes_destinatarios ON mensajes.id=mensajes_destinatarios.id_mensaje LEFT JOIN usuarios ON usuarios.id=mensajes_destinatarios.id_usuario WHERE mensajes.id_usuario=$idUsuario GROUP BY mensajes.id;";
    }

    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);

        die;
    }
    $ret = [];
    

    while($fila = mysqli_fetch_assoc($resultado)) {
        
        
        $ret[] = $fila;
    }
    mysqli_free_result($resultado);
    mysqli_close($link);
    outputJson($ret);
}

function getMensajesrecibidos()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    $sql = "SELECT mensajes.id as id, mensajes.tema as tema, mensajes.texto as texto, mensajes.fecha as fecha, mensajes_destinatarios.id_usuario as destinatarios_id, usuarios.nombre as nombre_enviador, usuarios.id as id_enviador FROM usuarios left join mensajes ON mensajes.id_usuario=usuarios.id LEFT JOIN mensajes_destinatarios ON mensajes_destinatarios.id_mensaje=mensajes.id WHERE mensajes_destinatarios.id_usuario=$idUsuario GROUP BY mensajes.id;
";

    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);

        die;
    }
    $ret = [];
    

    while($fila = mysqli_fetch_assoc($resultado)) {
        
        
        $ret[] = $fila;
    }
    mysqli_free_result($resultado);
    mysqli_close($link);
    outputJson($ret);
}

function postMensaje()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    $dato = json_decode(file_get_contents('php://input'), true);

    $tema = mysqli_real_escape_string($link, $dato['tema']);
    $texto = mysqli_real_escape_string($link, $dato['texto']);
    

    
   
    
    $sql = "INSERT INTO mensajes (tema, texto, id_usuario, fecha) VALUES ('$tema', '$texto', '$idUsuario', SYSDATE() )";
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }

    $ret = [
        'id' => mysqli_insert_id($link)
    ];


    $id_mensaje=mysqli_insert_id($link);


    $id_destinatarios = mysqli_real_escape_string($link, $dato['destinatarios']);
    
    
    
    $sql="insert into mensajes_destinatarios (id_mensaje, id_usuario) values ($id_mensaje, $id_destinatarios);";
    $resultado=mysqli_query ($link, $sql);
    if($resultado===false){
        
        outputError(500);
    }
    

    mysqli_close($link);
    outputJson($ret, 201);
}


function deleteMensaje($id)
{
    $datosUsuario = requireLogin();
    $id += 0;
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();


    $sql = "SELECT id FROM mensajes WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    if (mysqli_num_rows($resultado) == 0) {
        outputError(404);
    }
    mysqli_free_result($resultado);
    $sql = "DELETE FROM mensajes WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    mysqli_close($link);
    outputJson([]);
}








function getImagenes(){

    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    

    $sql = "select id, id_usuario, path_imagen, descripcion from imagenes where id_usuario=$idUsuario;";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        print "Falló la consulta: " . mysqli_error($link);
        outputError(500);
        die;
    }
    $ret = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        
        
        $ret[] = $fila;
    }
    mysqli_close($link);
    mysqli_free_result($resultado);
    outputJson($ret);

}


function postImagen()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    
    $dato = json_decode(file_get_contents('php://input'), true);
    $descripcion = mysqli_real_escape_string($link, $dato['descripcion']);
    $path_imagen = mysqli_real_escape_string($link, $dato['path_imagen']);
    

    
   
    
    $sql = "INSERT INTO imagenes (id_usuario, path_imagen, descripcion) VALUES ('$idUsuario','$path_imagen', '$descripcion');";
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }

    $ret = [
        'id' => mysqli_insert_id($link)
    ];


    $id_imagen=mysqli_insert_id($link);


    
    

    mysqli_close($link);
    outputJson($ret, 201);
}


function deleteImagen($id)
{
    $datosUsuario = requireLogin();
    $id += 0;
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    

    $sql = "SELECT id FROM imagenes WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    if (mysqli_num_rows($resultado) == 0) {
        outputError(404);
    }
    mysqli_free_result($resultado);
    $sql = "DELETE FROM imagenes WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    mysqli_close($link);
    outputJson([]);
}


function getPosts(){

    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    
    if($idUsuario!=99){
        $sql = "select usuarios.nombre as nombre, usuarios.foto_perfil as foto_perfil, usuarios.id as id_dueño, amistades.id_usuario_amigo, posts.id as id, posts.titulo as titulo, posts.texto as texto, imagenes.id as id_imagen, imagenes.path_imagen as path_imagen, imagenes.descripcion as descripcion from usuarios left join amistades ON usuarios.id=amistades.id_usuario_amigo left join posts ON amistades.id_usuario_amigo=posts.id_usuario left join imagenes ON posts.id_imagen=imagenes.id where amistades.id_usuario=$idUsuario AND posts.visible=1;";
    }else{
        $sql = "select usuarios.nombre as nombre, usuarios.foto_perfil as foto_perfil, usuarios.id as id_dueño, posts.id as id, posts.titulo as titulo, posts.texto as texto, imagenes.id as id_imagen, imagenes.path_imagen as path_imagen, imagenes.descripcion as descripcion from usuarios left join posts ON usuarios.id=posts.id_usuario left join imagenes ON posts.id_imagen=imagenes.id group by id;";
    }
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        print "Falló la consulta: " . mysqli_error($link);
        outputError(500);
        die;
    }
    $ret = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        
        
        $ret[] = $fila;
    }
    mysqli_close($link);
    mysqli_free_result($resultado);
    outputJson($ret);

}


function postPost()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    
    $dato = json_decode(file_get_contents('php://input'), true);
    
    $titulo = mysqli_real_escape_string($link, $dato['titulo']);
    $texto = mysqli_real_escape_string($link, $dato['texto']);

    

    
    if(!isset($dato['path_imagen'])||$dato['path_imagen']==null){
       $sql = "INSERT INTO posts (titulo, texto, imagen, id_usuario, visible) VALUES ('$titulo','$texto', null, '$idUsuario', 1);";

        $resultado = mysqli_query($link, $sql);
        if ($resultado === false) {
            outputError(500);
            die;
        }

        $ret = [
            'id' => mysqli_insert_id($link)
        ];

        mysqli_close($link);
        outputJson($ret, 201);

    }else{
        $descripcion = mysqli_real_escape_string($link, $dato['descripcion']);
        $path_imagen = mysqli_real_escape_string($link, $dato['path_imagen']);
        $sql = "INSERT INTO imagenes (id_usuario, path_imagen, descripcion) VALUES ('$idUsuario','$path_imagen', '$descripcion');";

        $resultado = mysqli_query($link, $sql);
        if ($resultado === false) {
            outputError(500);
            die;
        }

        $ret = [
            'id' => mysqli_insert_id($link)
        ];


        $id_imagen=mysqli_insert_id($link);
        mysqli_close($link);
        $link = conectarBD();
        $sql = "INSERT INTO posts (titulo, texto, id_imagen, id_usuario, visible) VALUES ('$titulo','$texto', '$id_imagen', '$idUsuario', 1);";

        $resultado = mysqli_query($link, $sql);
        if ($resultado === false) {
            outputError(500);
            die;
        }

        $ret = [
            'id' => mysqli_insert_id($link)
        ];

        mysqli_close($link);
        outputJson($ret, 201);
        
    }


}






function getMispostsConParametros($id){

    $datosUsuario = requireLogin();
    $id += 0; //id contacto
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    
                        //posts de un amigo
    if($id!=0){
        
        $sql = "select usuarios.nombre as nombre, usuarios.id as id_dueño, amistades.id_usuario_amigo, posts.id as id, posts.titulo as titulo, posts.texto as texto, imagenes.id as id_imagen, imagenes.path_imagen as path_imagen, imagenes.descripcion as descripcion from usuarios left join amistades ON usuarios.id=amistades.id_usuario_amigo left join posts ON amistades.id_usuario_amigo=posts.id_usuario left join imagenes ON posts.id_imagen=imagenes.id where amistades.id_usuario=$idUsuario AND posts.visible=1;";

    }else{               //posts mios
        $sql = "select usuarios.nombre as nombre, usuarios.id as id_usuario_amigo, posts.id as id, posts.titulo as titulo, posts.texto as texto, imagenes.id as id_imagen, imagenes.path_imagen as path_imagen, imagenes.descripcion as descripcion from usuarios left join posts ON usuarios.id=posts.id_usuario left join imagenes ON posts.id_imagen=imagenes.id where usuarios.id=$idUsuario;";
    }
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        print "Falló la consulta: " . mysqli_error($link);
        outputError(500);
        die;
    }
    $ret = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        
        
        $ret[] = $fila;
    }
    mysqli_close($link);
    mysqli_free_result($resultado);
    outputJson($ret);

}




function getUsuarioConParametros($id){

    $datosUsuario = requireLogin();
    $id += 0; //id contacto
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    
    if($id!=0){
        $sql = "select usuarios.nombre from usuarios left join amistades ON usuarios.id=amistades.id_usuario_amigo where amistades.id_usuario=$idUsuario AND usuarios.id=$id;";
    }else{
        $sql ="select usuarios.nombre from usuarios where usuarios.id=$idUsuario";
    }
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        print "Falló la consulta: " . mysqli_error($link);
        outputError(500);
        die;
    }
    $ret = mysqli_fetch_assoc($resultado);
    mysqli_close($link);
    mysqli_free_result($resultado);
    outputJson($ret);

}


function getAmigos()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    $sql = "select usuarios.id, usuarios.nombre, usuarios.email, usuarios.foto_perfil from usuarios left join amistades ON usuarios.id=amistades.id_usuario_amigo where amistades.id_usuario=$idUsuario;";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    $ret=[];
    while($fila = mysqli_fetch_assoc($resultado)) {
        
        
        $ret[] = $fila;
    }
    mysqli_free_result($resultado);
    mysqli_close($link);
    outputJson($ret);
}



function deleteAmigos($id)
{
    $datosUsuario = requireLogin();
    $id += 0;
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    $sql = "SELECT id FROM amistades WHERE id_usuario=$idUsuario AND id_usuario_amigo=$id
    UNION ALL 
    SELECT id FROM amistades WHERE id_usuario=$id AND id_usuario_amigo=$idUsuario";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
   
    

    mysqli_free_result($resultado);
    mysqli_close($link);

    $link = conectarBD();
    $sql = "DELETE FROM amistades WHERE id_usuario=$idUsuario AND id_usuario_amigo=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    
    mysqli_close($link);

    borrarAmistad2($id);
    outputJson([]);
}


function borrarAmistad2($id){
    $datosUsuario = requireLogin();
    $id += 0;
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    $sql = "DELETE FROM amistades WHERE id_usuario=$id AND id_usuario_amigo=$idUsuario";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    
    mysqli_close($link);

}


function postAmigos()
{
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];

    $link = conectarBD();
    $dato = json_decode(file_get_contents('php://input'), true);
    
    $email = mysqli_real_escape_string($link, $dato['email']);
    
    $sql = "select nombre, id, foto_perfil from usuarios where email='$email';";
    $resultado = mysqli_query($link, $sql);
    
    if (mysqli_num_rows($resultado) == 0) {
        outputError(404);
    }
    if(mysqli_num_rows($resultado) == 1){
        mysqli_free_result($resultado);

        $sql = "INSERT INTO amistades (id_usuario, id_usuario_amigo)
        SELECT $idUsuario, id
        FROM usuarios
        WHERE email='$email';";

        $resultado = mysqli_query($link, $sql);
        if ($resultado === false) {
            outputError(500);
            die;
        }
        
        
        

        $sql = "INSERT INTO amistades (id_usuario, id_usuario_amigo)
        SELECT id, $idUsuario
        FROM usuarios
        WHERE email='$email';";

        $resultado = mysqli_query($link, $sql);
        
        if ($resultado === false) {
            outputError(500);
            die;
        }
        
        

    }
    
    

    $ret = [
       
    ];

    mysqli_close($link);
    outputJson($ret, 201);
}


function patchFoto()
{
    $datosUsuario = requireLogin();
    $link = conectarBD();
    $idUsuario = $datosUsuario['id'];

    

    $dato = json_decode(file_get_contents('php://input'), true);
    
    $path_imagen = mysqli_real_escape_string($link, $dato['path_imagen']);

    $sql = "UPDATE usuarios SET foto_perfil ='$path_imagen' WHERE id = $idUsuario";
    
    $resultado = mysqli_query($link, $sql);
    
    if ($resultado === false) {
        outputError(500);
        die;
    }

    $ret = [];

    mysqli_close($link);
    outputJson($ret, 201);
}


function getDatosperfil(){
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    $sql = "SELECT nombreFROM usuarios WHERE id=$idUsuario";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
   
    $ret=mysqli_fetch_assoc($resultado);

    mysqli_free_result($resultado);
    mysqli_close($link);

    outputJson($ret);

}


function getQuiensoy(){
    $datosUsuario = requireLogin();
    $idUsuario = $datosUsuario['id'];
    
    

    outputJson($idUsuario);

}


function deletePost($id)
{
    $datosUsuario = requireLogin();
    $id += 0;
    $idUsuario = $datosUsuario['id'];
    $link = conectarBD();

    // Verifica si el usuario tiene permiso de modificar este post
    $sql = "SELECT 1 FROM posts WHERE id=$id AND id_usuario=$idUsuario LIMIT 1";
    $resultado = mysqli_query($link, $sql);
    if (mysqli_num_rows($resultado)!=1) {
        if($idUsuario!=99)
        {
            outputError(401);
        }
        
    }

    $sql = "SELECT id FROM posts WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    if (mysqli_num_rows($resultado) == 0) {
        outputError(404);
    }
    mysqli_free_result($resultado);
    $sql = "DELETE FROM posts WHERE id=$id";
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }
    mysqli_close($link);
    outputJson([]);
}



function patchPost()
{
    $datosUsuario = requireLogin();
    $link = conectarBD();
    $idUsuario = $datosUsuario['id'];
    
    $visible=0;
    $dato = json_decode(file_get_contents('php://input'), true);
    
    $id = mysqli_real_escape_string($link, $dato['idd']);
    

        //vemos si esta o no compartido el post
    $sql ="UPDATE posts SET visible = $visible WHERE id = $id";
    
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) {
        outputError(500);
        die;
    }




    mysqli_close($link);
    outputJson($ret, 201);
}

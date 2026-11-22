<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/src/core/Router.php");
require_once(__DIR__ . "/src/core/Database.php");

require_once(__DIR__ . "/src/controllers/AuthController.php");
require_once(__DIR__ . "/src/controllers/PagesController.php");

/*
require_once(__DIR__ . "/src/controllers/NavgationAdmController.php");
require_once(__DIR__ . "/src/controllers/ContentController.php");
require_once(__DIR__ . "/src/controllers/PlayerController.php");
require_once(__DIR__ . "/src/controllers/PlaylistController.php");*/



$router = new Router();

// o nome a rota deve estar em minusculo

//MINHAS ROTAS GET

$router->get("/",[PagesController::class,"init"]);
$router->get("/index",[PagesController::class,"init"]);
$router->get("/login",[AuthController::class,"login"]);
$router->get("/signup",[AuthController::class,"signup"]);
$router->get("/password",[AuthController::class,"password"]);
$router->get("/enviados",[AuthController::class,"enviados"]);
$router->get("/importantes",[AuthController::class,"importantes"]);
$router->get("/lixeira",[AuthController::class,"lixeira"]);

//MINHAS ROTAS POST





/*
$router->get("/dashboard",[NavgationAdmController::class,"dashboard"]);
$router->get("/playlists",[NavgationAdmController::class,"playlists"]);
$router->get("/playlistconfig",[NavgationAdmController::class,"playlistConfig"]);


//post method 
$router->post("/update",[ContentController::class,"update"]);
$router->post("/deletecontent",[ContentController::class,"deleteContent"]);
$router->post("/registerplayer",[PlayerController::class,"registerPlayer"]);

$router->post("/updateplayer",[PlayerController::class,"updatePlayer"]);

$router->post("/associate",[PlaylistController::class,"associate"]);

$router->post("/createplaylist",[PlaylistController::class,"createPlaylist"]);

$router->post("/deleteplaylist",[PlaylistController::class,"deletePlaylist"]);




//testando a conexão
*/
try{
    $conectInst = new Database();
    $conectInst->getConnection();
    echo "<p style='color:green;'>Conexão com o banco de dados: " . DB_NAME . ". </p>";

}catch( Exception $e){
    echo "<p style='color:red;'>Erro ao conectar com o banco de dados: " . $e->getMessage() . "</p>";
    throw new Exception("Algo deu errado! -  Conexão com o banco NÃO estabelecida");
}


$router->dispach();

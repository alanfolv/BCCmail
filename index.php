<?php
// Inicializar sessão antes de qualquer saída
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$router->get("/",[AuthController::class,"login"]);
$router->get("/index",[PagesController::class,"init"]);
$router->get("/login",[AuthController::class,"login"]);
$router->get("/signup",[AuthController::class,"signup"]);
$router->get("/password",[AuthController::class,"password"]);
$router->get("/enviados",[PagesController::class,"enviados"]);
$router->get("/importantes",[PagesController::class,"importantes"]);
$router->get("/lixeira",[PagesController::class,"lixeira"]);

$router->get("/setup",[AuthController::class,"setup"]);

$router->get("/api/emails",[PagesController::class, "listarEmailsJson"]);
$router->get("/index.html",[PagesController::class,"init"]);
$router->get("/login.html",[AuthController::class,"login"]);
$router->get("/signup.html",[AuthController::class,"signup"]);
$router->get("/password.html",[AuthController::class,"password"]);
$router->get("/enviados.html",[PagesController::class,"enviados"]);
$router->get("/importantes.html",[PagesController::class,"importantes"]);
$router->get("/lixeira.html",[PagesController::class,"lixeira"]);

$router->get("/setup",[AuthController::class,"setup"]);


//MINHAS ROTAS POST

$router->post("/login",[AuthController::class,"login"]);
$router->post("/signup",[AuthController::class,"signup"]);
$router->post("/enviaremail",[PagesController::class,"enviaremail"]);





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
try {
    $conectInst = new Database();
    $conectInst->getConnection();

    error_log("Conexão com o banco de dados: " . DB_NAME);

} catch (Exception $e) {

    error_log("Erro ao conectar com o banco: " . $e->getMessage());

    http_response_code(500);
    exit("Erro ao conectar com o banco de dados.");
}
$router->dispach();

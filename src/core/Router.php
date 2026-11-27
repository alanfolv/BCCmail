<?php

require_once __DIR__ . "/../../configs/config.php";

class Router
{
    private array $routes = [];

    // ==========================================
    // REGISTRAR ROTAS GET
    // ==========================================

    public function get($path, $action)
    {
        $path = strtolower($path);

        $this->routes["GET"][$path] = $action;
    }


    // ==========================================
    // REGISTRAR ROTAS POST
    // ==========================================

    public function post($path, $action)
    {
        $path = strtolower($path);

        $this->routes["POST"][$path] = $action;
    }


    // ==========================================
    // LISTAR ROTAS GET
    // ==========================================

    public function listAllRoutesGET()
    {
        foreach ($this->routes["GET"] ?? [] as $path => $action) {

            echo "Rota: $path <br>";
            echo "Controller: " . $action[0] . "<br>";
            echo "Método: " . $action[1] . "<br><br>";

        }
    }


    // ==========================================
    // LISTAR ROTAS POST
    // ==========================================

    public function listAllRoutesPOST()
    {
        foreach ($this->routes["POST"] ?? [] as $path => $action) {

            echo "Rota: $path <br>";
            echo "Controller: " . $action[0] . "<br>";
            echo "Método: " . $action[1] . "<br><br>";

        }
    }


    // ==========================================
    // NORMALIZAR URL
    // ==========================================

    private function normalizeUri(): string
    {
        $uri = parse_url(
            $_SERVER["REQUEST_URI"],
            PHP_URL_PATH
        ) ?: "/";

        // Extrair caminho da BASE_URL
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);

        if ($basePath === false || $basePath === null) {
            $basePath = "";
        }

        $basePath = rtrim($basePath, "/");

        // Remover somente o prefixo da aplicação
        if (
            $basePath !== "" &&
            strpos($uri, $basePath) === 0
        ) {
            $uri = substr($uri, strlen($basePath));
        }

        if ($uri === "" || $uri === false) {
            $uri = "/";
        }

        // Normalizar barras e letras
        $uri = "/" . trim($uri, "/");

        if ($uri === "") {
            $uri = "/";
        }

        return strtolower($uri);
    }


    // ==========================================
    // RESPONDER ERRO JSON
    // ==========================================

    private function jsonError(
        string $message,
        int $status
    ): void {

        http_response_code($status);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    // ==========================================
    // EXECUTAR ROTA
    // ==========================================

    public function dispach()
    {
        $method = $_SERVER["REQUEST_METHOD"];

        $uri = $this->normalizeUri();

        $isApi = strpos($uri, "/api/") === 0 ||
                 $uri === "/api";

        // Verificar se o método HTTP existe
        if (!isset($this->routes[$method])) {

            if ($isApi) {
                $this->jsonError("Método HTTP não permitido.", 405);
            }

            http_response_code(405);
            return;
        }

        // Verificar se a rota existe
        if (!isset($this->routes[$method][$uri])) {

            if ($isApi) {
                $this->jsonError("Endpoint não encontrado.", 404);
            }

            http_response_code(404);

            $arquivo404 = __DIR__ . "/../views/erro404.html";

            if (file_exists($arquivo404)) {
                include $arquivo404;
            } else {
                echo "Página não encontrada.";
            }

            return;
        }

        // Recuperar controller e método
        $action = $this->routes[$method][$uri];

        $controllerClass = $action[0];
        $methodExec = $action[1];

        try {

            // Instanciar controller
            $controller = new $controllerClass();

            // Verificar se o método existe
            if (!method_exists($controller, $methodExec)) {

                throw new Exception(
                    "Método {$methodExec} não encontrado em {$controllerClass}."
                );

            }

            // Executar método
            $controller->$methodExec();

        } catch (Throwable $e) {

            error_log(
                "Erro no Router: " . $e->getMessage()
            );

            if ($isApi) {

                $this->jsonError(
                    "Erro interno ao processar a requisição.",
                    500
                );

            }

            http_response_code(500);

            echo "Erro interno do servidor.";

        }
    }
}
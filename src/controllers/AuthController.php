<?php
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../core/Database.php';


class AuthController {
    
    private $connectionInstance;
    private $pdo;

    public function __construct() {
        $this->connectionInstance = new Database();
        $this->pdo = $this->connectionInstance->getConnection();

    }
    private function iniciarSessao() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

}

    public function login() {

    $userDAO = new UserDAO($this->pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $this->iniciarSessao();
        $email = $_POST['email'] ?? '';
        $password = $_POST['senha'] ?? '';

        if ($userDAO->authenticate($email, $password) != null) {

            $user = $userDAO->findByEmail($email);

            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getName();
            $_SESSION['user_email'] = $user->getEmail();

            header('Location: /');
            exit();

        } else {

            $error = "Email ou senha inválidos.";

            $caminho = __DIR__ . "/../views/login.html";

            include $caminho;
            return;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $caminho = __DIR__ . "/../views/login.html";

        include $caminho;
    }
}
    
   public function signup() {

    $userDAO = new UserDAO($this->pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $this->iniciarSessao();

        $nome = trim($_POST['nome'] ?? '');
        $sobrenome = trim($_POST['sobrenome'] ?? '');
        $emailLocal = trim($_POST['email-cadastro'] ?? '');

        $email = $emailLocal . "@bccmail.com";

        $password = $_POST['senha-cadastro'] ?? '';
        $dataNascimento = $_POST['data-nascimento'] ?? '';
        $genero = $_POST['genero'] ?? '';

        // Verificar se o email já existe
        if ($userDAO->findByEmail($email) === null) {

            $nomeCompleto = $nome . ' ' . $sobrenome;

            // Criar objeto
            $user = new CommonUser(
                null,
                $nomeCompleto,
                $dataNascimento,
                $emailLocal,
                null,
                $email,
                $genero
            );

            // Aplicar hash na senha
            $user->setPassword($password);

            // Persistir no banco
            $userDAO->create($user);

            // Recuperar usuário criado
            $user = $userDAO->findByEmail($email);

            // Configurar sessão
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getName();
            $_SESSION['user_email'] = $user->getEmail();

            // Redirecionar
            header('Location: /');
            exit();

        } else {

            $error = "Email já cadastrado.";

            $caminho = __DIR__ . "/../views/cadastro.html";

            include $caminho;
            return;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $caminho = __DIR__ . "/../views/cadastro.html";

        include $caminho;
    }
}
    public function setup(){
        
         $caminho = __DIR__ . "/../../configs/setup_database.php";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;
    }
    
}

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

    public function login(){
        $userDAO = new UserDAO($this->pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            
            $email = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (!($userDAO->authenticate($email, $password)) != null) {
                // Autenticação bem-sucedida

                session_start();
                $_SESSION['user_email'] = $email;
                


                header('Location: /');
                exit();
            } else {
                // Autenticação falhou
                $error = "Email ou senha inválidos.";
                $caminho = __DIR__ . "/../views/login.html";
    
                if (!file_exists($caminho)) {
                    throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
                }
                include $caminho;

            }

            }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $caminho = __DIR__ . "/../views/login.html";
    
            if (!file_exists($caminho)) {
                throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
            }
            include $caminho;
        }
    }
    public function password(){
        $caminho = __DIR__ . "/../views/password.html";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;
    }
    
    public function signup(){
        
         $caminho = __DIR__ . "/../views/auth/signup.html";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;
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

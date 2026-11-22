<?php

class AuthController {
    
    public function login(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($username === 'admin' && $password === 'senha123') {
                echo "Login bem-sucedido!";
                header("Location: /index");
                exit();
            } else {
                echo "Credenciais inválidas. Tente novamente.";
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
    
}

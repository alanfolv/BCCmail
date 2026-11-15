<?php

class AuthController {
    public function init(){
        echo "Página inicial";
    }
    public function login(){
        echo "Página de login";
         /*$caminho = __DIR__ . "/../views/auth/login.php";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;*/
    }
    
   
    public function signup(){
        echo "Página de signup";

        /*
         $caminho = __DIR__ . "/../views/auth/signup.php";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;*/
    }
    
}

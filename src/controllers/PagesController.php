<?php

class PagesController {
    public function init(){
        $caminho = __DIR__ . "/../views/index.html";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;
    }
   
    
    
}

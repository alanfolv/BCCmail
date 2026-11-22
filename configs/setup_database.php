<?php

/*
    * Pelo amor de deus vamos usar isso aqui só como um seeder
    * inicial. De forma alguma deixar no código em produção, por que
    * qualquer um que tiver acesso a esse arquivo pode resetar o banco
    * de dados e dar uma confusão diabólica. Pro futuro, vamos usar 
    * migrations ou um sistema de seeders mais elaborado, que não
    * dependa de um arquivo PHP acessível publicamente tipo esse kkkkkkkk
    * mas enfim, é importante deixar esse pq o index.php depende da tabela adm
    * e eu mudo muito de computador, então é mais fácil ter esse arquivo
    * do que ficar recriando a tabela toda hora.

    * obrigado pela atencao

*/ 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/config.php';

echo "<h2>Verifique se a base já existe nos eu banco de dados: digitalSignage_reestruct</h2>";
echo "<h1>Iniciando Setup do Banco de Dados...</h1>";

try {

    $db = new Database();
    $pdo = $db->getConnection();

    echo "<p style='color:blue;'>Conexão com o banco de dados '" . DB_NAME . "' bem-sucedida.</p>";

    $sql = "
CREATE TABLE IF NOT EXISTS adm (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);

    echo "<p style='color:green;'>Tabelas  criadas com sucesso!</p>";

} catch (PDOException $e) {

    echo "<p style='color:red;'>Erro ao criar a tabela: " . $e->getMessage() . "</p>";
    echo "<p style='color:orange;'>Isso pode ser normal se a tabela 'adm' já existir no banco de dados.</p>";
}
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/config.php';

echo "<h2>Verificando banco de dados: " . DB_NAME . "</h2>";
echo "<h1>Iniciando Setup do Banco de Dados...</h1>";

try {

    $db = new Database();
    $pdo = $db->getConnection();

    echo "<p style='color:blue;'>Conexão estabelecida com sucesso.</p>";

    // TABELA DE ADMINISTRADORES
    $sql = "
    CREATE TABLE IF NOT EXISTS adm (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);


    // TABELA DE USUÁRIOS
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        birth_date DATE NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        cpf VARCHAR(14) UNIQUE,
        email VARCHAR(150) NOT NULL UNIQUE,
        adm_code VARCHAR(50)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);


    // TABELA DE CHATS
    $sql = "
    CREATE TABLE IF NOT EXISTS chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);


    // TABELA DE EMAILS
    $sql = "
    CREATE TABLE IF NOT EXISTS emails (
        id INT AUTO_INCREMENT PRIMARY KEY,

        from_id INT NOT NULL,
        to_id INT NOT NULL,
        chat_id INT NOT NULL,

        subject VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,

        date DATETIME DEFAULT CURRENT_TIMESTAMP,

        is_read BOOLEAN DEFAULT FALSE,

        CONSTRAINT fk_email_sender
            FOREIGN KEY (from_id)
            REFERENCES users(id)
            ON DELETE CASCADE,

        CONSTRAINT fk_email_receiver
            FOREIGN KEY (to_id)
            REFERENCES users(id)
            ON DELETE CASCADE,

        CONSTRAINT fk_email_chat
            FOREIGN KEY (chat_id)
            REFERENCES chats(id)
            ON DELETE CASCADE

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);


    echo "<p style='color:green;'>Tabelas criadas com sucesso!</p>";

} catch (PDOException $e) {

    
    echo "<p style='color:red;'>Erro ao criar as tabelas: "
        . htmlspecialchars($e->getMessage()) . "</p>";
}
<?php

require_once __DIR__ . '/../models/Chats.php';
require_once __DIR__ . '/EmailDAO.php';

class ChatDAO {

    private PDO $pdo;
    private EmailDAO $emailDAO;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->emailDAO = new EmailDAO($pdo);
    }

    // CRIAR CHAT
    public function create(Chats $chat) {

        $sql = "INSERT INTO chats (created_at)
                VALUES (:created_at)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':created_at' => $chat->getCreatedAt()
        ]);

        return $this->pdo->lastInsertId();
    }

    // BUSCAR CHAT POR ID
    public function findById($id) {

        $sql = "SELECT * FROM chats WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $chat = new Chats(
            $data['id'],
            $data['created_at']
        );

        // Recupera os emails pertencentes ao chat.
        $emails = $this->emailDAO->findByChat($id);

        foreach ($emails as $email) {
            $chat->addEmail($email);
        }

        return $chat;
    }


public function findByUser($userEmail) {

    $sql = "
        SELECT
            c.id,
            c.created_at,
            MAX(e.date) AS last_email_date

        FROM chats c

        INNER JOIN emails e
            ON e.chat_id = c.id

        WHERE e.from_email = :from_email
           OR e.to_email = :to_email

        GROUP BY c.id, c.created_at

        ORDER BY last_email_date DESC
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':from_email' => $userEmail,
        ':to_email' => $userEmail
    ]);

    $chats = [];

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $chat = new Chats(
            $data['id'],
            $data['created_at']
        );

        $chats[] = $chat;
    }

    return $chats;
}
    // EXCLUIR CHAT
    public function delete($id) {

        $sql = "DELETE FROM chats WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }
}
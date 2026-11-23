<?php

require_once __DIR__ . '/../models/Chat.php';
require_once __DIR__ . '/EmailDAO.php';

class ChatDAO {

    private PDO $pdo;
    private EmailDAO $emailDAO;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->emailDAO = new EmailDAO($pdo);
    }

    // CRIAR CHAT
    public function create(Chat $chat) {

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

        $chat = new Chat(
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

    // LISTAR CHATS DE UM USUÁRIO
    public function findByUser($userId) {

        $sql = "
            SELECT DISTINCT c.*
            FROM chats c
            INNER JOIN emails e ON e.chat_id = c.id
            WHERE e.from_id = :sender
               OR e.to_id = :receiver
            ORDER BY c.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':sender' => $userId,
            ':receiver' => $userId
        ]);

        $chats = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $chat = new Chat(
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
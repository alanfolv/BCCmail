<?php

require_once __DIR__ . '/../models/Email.php';
require_once __DIR__ . '/UserDAO.php';

class EmailDAO {

    private PDO $pdo;
    private UserDAO $userDAO;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->userDAO = new UserDAO($pdo);
    }

    // OBTER ID DO USUÁRIO
    private function getUserId($user) {

        if (is_object($user)) {
            return $user->getId();
        }

        return $user;
    }

    // ENVIAR EMAIL
    public function send(Email $email) {

        $sql = "INSERT INTO emails
                (from_id, to_id, chat_id, subject, body, date, is_read)
                VALUES
                (:from_id, :to_id, :chat_id, :subject, :body, :date, :is_read)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':from_id' => $this->getUserId($email->getFrom()),
            ':to_id' => $this->getUserId($email->getTo()),
            ':chat_id' => $email->getChatId(),
            ':subject' => $email->getSubject(),
            ':body' => $email->getBody(),
            ':date' => $email->getDate(),
            ':is_read' => $email->getIsRead()
        ]);

        return $this->pdo->lastInsertId();
    }

    // BUSCAR EMAIL POR ID
    public function findById($id) {

        $sql = "SELECT * FROM emails WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }

    // BUSCAR EMAILS DE UM CHAT
    public function findByChat($chatId) {

        $sql = "SELECT * FROM emails
                WHERE chat_id = :chat_id
                ORDER BY date ASC, id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':chat_id' => $chatId]);

        $emails = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $this->hydrate($data);
        }

        return $emails;
    }

    // CAIXA DE ENTRADA
    public function findInbox($userId) {

        $sql = "SELECT * FROM emails
                WHERE to_id = :user_id
                ORDER BY date DESC, id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        $emails = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $this->hydrate($data);
        }

        return $emails;
    }

    // EMAILS ENVIADOS
    public function findSent($userId) {

        $sql = "SELECT * FROM emails
                WHERE from_id = :user_id
                ORDER BY date DESC, id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        $emails = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $this->hydrate($data);
        }

        return $emails;
    }

    // MARCAR COMO LIDO
    public function markAsRead($id) {

        $sql = "UPDATE emails SET is_read = TRUE WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

    // MARCAR COMO NÃO LIDO
    public function markAsUnread($id) {

        $sql = "UPDATE emails SET is_read = FALSE WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

    // EXCLUIR EMAIL
    public function delete($id) {

        $sql = "DELETE FROM emails WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

    // CONVERTER REGISTRO EM OBJETO
    private function hydrate(array $data) {

        $from = $this->userDAO->findById($data['from_id']);
        $to = $this->userDAO->findById($data['to_id']);

        return new Email(
            $data['id'],
            $from,
            $to,
            $data['subject'],
            $data['body'],
            $data['date'],
            (bool) $data['is_read'],
            $data['chat_id']
        );
    }
}
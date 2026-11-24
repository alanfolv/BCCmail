<?php

require_once __DIR__ . '/../models/Email.php';

class EmailDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }


    // ==========================================
    // ENVIAR EMAIL
    // ==========================================

    public function send(Email $email) {

        $sql = "INSERT INTO emails
                (from_email, to_email, chat_id, subject, body, date, is_read)
                VALUES
                (:from_email, :to_email, :chat_id, :subject, :body, :date, :is_read)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':from_email' => $email->getFrom(),
            ':to_email' => $email->getTo(),
            ':chat_id' => $email->getChatId(),
            ':subject' => $email->getSubject(),
            ':body' => $email->getBody(),
            ':date' => $email->getDate(),
            ':is_read' => (int) $email->getIsRead()
        ]);

        $id = $this->pdo->lastInsertId();

        $email->setId($id);

        return $id;
    }


    // ==========================================
    // BUSCAR EMAIL POR ID
    // ==========================================

    public function findById($id) {

        $sql = "SELECT *
                FROM emails
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }


    // ==========================================
    // BUSCAR EMAILS DE UM CHAT
    // ==========================================

    public function findByChat($chatId) {

        $sql = "SELECT *
                FROM emails
                WHERE chat_id = :chat_id
                ORDER BY date ASC, id ASC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':chat_id' => $chatId
        ]);

        $emails = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $this->hydrate($data);
        }

        return $emails;
    }


    // ==========================================
    // CAIXA DE ENTRADA
    // ==========================================

    public function findInbox($userEmail) {

        $sql = "SELECT *
                FROM emails
                WHERE to_email = :user_email
                ORDER BY date DESC, id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':user_email' => $userEmail
        ]);

        $emails = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $this->hydrate($data);
        }

        return $emails;
    }


    // ==========================================
    // EMAILS ENVIADOS
    // ==========================================

    public function findSent($userEmail) {

        $sql = "SELECT *
                FROM emails
                WHERE from_email = :user_email
                ORDER BY date DESC, id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':user_email' => $userEmail
        ]);

        $emails = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $this->hydrate($data);
        }

        return $emails;
    }


    // ==========================================
    // MARCAR COMO LIDO
    // ==========================================

    public function markAsRead($id, $userEmail) {

        $sql = "UPDATE emails
                SET is_read = TRUE
                WHERE id = :id
                AND to_email = :user_email";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':user_email' => $userEmail
        ]);
    }


    // ==========================================
    // MARCAR COMO NÃO LIDO
    // ==========================================

    public function markAsUnread($id, $userEmail) {

        $sql = "UPDATE emails
                SET is_read = FALSE
                WHERE id = :id
                AND to_email = :user_email";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':user_email' => $userEmail
        ]);
    }


    // ==========================================
    // ADICIONAR AOS FAVORITOS
    // ==========================================

    public function addFavorite($userId, $userEmail, $emailId) {

        $sql = "INSERT IGNORE INTO email_favorites
                    (user_id, email_id)

                SELECT
                    :user_id,
                    e.id

                FROM emails e

                WHERE e.id = :email_id
                AND (
                    e.from_email = :from_email
                    OR e.to_email = :to_email
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId,
            ':email_id' => $emailId,
            ':from_email' => $userEmail,
            ':to_email' => $userEmail
        ]);

        return $stmt->rowCount() > 0;
    }


    // ==========================================
    // REMOVER DOS FAVORITOS
    // ==========================================

    public function removeFavorite($userId, $emailId) {

        $sql = "DELETE FROM email_favorites
                WHERE user_id = :user_id
                AND email_id = :email_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':user_id' => $userId,
            ':email_id' => $emailId
        ]);
    }


    // ==========================================
    // VERIFICAR SE É FAVORITO
    // ==========================================

    public function isFavorite($userId, $emailId) {

        $sql = "SELECT 1
                FROM email_favorites
                WHERE user_id = :user_id
                AND email_id = :email_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId,
            ':email_id' => $emailId
        ]);

        return $stmt->fetchColumn() !== false;
    }


    // ==========================================
    // LISTAR FAVORITOS
    // ==========================================

    public function findFavorites($userId) {

        $sql = "SELECT e.*
                FROM emails e

                INNER JOIN email_favorites f
                    ON f.email_id = e.id

                WHERE f.user_id = :user_id

                ORDER BY e.date DESC, e.id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId
        ]);

        $emails = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $this->hydrate($data);
        }

        return $emails;
    }


    // ==========================================
    // EXCLUIR EMAIL
    // ==========================================

    public function delete($id, $userEmail) {

        $sql = "DELETE FROM emails
                WHERE id = :id
                AND (
                    from_email = :from_email
                    OR to_email = :to_email
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':from_email' => $userEmail,
            ':to_email' => $userEmail
        ]);
    }


    // ==========================================
    // CONVERTER REGISTRO EM OBJETO
    // ==========================================

    private function hydrate(array $data) {

        return new Email(
            $data['id'],
            $data['from_email'],
            $data['to_email'],
            $data['subject'],
            $data['body'],
            $data['date'],
            (bool) $data['is_read'],
            $data['chat_id']
        );
    }

}
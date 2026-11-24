<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/CommonUser.php';

class UserDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }


    // ==========================================
    // CADASTRAR USUÁRIO
    // ==========================================

    public function create(CommonUser $user) {

        $sql = "INSERT INTO users
                (
                    name,
                    birth_date,
                    username,
                    password,
                    email,
                    genero
                )
                VALUES
                (
                    :name,
                    :birth_date,
                    :username,
                    :password,
                    :email,
                    :genero
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':name' => $user->getName(),
            ':birth_date' => $user->getBirthDate(),
            ':username' => $user->getUsername(),
            ':password' => $user->getPassword(),
            ':email' => $user->getEmail(),
            ':genero' => $user->getGenero()
        ]);

        return $this->pdo->lastInsertId();
    }


    // ==========================================
    // BUSCAR USUÁRIO POR ID
    // ==========================================

    public function findById($id) {

        $sql = "SELECT *
                FROM users
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }


    // ==========================================
    // BUSCAR USUÁRIO POR EMAIL
    // ==========================================

    public function findByEmail($userEmail) {

        $sql = "SELECT *
                FROM users
                WHERE email = :email";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':email' => trim($userEmail)
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }


    // ==========================================
    // BUSCAR USUÁRIO POR USERNAME
    // ==========================================

    public function findByUsername($username) {

        $sql = "SELECT *
                FROM users
                WHERE username = :username";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':username' => $username
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }


    // ==========================================
    // LISTAR TODOS OS USUÁRIOS
    // ==========================================

    public function findAll() {

        $sql = "SELECT *
                FROM users
                ORDER BY name ASC";

        $stmt = $this->pdo->query($sql);

        $users = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $users[] = $this->hydrate($data);

        }

        return $users;
    }


    // ==========================================
    // ATUALIZAR USUÁRIO
    // ==========================================

    public function update(CommonUser $user) {

        $sql = "UPDATE users SET
                    name = :name,
                    birth_date = :birth_date,
                    username = :username,
                    email = :email,
                    genero = :genero

                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $user->getName(),
            ':birth_date' => $user->getBirthDate(),
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':genero' => $user->getGenero(),
            ':id' => $user->getId()
        ]);
    }


    // ==========================================
    // ATUALIZAR SENHA
    // ==========================================

    public function updatePassword($id, $newPassword) {

        $hash = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );

        $sql = "UPDATE users
                SET password = :password
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':password' => $hash,
            ':id' => $id
        ]);
    }


    // ==========================================
    // AUTENTICAR USUÁRIO
    // ==========================================

    public function authenticate($email, $password) {

        $user = $this->findByEmail($email);

        if ($user === null) {
            return null;
        }

        if (!password_verify($password, $user->getPassword())) {
            return null;
        }

        return $user;
    }


    // ==========================================
    // EXCLUIR USUÁRIO
    // ==========================================

    public function delete($id) {

        $sql = "DELETE FROM users
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }


    // ==========================================
    // CONVERTER REGISTRO EM OBJETO (HYDRATE)
    // ==========================================

    private function hydrate(array $data) {

        return new CommonUser(
            $data['id'],
            $data['name'],
            $data['birth_date'],
            $data['username'],
            $data['password'],
            $data['email'],
            $data['genero'] ?? null
        );
    }

}
<?php
class CommonUser extends User {
    
    // Propriedades para guardar os dados. Todas privadas.
    private $id;
    private $name;
    private $birthDate;
    private $username;
    private $password; // E AQ GUARDA O HASH
    private $genero;
    private $email;
    

    public function __construct($id = null,$name=null,$birthDate=null,$username=null,$password=null,$email=null,$genero = null){
            $this->id = $id;
            $this->name =$name;
            $this->birthDate = $birthDate;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
            $this->genero = $genero;
        }

    // MUDANÇA 3: Getters e Setters para cada propriedade
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
    }

    public function getBirthDate() {
        return $this->birthDate;
    }
    public function setBirthDate($birthDate) {
        $this->birthDate = $birthDate;
    }

    public function getUsername() {
        return $this->username;
    }
    public function setUsername($username) {
        $this->username = $username;
    }
    
    public function getPassword() {
        return $this->password;
    }
    // O setter da senha já pode aplicar o hash, ou podemos deixar para um método separado.
    public function setPassword($plainPassword) {
        // A senha em texto puro é recebida e o hash é armazenado na propriedade.
        $this->password = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function getGenero() {
        return $this->genero;
    }
    public function setGenero($genero) {
        $this->genero = $genero;
    }

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
}
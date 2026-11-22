<?php
    abstract class User{
        private $id;
        private $name;
        private $birthDate;
        private $username;
        private $password;
        private $email;


        public function __construct($id = null,$name,$birthDate,$username,$password,$email){
            $this->id = $id;
            $this->name =$name;
            $this->birthDate = $birthDate;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
        }
        public function  getId(){
           return $this->id;
        }
        public function getName(){
            return $this->name;
        }

        public function  getBirthDate(){
            return $this->birthDate;
        }

        public function  getPassword(){
            return $this->password;
        }
        public function  getUsername(){
            return $this->username;
        }
        public function  getEmail(){
            return $this->email;
        }
        public function setId($id){
            $this->id = $id;
        }
        public function setName($name){
            $this->name = $name;
        }
        public function setBirthDate($birthDate){
            $this->birthDate = $birthDate;
        }
        public function setUsername($username){
            $this->username = $username;
        }
        public function setPassword($password){
            $this->password = $password;
        }

        public function setEmail($email){
            $this->email = $email;
        }
        public  function toString(){
            return "ID:{$this->id}<br>Name:{$this->name}<br>Birth Date:{$this->birthDate}<br>Username:{$this->username}<br>Pasword:{$this->password}<br>Email:{$this->email}";
        }
}
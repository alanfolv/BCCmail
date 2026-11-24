<?php

class Chats{
    private $id;
    private $emails;
    private $createdAt;

    public function __construct($id = null, $createdAt = null) {
        $this->id = $id;
        $this->emails = [];
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }

    public function getId() {
        return $this->id;
    }

    public function getEmails() {
        return $this->emails;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function addEmail(Email $email) {
        $email->setChatId($this->id);
        $this->emails[] = $email;
    }

    public function removeEmail($emailId) {
        foreach ($this->emails as $index => $email) {
            if ($email->getId() == $emailId) {
                unset($this->emails[$index]);
                $this->emails = array_values($this->emails);
                return true;
            }
        }

        return false;
    }

    public function getEmailCount() {
        return count($this->emails);
    }
}
<?php

class Email {
    private $id;
    private $from;
    private $to;
    private $subject;
    private $body;
    private $date;
    private $isRead;
    private $chatId;
    private $isFavorite;

public function __construct(
    $id,
    $from,
    $to,
    $subject,
    $body,
    $date,
    $isRead = false,
    $chatId = null,
    $isFavorite = false
) {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->date = $date ?? date('Y-m-d H:i:s');
        $this->isRead = $isRead;
        $this->chatId = $chatId;
        $this->isFavorite = $isFavorite;
    }

    public function getId() {
        return $this->id;
    }

    public function getFrom() {
        return $this->from;
    }

    public function getTo() {
        return $this->to;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getBody() {
        return $this->body;
    }

    public function getDate() {
        return $this->date;
    }

    public function getIsRead() {
        return $this->isRead;
    }

    public function getChatId() {
        return $this->chatId;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setIsRead($isRead) {
        $this->isRead = $isRead;
    }

    public function setChatId($chatId) {
        $this->chatId = $chatId;
    }
}
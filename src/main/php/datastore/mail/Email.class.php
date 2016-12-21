<?php

class Email {
    
    private $from;
    private $to;
    private $subject;
    private $text;
    private $html;
    
    public function getFrom() {
        return $this->from;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function getTo() {
        return $this->to;
    }

    public function setTo($to) {
        $this->to = $to;
    }
    
    public function getSubject() {
        return $this->subject;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function setText($text) {
        $this->text = $text;
    }

    public function getHtml() {
        return $this->html;
    }
    
    public function setHtml($html) {
        $this->html = $html;
    }
    
}
<?php

require_once '../../vendor/autoload.php';
use Mailgun\Mailgun;

/**
 *  Source: https://github.com/mailgun/mailgun-php
 */
class Mailer {
    private $mailgun;
    private $domain;
    
    // First, instantiate the SDK with your API credentials and define your domain. 
    public function __construct() {
        $this->mailgun = new Mailgun(MAILGUN_API_KEY);
        $this->domain = MAINGUN_API_DOMAIN;
    }
    
    // compose and send your message.
    public function send($email) {
        // Build options
        
        $options = array(
            'from'    => $email->getFrom(), 
            'to'      => $email->getTo(),
            'subject' => $email->getSubject()
            );
        if (null !== $email->getText()) {
            $options['text'] = $email->getText();
        }

        if (null !== $email->getHtml()) {
            $options['html'] = $email->getHtml();
        }
        
        $this->mailgun->sendMessage($this->domain, $options);
    }
}
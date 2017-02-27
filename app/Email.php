<?php

require_once 'vendor/autoload.php';
require_once 'parameters.php';


/**
 * Created by PhpStorm.
 * User: Faizan
 * Date: 17-02-2017
 * Time: 01:17 AM
 */
class Email extends Swift_SmtpTransport
{
    public function __construct($host = 'smtp.zoho.com', $port = 25, $security = null)
    {
        parent::__construct($host, $port, $security);
    }

    public function emailAction($data, $subject)
    {
        global $configuration;
        $transport = self::newInstance('smtp.zoho.com',465,'ssl')
            ->setUsername($configuration['email']['username'])
            ->setPassword($configuration['email']['password']);

        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance("Daily Report - ".$subject)
            ->setFrom($configuration['email']['from'])
            ->setTo($configuration['email']['to'])
            ->setCc($configuration['email']['cc'])
            ->setBcc($configuration['email']['bcc']);

        $cid = $message->embed(Swift_Image::newInstance($data, 'report.png', 'image/png'));

        $message->setBody(
            '<html>' .
            ' <head></head>' .
            ' <body>' .
            '<img src="' . $cid . '" alt="Image" />' .
            ' </body>' .
            '</html>',
            'text/html' // Mark the content-type as HTML
        );

        $result = $mailer->send($message);
        if ($result) echo "Email sent successfully";        
    }

}
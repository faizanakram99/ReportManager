<?php

require_once 'vendor/autoload.php';


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
        $transport = self::newInstance('smtp.zoho.com',465,'ssl')
            ->setUsername('your_username')
            ->setPassword('yourpassword');

        $mailer = Swift_Mailer::newInstance($transport);
       // $attachment = Swift_Attachment::newInstance($data,'DailyReport.png','image/png')->setDisposition('inline');

        $message = Swift_Message::newInstance("Daily Report".$subject)
            ->setFrom(['dummy@dummy.com' => 'dummy@dummy.com'])
            ->setTo(['dummy@dummy.com' => 'dummy@dummy.com']);

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
    }

}
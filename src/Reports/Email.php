<?php

namespace Reports;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

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
            ->setUsername(Parameters::USERNAME)
            ->setPassword(Parameters::PASSWORD);

        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance("Daily Report - ".$subject)
            ->setFrom(Parameters::USERNAME)
            ->setTo(Parameters::TO)
            ->setCc(Parameters::CC)
            ->setBcc(Parameters::BCC);

        $message->setBody($data, 'text/html');

        $result = $mailer->send($message);
        if ($result) echo "Email sent successfully";        
    }

}
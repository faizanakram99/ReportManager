<?php
/**
 * Created by PhpStorm.
 * User: faizan
 * Date: 13/1/18
 * Time: 6:45 PM.
 */

namespace Api;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Entity\Reportdetail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Report.
 */
class Report
{
    /** @var EntityManager $em */
    private $em;

    /**
     * Report constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $date = \DateTime::createFromFormat('Y-m-d', str_replace('/', '', $request->getPathInfo()));
        /** @var \Entity\Report $report */
        $report = $this->em->getRepository('Entity\Report')->findOneBy(['date' => $date]);

        return new JsonResponse($report ? $report->getAll() : ['reportdetails' => [new class() {}]]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        $report = json_decode($request->getContent(), true);
        $date = \DateTime::createFromFormat('Y-m-d', str_replace('/', '', $request->getPathInfo()));

        $reportEntity = $this->em->getRepository('Entity\Report')->findOneBy(['date' => $date]) ?: new \Entity\Report($date);

        $reportEntity
            ->setLogin(new \DateTime($report['login']))
            ->setLogout(new \DateTime($report['logout']))
            ->removeReportdetails();
        $this->em->persist($reportEntity);

        foreach ($report['reportdetails'] as $reportdetail) {
            $reportdetailEntity = new Reportdetail();

            $reportdetailEntity
                ->setTickets($reportdetail['tickets'])
                ->setLoggedTime($reportdetail['spent_time'])
                ->setSpentTime($reportdetail['logged_time'])
                ->setRemarks($reportdetail['remarks']);

            $reportEntity->addReportdetail($reportdetailEntity);
            $this->em->persist($reportdetailEntity);
        }

        try {
            @$this->em->flush();
        } catch (ORMException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Saved successfully!']);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $date = \DateTime::createFromFormat('Y-m-d', str_replace('/', '', $request->getPathInfo()));

        /** @var \Entity\Report $report */
        $report = $this->em->getRepository('Entity\Report')->findOneBy(['date' => $date]);
        $this->em->remove($report);
        try {
            @$this->em->flush();
        } catch (ORMException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Deleted successfully!']);
    }

    /**
     * @param $request
     *
     * @return JsonResponse
     */
    public function email(Request $request): JsonResponse
    {
        $date = \DateTime::createFromFormat('Y-m-d', str_replace('/', '', $request->getPathInfo()));
        $content = $this->em->getRepository('Entity\Report')->findOneBy(['date' => $date])->getAll();

        $parameters = Yaml::parseFile(__DIR__.'/../../config/email.yaml');

        $transport = Swift_SmtpTransport::newInstance($parameters['host'], $parameters['port'], $parameters['security'])
            ->setUsername($parameters['username'])
            ->setPassword($parameters['password']);

        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance('Daily Report - '.$date->format('M d Y'))
            ->setFrom($parameters['username'])
            ->setTo($parameters['sendto']);

        if (isset($parameters['cc'])) {
            $message->setCc($parameters['cc']);
        }

        if (isset($parameters['bcc'])) {
            $message->setBcc($parameters['bcc']);
        }

        $heading = "<strong>Work hours : {$content['login']} - {$content['logout']}</strong>";

        $thead = '<thead style="background: #3c3c3c; color: #fff;">
                    <th style="padding: 6px; width: 25px;">#</th>
                    <th style="padding: 6px">Tickets/Tasks</th>
                    <th style="padding: 6px; width: 70px;">Time spent</th>
                    <th style="padding: 6px; width: 70px;">Time logged</th>
                    <th style="padding: 6px">Remarks</th>
                  </thead>';

        $tr = '';
        foreach ($content['reportdetails'] as $index => $reportdetail) {
            $row = $index + 1;
            $tr .= "<tr>
                        <td style='padding: 5px'>{$row}</td>
                        <td style='padding: 5px'>
                            <div style='word-wrap: break-word'>{$reportdetail['tickets']}</div>
                        </td>
                        <td style='padding: 5px'>{$reportdetail['spent_time']}</td>
                        <td style='padding: 5px'>{$reportdetail['logged_time']}</td>
                        <td style='padding: 5px'>
                            <div style='word-wrap: break-word'>{$reportdetail['remarks']}</div>
                        </td>
                     </tr>";
        }
        $tbody = "<tbody>{$tr}</tbody>";
        $table = "<table border='1' style='width: 600px; table-layout: fixed'>{$thead}{$tbody}</table>";
        $body = "<div>{$heading}{$table}</div>";

        $message->setBody($body, 'text/html');
        $result = $mailer->send($message);
        if (!$result) {
            return new JsonResponse(['error' => 'Mail failed!'], 500);
        }

        return new JsonResponse(['message' => 'Mail sent successfully!']);
    }
}

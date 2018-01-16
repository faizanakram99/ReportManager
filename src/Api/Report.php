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

    /** @var \Twig_Environment $twig */
    private $twig;

    /**
     * Report constructor.
     *
     * @param EntityManager $em
     * @param \Twig_Environment $twig
     */
    public function __construct(EntityManager $em, \Twig_Environment $twig)
    {
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $report = $this->getReport($this->getDate($request));

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
        $date = $this->getDate($request);

        $reportEntity = $this->getReport($date) ?: new \Entity\Report($date);

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
        $report = $this->getReport($this->getDate($request));
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
        $date = $this->getDate($request);
        $content = $this->getReport($date)->getAll();

        $parameters = Yaml::parseFile(__DIR__.'/../../config/email.yaml');

        $transport = (new Swift_SmtpTransport($parameters['host'], $parameters['port'], $parameters['security']))
            ->setUsername($parameters['username'])
            ->setPassword($parameters['password']);

        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message('Daily Report - '.$date->format('M d Y')))
            ->setFrom($parameters['username'])
            ->setTo($parameters['sendto']);

        if (isset($parameters['cc'])) {
            $message->setCc($parameters['cc']);
        }

        if (isset($parameters['bcc'])) {
            $message->setBcc($parameters['bcc']);
        }

        $message->setBody($this->twig->render('email.html.twig', ['content' => $content]), 'text/html');
        $result = $mailer->send($message);
        if (!$result) {
            return new JsonResponse(['error' => 'Mail failed!'], 500);
        }

        return new JsonResponse(['message' => 'Mail sent successfully!']);
    }

    /**
     * @param \DateTime $date
     * @return \Entity\Report|null|object
     */
    private function getReport(\DateTime $date)
    {
        return $this->em->getRepository('Entity\Report')->findOneBy(['date' => $date]);
    }

    /**
     * @param Request $request
     * @return bool|\DateTime
     */
    private function getDate(Request $request)
    {
        return \DateTime::createFromFormat('Y-m-d', str_replace('/', '', $request->getPathInfo()));
    }
}

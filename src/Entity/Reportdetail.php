<?php
/**
 * Created by PhpStorm.
 * User: faizan
 * Date: 13/1/18
 * Time: 5:26 PM.
 */

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Reportdetail.
 *
 * @ORM\Entity
 * @ORM\Table(name="reportdetail")
 */
class Reportdetail
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Report", inversedBy="reportdetails")
     * @ORM\JoinColumn
     */
    protected $report;

    /**
     * @ORM\Column(type="string")
     */
    protected $tickets;

    /**
     * @ORM\Column(type="string")
     */
    protected $spent_time;

    /**
     * @ORM\Column(type="string")
     */
    protected $logged_time;

    /**
     * @ORM\Column(type="text")
     */
    protected $remarks;

    /**
     * @param mixed $report
     *
     * @return Reportdetail
     */
    public function setReport(Report $report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * @param string $tickets
     *
     * @return Reportdetail
     */
    public function setTickets(string $tickets)
    {
        $this->tickets = $tickets;

        return $this;
    }

    /**
     * @param string $spent_time
     *
     * @return Reportdetail
     */
    public function setSpentTime(string $spent_time)
    {
        $this->spent_time = $spent_time;

        return $this;
    }

    /**
     * @param string $logged_time
     *
     * @return Reportdetail
     */
    public function setLoggedTime(string $logged_time)
    {
        $this->logged_time = $logged_time;

        return $this;
    }

    /**
     * @param string $remarks
     *
     * @return Reportdetail
     */
    public function setRemarks(string $remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    public function getAll()
    {
        return [
            'id' => $this->id,
            'tickets' => $this->tickets,
            'spent_time' => $this->spent_time,
            'logged_time' => $this->logged_time,
            'remarks' => $this->remarks,
        ];
    }
}

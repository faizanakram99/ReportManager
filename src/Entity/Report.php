<?php
/**
 * Created by PhpStorm.
 * User: faizan
 * Date: 13/1/18
 * Time: 4:03 PM.
 */

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Report.
 *
 * @ORM\Entity
 * @ORM\Table(name="report", uniqueConstraints={@ORM\UniqueConstraint(name="date_idx", columns={"date"})})})
 */
class Report
{
    /**
     * Report constructor.
     *
     * @param \DateTime $date
     */
    public function __construct(\DateTime $date)
    {
        $this->date = $date;
        $this->reportdetails = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="time")
     */
    protected $login;

    /**
     * @ORM\Column(type="time")
     */
    protected $logout;

    /**
     * @ORM\OneToMany(targetEntity="Reportdetail", mappedBy="report", cascade={"persist","remove"}, orphanRemoval=true)
     */
    protected $reportdetails;

    /**
     * @param mixed $login
     *
     * @return Report
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @param mixed $logout
     *
     * @return Report
     */
    public function setLogout($logout)
    {
        $this->logout = $logout;

        return $this;
    }

    /**
     * @param Reportdetail $reportdetail
     *
     * @return Report
     */
    public function addReportdetail(Reportdetail $reportdetail)
    {
        $reportdetail->setReport($this);
        $this->reportdetails->add($reportdetail);

        return $this;
    }

    /**
     * @return Report
     */
    public function removeReportdetails()
    {
        $this->reportdetails->clear();

        return $this;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'login' => $this->login->format('H:i:s'),
            'logout' => $this->logout->format('H:i:s'),
            'reportdetails' => $this->reportdetails->map(function ($reportdetail) { return $reportdetail->getAll(); })->toArray() ?: [new class() {}],
        ];
    }
}

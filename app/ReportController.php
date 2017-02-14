<?php
require_once "ReportEntity.php";

class ReportController
{
    protected $reportEntity;
    protected $data;
    
    public function __construct($data = null){
        if ($data){
            $this->data = json_decode($data);
        }        
    }

    public function editAction($date = null){
        $reportEntity = new ReportEntity($date);
        if ($reportEntity->report){
            $report['date'] = $reportEntity->report['date'];
            $report['login'] = $reportEntity->report['login'];
            $report['logout'] = $reportEntity->report['logout'];

            $ids = explode(",", $reportEntity->report['ids']);
            $tickets = explode(",", $reportEntity->report['tickets']);
            $spent_time = explode(",", $reportEntity->report['spent_time']);
            $logged_time = explode(",", $reportEntity->report['logged_time']);
            $remarks = explode(",", $reportEntity->report['remarks']);
            
            $len = count($tickets);

            for($i = 0; $i < $len ; $i++){
                $report['reportdetails'][$i]['ids'] = $ids[$i];
                $report['reportdetails'][$i]['tickets'] = $tickets[$i];
                $report['reportdetails'][$i]['spent_time'] = $spent_time[$i];
                $report['reportdetails'][$i]['logged_time'] = $logged_time[$i];
                $report['reportdetails'][$i]['remarks'] = $remarks[$i];
            }

            header("Content-type: application/json");
            echo(json_encode($report));
           
        }
    }


    public function updateAction($date = null){
        $reportEntity = new ReportEntity($date);
        $reportEntity->setReport($this->data);
    }


    public function emailAction(){
        
    }
}

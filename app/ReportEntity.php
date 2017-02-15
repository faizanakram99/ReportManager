<?php

class ReportEntity
{
    const SERVER = 'localhost';
    const USERNAME = 'root';
    const PASSWORD = '';
    const DATABASE = 'reportmanager';

    protected $conn;
    public $report;

    /**
     * DatabaseManager constructor.
     */
    public function __construct($date = null)
    {
        $this->conn = new mysqli(self::SERVER, self::USERNAME, self::PASSWORD,self::DATABASE);
        if ($this->conn->connect_error) {
            die('Connect Error ('.$this->conn->connect_errno.') '.$this->conn->connect_error);
        }
        $result = $this->conn->query("SELECT report.*,
                                (SELECT GROUP_CONCAT(id SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as reportdetail_id, 
                                (SELECT GROUP_CONCAT(tickets SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as tickets,
                                (SELECT GROUP_CONCAT(spent_time SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as spent_time,
                                (SELECT GROUP_CONCAT(logged_time SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as logged_time,
                                (SELECT GROUP_CONCAT(remarks SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as remarks
                                FROM report WHERE report.date ='".$date."'");
        if($result->num_rows) $this->report = $result->fetch_assoc();
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->report)) {
            return $this->report[$name];
        }
        elseif ($name == "reportdetails"){
            $result = $this->conn->query("SELECT * FROM reportdetails WHERE report_id ='".$this->id."'");
            while($row = $result->fetch_assoc()) $rows[] = $row;
            return ($rows);
        }
        else{
            throw new Exception("Unknown property called!". $name);
        }
    }

    public function setReport($data){
        if (!$this->report){
            $this->conn->query("INSERT INTO report (date, login, logout)
                                VALUES('$data->date', '$data->login', '$data->logout')");
            $reportid = $this->conn->insert_id;

            foreach($data->reportdetails as $reportdetail){
                $this->conn->query("INSERT INTO reportdetails (report_id, tickets, spent_time, logged_time, remarks)
                                    VALUES('$reportid', '$reportdetail->tickets', '$reportdetail->spent_time', 
                                    '$reportdetail->logged_time', '$reportdetail->remarks')");
            }
            echo "Saved successfully";
        }
        else{
            $this->conn->query("UPDATE report SET login = '$data->login', logout = '$data->logout'
                                WHERE date = '$data->date'");

            foreach($data->reportdetails as $reportdetail){
                if($reportdetail->reportdetail_id != null || $reportdetail->reportdetail_id != ''){
                    $this->conn->query("UPDATE reportdetails 
                                    SET tickets = '$reportdetail->tickets', 
                                    spent_time = '$reportdetail->spent_time',
                                    logged_time = '$reportdetail->logged_time',
                                    remarks = '$reportdetail->remarks'
                                    WHERE id = '$reportdetail->reportdetail_id'");
                }
                else{
                    $this->conn->query("INSERT INTO reportdetails (report_id, tickets, spent_time, logged_time, remarks)
                                    VALUES('$this->id', '$reportdetail->tickets', '$reportdetail->spent_time', 
                                    '$reportdetail->logged_time', '$reportdetail->remarks')");
                }                
            }
            echo "Updated successfully";
        }
    }

    public function deleteReport($date, $reportdetail_id = null){
        if($this->report){
            if($reportdetail_id) {
                $this->conn->query("DELETE FROM reportdetails where id ='".$reportdetail_id."'");
                echo "Deleted report line successfully!";
            }
            else{
                $this->conn->query("DELETE FROM report where date ='".$date."'");
                echo "Deleted report successfully!";
            }
        }
    }
}
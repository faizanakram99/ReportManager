<?php

namespace Reports;
use mysqli;

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
        if ($this->conn->connect_error) die('Connect Error ('.$this->conn->connect_errno.') '.$this->conn->connect_error);

        if(!($stmt = $this->conn->prepare("SELECT report.*,
                                (SELECT GROUP_CONCAT(id SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as reportdetail_id, 
                                (SELECT GROUP_CONCAT(tickets SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as tickets,
                                (SELECT GROUP_CONCAT(spent_time SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as spent_time,
                                (SELECT GROUP_CONCAT(logged_time SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as logged_time,
                                (SELECT GROUP_CONCAT(remarks SEPARATOR '|#|') FROM reportdetails where report_id = report.id) as remarks
                                FROM report WHERE report.date = ?"))){
            echo "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
        }
        if(!$stmt->bind_param('s',$date)) echo "Binding parameters failed: (".$stmt->errno.") ". $stmt->error;
        if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        $result = $stmt->get_result();
        if($result->num_rows) $this->report = $result->fetch_assoc();
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->report)) {
            return $this->report[$name];
        }
        elseif ($name == "reportdetails"){
            if(!($stmt = $this->conn->prepare("SELECT * FROM reportdetails WHERE report_id = ?"))){
                echo "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            }
            if(!$stmt->bind_param('i',$this->id)) echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
            if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) $rows[] = $row;
            return ($rows);
        }
        else{
            throw new Exception("Unknown property called!". $name);
        }
    }

    public function setReport($data){
        if (!$this->report){
            if(!($stmt = $this->conn->prepare("INSERT INTO report (date, login, logout)
                                VALUES(?,?,?)"))){
                echo "Prepare failed: (".$this->conn->errno.") ".$this->conn->error;
            }
            if(!$stmt->bind_param('sss',$data->date, $data->login, $data->logout)){
                echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
            }
            if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $reportid = $stmt->insert_id;
            $stmt->close();

            foreach($data->reportdetails as $reportdetail){
                if(!($stmt = $this->conn->prepare("INSERT INTO reportdetails (report_id, tickets, spent_time, logged_time, remarks)
                                    VALUES(?,?,?,?,?)"))){
                    echo "Prepare failed: (".$this->conn->errno.") ".$this->conn->error;
                }
                if(!$stmt->bind_param('issss',$reportid,$reportdetail->tickets,
                                              $reportdetail->spent_time,$reportdetail->logged_time, $reportdetail->remarks)){
                    echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
                }
                if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }
            echo "Saved successfully!";
        }
        else{
            if(!($stmt = $this->conn->prepare("UPDATE report SET login = ? , logout = ? WHERE date = ? "))){
                echo "Prepare failed: (".$this->conn->errno.") ".$this->conn->error;
            }
            if(!$stmt->bind_param('sss', $data->login, $data->logout, $data->date)){
                echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
            }
            if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $stmt->close();

            foreach($data->reportdetails as $reportdetail){
                if($reportdetail->reportdetail_id != null || $reportdetail->reportdetail_id != ''){
                    if(!($stmt = $this->conn->prepare("UPDATE reportdetails SET tickets = ?, 
                                                       spent_time = ?, logged_time = ?, remarks = ? WHERE id = ?"))){
                        echo "Prepare failed: (".$this->conn->errno.") ".$this->conn->error;
                    }
                    if(!$stmt->bind_param('ssssi',$reportdetail->tickets, $reportdetail->spent_time,
                                                  $reportdetail->logged_time, $reportdetail->remarks, $reportdetail->reportdetail_id)){
                        echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
                    }
                    if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                    $stmt->close();
                }
                else{
                    if(!($stmt = $this->conn->prepare("INSERT INTO reportdetails (report_id, tickets, spent_time, logged_time, remarks)
                                                  VALUES(?,?,?,?,?)"))){
                        echo "Prepare failed: (".$this->conn->errno.") ".$this->conn->error;
                    }
                    if(!$stmt->bind_param('issss',$this->id,$reportdetail->tickets, $reportdetail->spent_time,
                                                  $reportdetail->logged_time, $reportdetail->remarks)){
                        echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
                    }
                    if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                    $stmt->close();
                }                
            }
            echo "Updated successfully!";
        }
    }

    public function deleteReport($date, $reportdetail_id = null){
        if($this->report){
            if($reportdetail_id) {
                if(!($stmt = $this->conn->prepare("DELETE FROM reportdetails where id = ?"))){
                    echo "Prepare failed: (".$this->conn->errno.") ".$this->conn->error;
                }
                if(!$stmt->bind_param('i',$reportdetail_id )) echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
                if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $stmt->close();
                echo "Deleted successfully!";
            }
            else{
                if(!($stmt = $this->conn->prepare("DELETE FROM report where date = ?"))) {
                    echo "Prepare failed: (".$this->conn->errno.") ".$this->conn->error;
                }
                if(!$stmt->bind_param('s',$date )) echo "Binding parameters failed: (".$stmt->errno.") ".$stmt->error;
                if(!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $stmt->close();
                echo "Deleted successfully!";
            }
        }
    }
}
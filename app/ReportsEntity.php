<?php

/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 8/2/17
 * Time: 9:56 PM
 */
class ReportsEntity
{
    public $report;


    /**
     * DatabaseManager constructor.
     */
    public function __construct($id = null)
    {
        $conn = new mysqli('localhost', 'root', '','reportmanager');
        if ($conn->connect_error) {
            die('Connect Error (' . $conn->connect_errno . ') '
                . $conn->connect_error);
        }
        $result = $conn->query("select * from reports where id =".$id);
        $this->report = $result->fetch_assoc();
    }

    function __get($name)
    {
        if($this->report[$name]) return $this->report[$name];
    }
}
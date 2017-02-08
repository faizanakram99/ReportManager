<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 8/2/17
 * Time: 11:15 PM
 */

include ('app/ReportsEntity.php');

$db = new ReportsEntity(1);

var_dump($db);

$db->ticket_no = 33;



<?php

require_once "ReportController.php";

$data = file_get_contents("php://input");
$reportController = new ReportController($data);

switch ($_GET['action']){
    case 'edit':
    $reportController->editAction($_GET['date']);
    break;

    case 'save':
    $reportController->updateAction($_GET['date']);
    break;

    case 'email':
    $reportController->emailAction();
    break;
}

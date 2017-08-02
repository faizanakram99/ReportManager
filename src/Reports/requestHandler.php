<?php

require_once "../../vendor/autoload.php";

$data = file_get_contents("php://input");
$reportController = new Reports\ReportController($data);

switch ($_GET['action']){
    case 'edit':
    $reportController->editAction($_GET['date']);
    break;

    case 'save':
    $reportController->updateAction($_GET['date']);
    break;

    case 'email':
    $reportController->emailAction($_GET['date']);
    break;

    case 'delete':
    $reportController->deleteAction($_GET['date']);
    break;
}

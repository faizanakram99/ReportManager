<?php
/**
 * Created by PhpStorm.
 * User: faizan
 * Date: 13/1/18
 * Time: 11:37 PM
 */

use Symfony\Component\HttpFoundation\JsonResponse;

require_once __DIR__ . '/../bootstrap.php';

$methods = [
    'POST' => function () use ($report, $request) {
        return $report->save($request);
    },
    'GET' => function () use ($report, $request) {
        return $request->get('email') ? $report->email($request) : $report->get($request);
    },
    'DELETE' => function () use ($report, $request) {
        return $report->delete($request);
    },
];

/** @var JsonResponse $response */
$response = $methods[$request->getMethod()]();
$response->send();
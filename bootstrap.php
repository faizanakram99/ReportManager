<?php
/**
 * Created by PhpStorm.
 * User: faizan
 * Date: 13/1/18
 * Time: 4:42 PM.
 */
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

require_once 'vendor/autoload.php';

$isDevMode = true;

$config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/src/Entity'], $isDevMode, null, null, false);

$dbParams = Yaml::parseFile(__DIR__.'/config/db.yaml');

try {
    /** @var EntityManager $em */
    $em = EntityManager::create($dbParams, $config);
} catch (ORMException $e) {
    echo json_encode($e->getMessage());
}

/** @var Request $request */
$request = Request::createFromGlobals();

$report = new Api\Report($em);

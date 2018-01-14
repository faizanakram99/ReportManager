<?php
/**
 * Created by PhpStorm.
 * User: faizan
 * Date: 13/1/18
 * Time: 5:40 PM
 */

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once "bootstrap.php";

return ConsoleRunner::createHelperSet($em);
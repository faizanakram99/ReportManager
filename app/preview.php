<?php

require __DIR__ . '/vendor/autoload.php';

use Knp\Snappy\Pdf;

$snappy = new Pdf('C:/wkhtmltopdf/bin/wkhtmltopdf.exe');
$snappy->setOption('disable-javascript', false);
$snappy->setOption('print-media-type', true);

header('Content-Type: application/pdf');
echo $snappy->getOutput("../index.html");

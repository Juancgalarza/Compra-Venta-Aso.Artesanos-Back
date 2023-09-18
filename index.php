<?php

require_once 'app/app.php';
require_once 'vendor/autoload.php';

Kint::$enabled_mode = true;
Kint\Renderer\RichRenderer::$folder = false;

date_default_timezone_set('America/Guayaquil');

$app = new App();
$app->run();
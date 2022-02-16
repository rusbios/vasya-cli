<?php

require __DIR__ . '/vendor/autoload.php';

ob_start();
defined('BASE_PATH') or define('BASE_PATH', __DIR__);

(new Symfony\Component\Dotenv\Dotenv())->bootEnv(BASE_PATH.'/.env');

//$finder = new Symfony\Component\Finder\Finder();
//$finder->in([__DIR__ . '/src/']);
<?php

if (defined('BEDREST_TESTS_PATH') || define('BEDREST_TESTS_PATH', realpath(__DIR__ . '/../')));

require_once __DIR__ . '/../vendor/doctrine2/lib/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', __DIR__ . '/../vendor/doctrine2/lib/vendor/doctrine-common/lib');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', __DIR__ . '/../vendor/doctrine2/lib/vendor/doctrine-dbal/lib');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', __DIR__ . '/../vendor/doctrine2/lib');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedREST\TestFixtures', __DIR__);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedREST\Tests', __DIR__);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedREST', __DIR__ . '/../library');
$classLoader->register();

<?php

// set global constants
if (defined('BEDREST_LIB_PATH') || define('BEDREST_LIB_PATH', realpath(__DIR__ . '/../library/')));
if (defined('BEDREST_TESTS_PATH') || define('BEDREST_TESTS_PATH', realpath(__DIR__ . '/../')));
if (defined('DOCTRINE_LIB_PATH') || define('DOCTRINE_LIB_PATH', realpath(__DIR__ . '/../vendor/doctrine2/lib/')));

// initiate Doctrine class loader
require_once __DIR__ . '/../vendor/doctrine2/lib/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', DOCTRINE_LIB_PATH . '/vendor/doctrine-common/lib');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', DOCTRINE_LIB_PATH . '/vendor/doctrine-dbal/lib');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', DOCTRINE_LIB_PATH);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedREST\TestFixtures', __DIR__);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedREST\Tests', __DIR__);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedREST', BEDREST_LIB_PATH);
$classLoader->register();

// register custom annotations
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(DOCTRINE_LIB_PATH . '/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('BedREST\Mapping\\', BEDREST_LIB_PATH);

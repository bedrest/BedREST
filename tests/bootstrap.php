<?php

// set global constants
if (defined('BEDREST_LIB_PATH') || define('BEDREST_LIB_PATH', realpath(__DIR__ . '/../library/')));
if (defined('BEDREST_TESTS_PATH') || define('BEDREST_TESTS_PATH', realpath(__DIR__)));
if (defined('DOCTRINE_LIB_PATH') || define('DOCTRINE_LIB_PATH', realpath(__DIR__ . '/../vendor/doctrine/')));

// initiate Doctrine class loader
require_once __DIR__ . '/../vendor/autoload.php';

$classLoader = new \Doctrine\Common\ClassLoader('BedRest\TestFixtures', __DIR__);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedRest\Tests', __DIR__);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('BedRest', BEDREST_LIB_PATH);
$classLoader->register();

// register custom annotations
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(DOCTRINE_LIB_PATH . '/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(BEDREST_LIB_PATH . '/BedRest/Mapping/Resource/Annotations.php');


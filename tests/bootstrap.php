<?php

// Some paths
define('TESTS_BASEDIR', realpath(__DIR__));
define('LIBRARY_PATH', realpath(TESTS_BASEDIR . '/../library'));
define('VENDOR_PATH', realpath(TESTS_BASEDIR . '/../vendor'));

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            LIBRARY_PATH,
            VENDOR_PATH,
            get_include_path(),
        )
    )
);

// Load the Composer autoloader and point it at the BedREST namespaces
require_once VENDOR_PATH . '/autoload.php';
$loader = new Composer\Autoload\ClassLoader();

$loader->add('BedRest\TestFixtures', TESTS_BASEDIR);
$loader->add('BedRest\Tests', TESTS_BASEDIR);
$loader->add('BedRest', LIBRARY_PATH);
$loader->register();

// register custom annotations
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(LIBRARY_PATH . '/BedRest/Resource/Mapping/Annotations.php');
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(LIBRARY_PATH . '/BedRest/Service/Mapping/Annotations.php');

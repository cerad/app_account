<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
//  Composer\Autoload\ClassLoader;

/**
 * @var $loader ClassLoader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

//$loader->add('Cerad',   __DIR__  . '/../../cerad/src');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;

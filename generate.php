<?php

declare(strict_types=1);

use PHPModelGenerator\Model\GeneratorConfiguration;
use PHPModelGenerator\ModelGenerator;
use PHPModelGenerator\SchemaProvider\RecursiveDirectoryProvider;

require_once __DIR__ . '/vendor/autoload.php';

define('GENERATED_DIR', __DIR__ . '/src/php/types');

$generator = new ModelGenerator((new GeneratorConfiguration())
    ->setNamespacePrefix('\Tecsafe\OFCP\JWT\Types')
);

$generator
    ->generateModelDirectory(GENERATED_DIR)
    ->generateModels(new RecursiveDirectoryProvider(__DIR__ . '/schemas'), GENERATED_DIR);

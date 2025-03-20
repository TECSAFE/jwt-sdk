<?php

declare(strict_types=1);

use PHPModelGenerator\Model\GeneratorConfiguration;
use PHPModelGenerator\ModelGenerator;
use PHPModelGenerator\SchemaProcessor\PostProcessor\EnumPostProcessor;
use PHPModelGenerator\SchemaProvider\RecursiveDirectoryProvider;

require_once __DIR__ . '/vendor/autoload.php';

const GENERATED_DIR = __DIR__ . '/src/php/Types';

$generator = new ModelGenerator((new GeneratorConfiguration())
    ->setNamespacePrefix('\Tecsafe\OFCP\JWT\Types')
);

$generator
    ->generateModelDirectory(GENERATED_DIR)
    ->addPostProcessor(new EnumPostProcessor(GENERATED_DIR, 'Tecsafe\OFCP\JWT\Types'))
    ->generateModels(new RecursiveDirectoryProvider(__DIR__ . '/schemas'), GENERATED_DIR)
;

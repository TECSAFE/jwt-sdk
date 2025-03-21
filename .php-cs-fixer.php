<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('test/')
    ->in(__DIR__ . '/src/php/Types')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS2.0' => true,
        '@PHP81Migration' => true,
        'class_attributes_separation' => [
            'elements' => [
                'property' => 'one', // Fügt eine Leerzeile zwischen Eigenschaften hinzu
                'method' => 'one',   // Optional: Auch Leerzeilen zwischen Methoden
                'trait_import' => 'one'
            ],
        ],
        // Strikte Typisierung
        'declare_strict_types' => true,
        'strict_param' => true,

        // Code-Qualität
        'ordered_class_elements' => true,

        // Import-Regeln
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const']
        ],
        'no_unused_imports' => true,

        // Array-Syntax
        'array_syntax' => ['syntax' => 'short'],

        // String-Verkettung
        'concat_space' => ['spacing' => 'one'],

        // Whitespace und Formatierung
        'method_chaining_indentation' => true,
        'no_extra_blank_lines' => true,
        'no_whitespace_before_comma_in_array' => true,
        'whitespace_after_comma_in_array' => true,
        'blank_line_before_statement' => true,

        // Typisierung und Null-Handling
        'nullable_type_declaration_for_default_null_value' => true,
        'return_type_declaration' => true,

        // Bereinigung
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'remove_inheritdoc' => true
        ],

        'is_null' => true,

        // Dokumentation
        'phpdoc_line_span' => [
            'const' => 'single',
            'property' => 'single',
            'method' => 'multi'
        ],
        'phpdoc_trim' => true,
        'phpdoc_separation' => true,

        // Performance-Optimierung
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true
        ],
        // Funktionsparameter in separaten Zeilen
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false
        ],

        // Array-Elemente in separaten Zeilen
        #'array_multiline_indentation' => true,

        // Erzwinge mehrzeilige Arrays, wenn mehr als ein Element
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'arguments']
        ],
        #'multiline_array_trailing_comma' => true,
        'no_multiline_whitespace_around_double_arrow' => true

    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
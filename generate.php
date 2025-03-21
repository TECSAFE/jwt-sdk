<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name;
use PhpParser\Node\Identifier;
use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard as CodePrinter;

class JsonSchemaClassGenerator
{
    private CodePrinter $printer;

    public function __construct(
        private array $schema,
        private string $outputDirectory,
        private string $namespace = 'GeneratedSchema'
    ) {
        $this->printer = new CodePrinter([
            'shortArraySyntax' => true,
            'multiline' => true,
        ]);
    }

    public function generate(): void
    {
        if (!is_dir($this->outputDirectory)) {
            mkdir($this->outputDirectory, 0777, true);
        }

        $generatedClasses = $this->generateMainClasses($this->schema['definitions']);

        foreach ($generatedClasses as $className => $classNode) {
            $this->writeClassToFile($className, $classNode);
        }

        // Generate Enums
        $enumClasses = $this->generateEnumDefinitions($this->schema['definitions']);

        foreach ($enumClasses as $enumName => $enumNode) {
            $this->writeClassToFile($enumName, $enumNode);
        }

        $this->runPhpCsFixer();
    }

    private function writeClassToFile(string $className, Stmt\ClassLike $classNode): void
    {
        // Sanitize classname
        $sanitizedClassName = $this->sanitizeClassName($className);

        // Generate file content with namespace and use-statements
        $fileNodes = [
            new Stmt\Declare_([
                new Stmt\DeclareDeclare('strict_types', new Node\Scalar\LNumber(1))
            ]),
            new Stmt\Namespace_(
                name: new Name($this->namespace),
                stmts: [
                    // Default Use-Statements
                    $classNode
                ]
            )
        ];

        // Generate PHP-Code
        $code = $this->printer->prettyPrintFile($fileNodes);

        // Create filename with actual classname
        $filename = $this->outputDirectory . '/' . $sanitizedClassName . '.php';

        // Create output dir
        @mkdir(dirname($filename), 0777, true);

        // Write file
        file_put_contents($filename, $code);

        echo "Generated file: $filename\n";
    }

    private function generateEnumDefinitions(array $definitions): array
    {
        $enumNodes = [];

        foreach ($definitions as $name => $definition) {
            if ($this->isEnumDefinition($definition)) {
                $enumNodes[$name] = $this->createEnumNode($name, $definition);
            }
        }

        return $enumNodes;
    }

    private function isEnumDefinition(array $definition): bool
    {
        return
            isset($definition['type']) &&
            $definition['type'] === 'string' &&
            isset($definition['enum']);
    }

    private function createEnumNode(string $name, array $definition): Stmt\Enum_
    {
        $enumName = $this->sanitizeClassName($name);

        $cases = array_map(function ($value) {
            $caseName = $this->sanitizeEnumCaseName($value);
            return new Stmt\EnumCase(
                name: new Identifier($caseName),
                expr: is_string($value) ? new Node\Scalar\String_($value) : null
            );
        }, $definition['enum']);

        // Special methods only for CockpitRole
        $additionalMethods = $name === 'CockpitRole'
            ? [
                $this->createCompareRolesMethod($enumName),
                $this->createHasRoleMethod($enumName)
            ]
            : [];

        return new Stmt\Enum_(
            name: new Identifier($enumName),
            subNodes: [
                'scalarType' => new Identifier('string'),
                'stmts' => array_merge($cases, $additionalMethods)
            ]
        );
    }

    private function createCompareRolesMethod(string $enumName): Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('compareRoles', [
            'flags' => Stmt\Class_::MODIFIER_PUBLIC | Stmt\Class_::MODIFIER_STATIC,
            'params' => [
                new Node\Param(
                    var: new Expr\Variable('has'),
                    type: new Name($enumName)
                ),
                new Node\Param(
                    var: new Expr\Variable('needs'),
                    type: new Name($enumName)
                )
            ],
            'returnType' => new Name('bool'),
            'stmts' => [
                new Stmt\Return_(
                    new Expr\BinaryOp\SmallerOrEqual(
                        new Expr\FuncCall(
                            new Name('array_search'),
                            [
                                new Node\Arg(new Expr\Variable('has')),
                                new Node\Arg(
                                    new Expr\StaticCall(
                                        new Name($enumName),
                                        'cases'
                                    )
                                )
                            ]
                        ),
                        new Expr\FuncCall(
                            new Name('array_search'),
                            [
                                new Node\Arg(new Expr\Variable('needs')),
                                new Node\Arg(
                                    new Expr\StaticCall(
                                        new Name($enumName),
                                        'cases'
                                    )
                                )
                            ]
                        )
                    )
                )
            ]
        ]);
    }

    private function createHasRoleMethod(string $enumName): Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('hasRole', [
            'flags' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => [
                new Node\Param(
                    var: new Expr\Variable('givenRole'),
                    type: new Name($enumName)
                )
            ],
            'returnType' => new Name('bool'),
            'stmts' => [
                new Stmt\Return_(
                    new Expr\StaticCall(
                        new Name($enumName),
                        'compareRoles',
                        [
                            new Node\Arg(new Expr\Variable('this')),
                            new Node\Arg(new Expr\Variable('givenRole'))
                        ]
                    )
                )
            ]
        ]);
    }

    private function generateMainClasses(array $definitions): array
    {
        $mainClassNodes = [];

        foreach ($definitions as $name => $definition) {
            if (false === $this->isEnumDefinition($definition)) {
                $mainClassNodes[$name] = $this->generateMainClass($name, $definition);
            }
        }

        return $mainClassNodes;
    }

    private function generateMainClass(string $className, array $schema): Stmt\Class_
    {
        $properties = $schema['properties'] ?? [];
        $description = $schema['description'] ?? null;

        $constructorMethod = $this->generateConstructor($properties);
        $jsonMethods = $this->generateJsonMethods($className, $properties);

        $class = new Stmt\Class_(
            name: new Identifier($className),
            subNodes: [
                'flags' => 0,
                'implements' => [new Name('\JsonSerializable')],
                'stmts' => array_merge(
                    [$constructorMethod],
                    $jsonMethods
                )
            ]
        );

        if ($description) {
            $class->setDocComment(
                new \PhpParser\Comment\Doc(
                    <<<DOC
/**
 * $description.
 */
DOC
                )
            );
        }

        return $class;
    }

    private function resolvePropertyType(string $name, array $propertySchema): string
    {
        // Resolve reference
        if (isset($propertySchema['$ref'])) {
            $refName = basename($propertySchema['$ref']);
            return $this->sanitizeClassName($refName);
        }

        // Type-mapping
        if (is_array($propertySchema['type'])) {
            return implode(
                '|',
                array_map(
                    fn($type) => $this->mapType($name, $type),
                    $propertySchema['type']
                )
            );
        }

        return $this->mapType($name, $propertySchema['type'] ?? 'mixed');
    }

    private function mapType(string $name, string $type): string
    {
        if ($name === 'type') {
            return 'JwtType';
        }

        return match ($type) {
            'string' => 'string',
            'integer' => 'int',
            'number' => 'float',
            'boolean' => 'bool',
            'array' => 'array',
            'object' => 'object',
            'null' => 'null',
            default => 'mixed'
        };
    }

    private function generateConstructor(array $properties): Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('__construct', [
            'params' => array_map(function ($name, $propertySchema) {
                $type = $this->resolvePropertyType($name, $propertySchema);

                $description = $propertySchema['description'] ?? null;

                $param = new Node\Param(
                    var: new Expr\Variable($name),
                    default: $this->createDefaultValue($propertySchema),
                    type: new Identifier($type),
                    flags: \PhpParser\Modifiers::PUBLIC,
                );
                if ($description) {
                    $param->setDocComment(
                        new \PhpParser\Comment\Doc(
                            <<<DOC
/**
 * $description
 */
DOC
                        )
                    );
                }
                return $param;
            }, array_keys($properties), $properties),
        ]);
    }

    private function createDefaultValue(array $propertySchema): ?Expr
    {
        if (!isset($propertySchema['default'])) {
            return null;
        }

        $default = $propertySchema['default'];
        return match (gettype($default)) {
            'string' => new Node\Scalar\String_($default),
            'integer' => new Node\Scalar\LNumber($default),
            'double' => new Node\Scalar\DNumber($default),
            'boolean' => new Expr\ConstFetch(new Name($default ? 'true' : 'false')),
            'array' => new Expr\Array_(
                array_map(fn($val) => new Expr\ArrayItem($this->createDefaultValue(['default' => $val])), $default)
            ),
            default => new Expr\ConstFetch(new Name('null'))
        };
    }

    private function generateJsonMethods(string $className, array $properties): array
    {
        return [
            $this->createFromJsonMethod($className, $properties),
            $this->createJsonSerializeMethod(array_keys($properties))
        ];
    }

    private function createFromJsonMethod(string $className, array $properties): Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('fromJson', [
            'flags' => Stmt\Class_::MODIFIER_PUBLIC | Stmt\Class_::MODIFIER_STATIC,
            'params' => [
                new Node\Param(
                    var: new Expr\Variable('data'),
                    type: new Identifier('string|array')
                )
            ],
            'returnType' => new Name($className),
            'stmts' => [
                // Call json_decode if no array
                new Stmt\If_(
                    cond: new Expr\FuncCall(
                        new Name('!is_array'),
                        [new Node\Arg(new Expr\Variable('data'))]
                    ),
                    subNodes: [
                        'stmts' => [
                            new Stmt\Expression(
                                new Expr\Assign(
                                    new Expr\Variable('data'),
                                    new Expr\FuncCall(
                                        new Name('json_decode'),
                                        [
                                            new Node\Arg(new Expr\Variable('data')),
                                            new Node\Arg(new Expr\ConstFetch(new Name('true')))
                                        ]
                                    )
                                )
                            )
                        ]
                    ]
                ),

                // Return of the instance with data
                new Stmt\Return_(
                    new Expr\New_(
                        new Name($className),
                        array_map(function ($name, $propertySchema) {
                            $propertyValue = new Expr\ArrayDimFetch(
                                new Expr\Variable('data'),
                                new Node\Scalar\String_($name)
                            );
                            return $this->processPropertyValue($name, $propertySchema, $propertyValue);
                        }, array_keys($properties), $properties)
                    )
                )
            ]
        ]);
    }

    private function processPropertyValue(string $name, array $propertySchema, Expr $propertyValue): Node\Arg
    {
        // Check for enum reference
        if (isset($propertySchema['$ref'])) {
            $referencedName = $this->sanitizeClassName(basename($propertySchema['$ref']));

            // Check whether it is an enum
            if ($this->isEnumDefinition($this->schema['definitions'][$referencedName] ?? [])) {
                return new Node\Arg(
                    new Expr\StaticCall(
                        new Name($referencedName),
                        'from',
                        [
                            new Node\Arg($propertyValue)
                        ]
                    )
                );
            } else {
                // For non-enum types: Call fromJson
                return new Node\Arg(
                    new Expr\StaticCall(
                        new Name($referencedName),
                        'fromJson',
                        [
                            new Node\Arg($propertyValue)
                        ]
                    )
                );
            }
        }

        // Special case for 'type' property
        if ($name === 'type') {
            return new Node\Arg(
                new Expr\StaticCall(
                    new Name('JwtType'),
                    'from',
                    [
                        new Node\Arg($propertyValue)
                    ]
                )
            );
        }

        // Standard case
        return new Node\Arg($propertyValue);
    }

    private function createJsonSerializeMethod(array $propertyNames): Stmt\ClassMethod
    {
        $arrayItems = array_map(function ($name) {
            return new Expr\ArrayItem(
                new Expr\PropertyFetch(
                    new Expr\Variable('this'),
                    $name
                ),
                new Node\Scalar\String_($name),
            // Setting multiline to true forces line breaks
            );
        }, $propertyNames);

        return new Stmt\ClassMethod('jsonSerialize', [
            'flags' => Stmt\Class_::MODIFIER_PUBLIC,
            'returnType' => new Name('mixed'),
            'stmts' => [
                new Stmt\Return_(
                    new Expr\Array_($arrayItems, [
                        'multiline' => true // This parameter forces multiple lines
                    ])
                )
            ]
        ]);
    }

    private function sanitizeClassName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', ucfirst($name));
    }

    private function sanitizeEnumCaseName(string $value): string
    {
        $sanitized = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $value));
        return $sanitized[0] === '0' ? 'VALUE_' . $sanitized : $sanitized;
    }

    private function runPhpCsFixer(): void
    {
        $configFile = __DIR__ . '/.php-cs-fixer.php'; // Path to the configuration file
        $outputDirectory = realpath($this->outputDirectory);

        // Create the command
        $command = sprintf(
            'vendor/bin/php-cs-fixer fix %s --config=%s --quiet --using-cache=no --allow-risky=yes',
            escapeshellarg($outputDirectory),
            escapeshellarg($configFile)
        );

        // Execute command
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            echo "PHP-CS-Fixer has problems formatting the files.\n";
            print_r($output);
        } else {
            echo "Files successfully formatted with PHP-CS-Fixer.\n";
        }
    }
}

$schemaJson = file_get_contents('schema.json');
$schema = json_decode($schemaJson, true);

$generator = new JsonSchemaClassGenerator(
    schema: $schema,
    outputDirectory: __DIR__ . '/src/php/Types',
    namespace: 'Tecsafe\OFCP\JWT\Types'
);
$generator->generate();

<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src/AppEnvironments.php',
        __DIR__ . '/src/AppErrorHandler.php',
        __DIR__ . '/src/AppSettingsKeys.php',
        __DIR__ . '/src/ContainerKeys.php',
        __DIR__ . '/src/smvc-tools/post-composer-create-project.php',
        __DIR__ . '/src/controllers',
    ])
    ->withPhpSets( php82: true) // up to the php version here
    ->withRules([
        //AddVoidReturnTypeWhereNoReturnRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true
    )
    ->withSkip([
        \Rector\CodeQuality\Rector\If_\ShortenElseIfRector::class,
        \Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector::class,
        \Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
        \Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector::class,
        \Rector\CodeQuality\Rector\If_\CompleteMissingIfElseBracketRector::class,
    ]);

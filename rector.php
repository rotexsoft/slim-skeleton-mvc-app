<?php
declare(strict_types=1);

use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src/AppErrorHandler.php',
        __DIR__ . '/src/smvc-tools/post-composer-create-project.php',
        __DIR__ . '/src/controllers',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets( php81: true )
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

//return static function (RectorConfig $rectorConfigurator): void {
//
//    // get parameters
//    //$parameters = $containerConfigurator->parameters();
//
//    // Define what rule sets will be applied
//
//    // here we can define, what sets of rules will be applied
//    // tip: use "SetList" class to autocomplete sets
//    $rectorConfigurator->import(SetList::PHP_52);
//    $rectorConfigurator->import(SetList::PHP_53);
//    $rectorConfigurator->import(SetList::PHP_54);
//    $rectorConfigurator->import(SetList::PHP_55);
//    $rectorConfigurator->import(SetList::PHP_56);
//    $rectorConfigurator->import(SetList::PHP_70);
//    $rectorConfigurator->import(SetList::PHP_71);
//    $rectorConfigurator->import(SetList::PHP_72);
//    $rectorConfigurator->import(SetList::PHP_73);
//    $rectorConfigurator->import(SetList::PHP_74);
//    $rectorConfigurator->import(SetList::PHP_80);
//    $rectorConfigurator->import(SetList::PHP_81);
//    $rectorConfigurator->import(SetList::CODE_QUALITY);
//    $rectorConfigurator->import(SetList::CODING_STYLE);
//    $rectorConfigurator->import(SetList::DEAD_CODE);
//    $rectorConfigurator->import(SetList::TYPE_DECLARATION);
//    
//    $skipables = [
//        \Rector\CodeQuality\Rector\If_\ShortenElseIfRector::class,
//        \Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector::class,
//        \Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
//        \Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector::class,
//        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector::class,
//        \Rector\CodeQuality\Rector\If_\CompleteMissingIfElseBracketRector::class,
//    ];
//    
//    $rectorConfigurator->skip($skipables);
//};


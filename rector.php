<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/app/*',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withRules([
        DeclareStrictTypesRector::class,
        ValidationRuleArrayStringValueToArrayRector::class,
    ])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
    ])->withSkip([
        __DIR__ . '/bootstrap/cache'
    ])->withPhpSets(
        php83: true
    );

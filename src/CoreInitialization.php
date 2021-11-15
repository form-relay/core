<?php

namespace FormRelay\Core;

use FormRelay\Core\ConfigurationResolver\ContentResolver\DefaultContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\DiscreteMultiValueContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldCollectorContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FirstOfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreIfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreIfEmptyContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\JoinContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\LoopDataContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\LowerCaseContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\MapContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\NegateContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\SplitContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\UpperCaseContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\ValueContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AllEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\AnyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\EqualsEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\ExistsEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\FieldEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\GateEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IndexEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\InEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IsFalseEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IsTrueEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\KeyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\LowerCaseEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\NotEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\OrEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\ProcessedEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\RegexpEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\RequiredEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\SelfEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\TrimEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\UpperCaseEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\ValueEvaluation;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\IfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\OriginalValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\RawValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\SelfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\SwitchValueMapper;
use FormRelay\Core\DataProvider\CookieDataProvider;
use FormRelay\Core\DataProvider\IpAddressDataProvider;
use FormRelay\Core\DataProvider\TimestampDataProvider;

class CoreInitialization extends Initialization
{
    const DATA_PROVIDERS = [
        CookieDataProvider::class,
        IpAddressDataProvider::class,
        TimestampDataProvider::class,
    ];

    const EVALUATIONS = [
        AllEvaluation::class,
        AndEvaluation::class,
        SelfEvaluation::class,
        AnyEvaluation::class,
        EmptyEvaluation::class,
        EqualsEvaluation::class,
        ExistsEvaluation::class,
        FieldEvaluation::class,
        GateEvaluation::class,
        GeneralEvaluation::class,
        IndexEvaluation::class,
        InEvaluation::class,
        IsFalseEvaluation::class,
        IsTrueEvaluation::class,
        KeyEvaluation::class,
        LowerCaseEvaluation::class,
        NotEvaluation::class,
        OrEvaluation::class,
        ProcessedEvaluation::class,
        RegexpEvaluation::class,
        RequiredEvaluation::class,
        TrimEvaluation::class,
        UpperCaseEvaluation::class,
        ValueEvaluation::class,
    ];

    const CONTENT_RESOLVERS = [
        SelfContentResolver::class,
        DefaultContentResolver::class,
        DiscreteMultiValueContentResolver::class,
        FieldCollectorContentResolver::class,
        FieldContentResolver::class,
        FirstOfContentResolver::class,
        GeneralContentResolver::class,
        IfContentResolver::class,
        IgnoreContentResolver::class,
        IgnoreIfEmptyContentResolver::class,
        IgnoreIfContentResolver::class,
        InsertDataContentResolver::class,
        JoinContentResolver::class,
        ListContentResolver::class,
        LoopDataContentResolver::class,
        LowerCaseContentResolver::class,
        MapContentResolver::class,
        MultiValueContentResolver::class,
        NegateContentResolver::class,
        SplitContentResolver::class,
        TrimContentResolver::class,
        UpperCaseContentResolver::class,
        ValueContentResolver::class,
    ];

    const VALUE_MAPPERS = [
        SelfValueMapper::class,
        GeneralValueMapper::class,
        IfValueMapper::class,
        OriginalValueMapper::class,
        RawValueMapper::class,
        SwitchValueMapper::class,
    ];
}

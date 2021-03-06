<?php

namespace FormRelay\Core;

use FormRelay\Core\ConfigurationResolver\ContentResolver\DefaultContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldCollectorContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreIfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreIfEmptyContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\JoinContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\LoopDataContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\MapContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\NegateContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\RawContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\SplitContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\KeyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\ProcessedEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\RegexpEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\SelfEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\EqualsEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\ExistsEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\GateEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\InEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\NotEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\OrEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\RequiredEvaluation;
use FormRelay\Core\ConfigurationResolver\FieldMapper\AppendKeyValueFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\AppendValueFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\SelfFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\DiscreteFieldFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\DistributeFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\GeneralFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\IfEmptyFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\IfFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\IgnoreFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\JoinFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\NegateFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\PassthroughFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\SplitFieldMapper;
use FormRelay\Core\ConfigurationResolver\FieldMapper\ValueMapFieldMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\SelfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\IfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\NegateValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\OriginalValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\RawValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\SwitchValueMapper;
use FormRelay\Core\DataProvider\TimestampDataProvider;

class CoreInitialization extends Initialization
{
    const DATA_PROVIDERS = [
        TimestampDataProvider::class,
    ];
    const EVALUATIONS = [
        AndEvaluation::class,
        SelfEvaluation::class,
        EmptyEvaluation::class,
        EqualsEvaluation::class,
        ExistsEvaluation::class,
        GateEvaluation::class,
        GeneralEvaluation::class,
        InEvaluation::class,
        KeyEvaluation::class,
        NotEvaluation::class,
        OrEvaluation::class,
        ProcessedEvaluation::class,
        RegexpEvaluation::class,
        RequiredEvaluation::class,
    ];
    const CONTENT_RESOLVERS = [
        SelfContentResolver::class,
        DefaultContentResolver::class,
        FieldCollectorContentResolver::class,
        FieldContentResolver::class,
        GeneralContentResolver::class,
        IfContentResolver::class,
        IgnoreIfEmptyContentResolver::class,
        IgnoreIfContentResolver::class,
        InsertDataContentResolver::class,
        JoinContentResolver::class,
        LoopDataContentResolver::class,
        MapContentResolver::class,
        NegateContentResolver::class,
        RawContentResolver::class,
        SplitContentResolver::class,
        TrimContentResolver::class,
    ];
    const VALUE_MAPPERS = [
        SelfValueMapper::class,
        GeneralValueMapper::class,
        IfValueMapper::class,
        NegateValueMapper::class,
        OriginalValueMapper::class,
        RawValueMapper::class,
        SwitchValueMapper::class,
    ];
}

<?php

namespace FormRelay\Core;

use FormRelay\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
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
        NotEvaluation::class,
        OrEvaluation::class,
        RequiredEvaluation::class,
    ];
    const CONTENT_RESOLVERS = [
        SelfContentResolver::class,
        FieldContentResolver::class,
        GeneralContentResolver::class,
        IfContentResolver::class,
        InsertDataContentResolver::class,
        TrimContentResolver::class,
    ];
    const FIELD_MAPPERS = [
        AppendKeyValueFieldMapper::class,
        AppendValueFieldMapper::class,
        SelfFieldMapper::class,
        DiscreteFieldFieldMapper::class,
        DistributeFieldMapper::class,
        GeneralFieldMapper::class,
        IfEmptyFieldMapper::class,
        IfFieldMapper::class,
        IgnoreFieldMapper::class,
        JoinFieldMapper::class,
        NegateFieldMapper::class,
        PassthroughFieldMapper::class,
        SplitFieldMapper::class,
        ValueMapFieldMapper::class,
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

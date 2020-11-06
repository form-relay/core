<?php

namespace FormRelay\Core;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\ContentEvaluation;
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
use FormRelay\Core\ConfigurationResolver\FieldMapper\ContentFieldMapper;
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
use FormRelay\Core\ConfigurationResolver\ValueMapper\ContentValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\IfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\NegateValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\OriginalValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\RawValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\SwitchValueMapper;
use FormRelay\Core\DataProvider\TimestampDataProvider;
use FormRelay\Core\Service\RegistryInterface;

class Initialization
{
    const DATA_PROVIDERS = [
        TimestampDataProvider::class,
    ];
    const EVALUATIONS = [
        AndEvaluation::class,
        ContentEvaluation::class,
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
        ContentContentResolver::class,
        FieldContentResolver::class,
        GeneralContentResolver::class,
        IfContentResolver::class,
        InsertDataContentResolver::class,
        TrimContentResolver::class,
    ];
    const FIELD_MAPPERS = [
        AppendKeyValueFieldMapper::class,
        AppendValueFieldMapper::class,
        ContentFieldMapper::class,
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
        ContentValueMapper::class,
        GeneralValueMapper::class,
        IfValueMapper::class,
        NegateValueMapper::class,
        OriginalValueMapper::class,
        RawValueMapper::class,
        SwitchValueMapper::class,
    ];
    const DATA_DISPATCHERS = [];
    const ROUTES = [];

    public static function initialize(RegistryInterface $registry)
    {
        foreach (static::DATA_PROVIDERS as $dataProvider) {
            $registry->registerDataProvider($dataProvider);
        }
        foreach (static::EVALUATIONS as $evaluation) {
            $registry->registerEvaluation($evaluation);
        }
        foreach (static::CONTENT_RESOLVERS as $contentResolver) {
            $registry->registerContentResolver($contentResolver);
        }
        foreach (static::FIELD_MAPPERS as $fieldMapper) {
            $registry->registerFieldMapper($fieldMapper);
        }
        foreach (static::VALUE_MAPPERS as $valueMapper) {
            $registry->registerValueMapper($valueMapper);
        }
        foreach (static::ROUTES as $route) {
            $registry->registerRoute($route);
        }
        foreach (static::DATA_DISPATCHERS as $dataDispatcher) {

        }
    }
}

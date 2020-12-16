<?php

namespace FormRelay\Core;

use FormRelay\Core\Service\RegistryInterface;

abstract class Initialization
{
    const DATA_PROVIDERS = [];
    const EVALUATIONS = [];
    const CONTENT_RESOLVERS = [];
    const VALUE_MAPPERS = [];
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
        foreach (static::VALUE_MAPPERS as $valueMapper) {
            $registry->registerValueMapper($valueMapper);
        }
        foreach (static::ROUTES as $route) {
            $registry->registerRoute($route);
        }
        foreach (static::DATA_DISPATCHERS as $dataDispatcher) {
            $registry->registerDataDispatcher($dataDispatcher);
        }
    }
}

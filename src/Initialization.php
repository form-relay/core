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
        foreach (static::DATA_PROVIDERS as $keyword => $dataProvider) {
            $registry->registerDataProvider($dataProvider, [], $keyword);
        }
        foreach (static::EVALUATIONS as $keyword => $evaluation) {
            $registry->registerEvaluation($evaluation, [], $keyword);
        }
        foreach (static::CONTENT_RESOLVERS as $keyword => $contentResolver) {
            $registry->registerContentResolver($contentResolver, [], $keyword);
        }
        foreach (static::VALUE_MAPPERS as $keyword => $valueMapper) {
            $registry->registerValueMapper($valueMapper, [], $keyword);
        }
        foreach (static::ROUTES as $keyword => $route) {
            $registry->registerRoute($route, [], $keyword);
        }
        foreach (static::DATA_DISPATCHERS as $keyword => $dataDispatcher) {
            $registry->registerDataDispatcher($dataDispatcher, [], $keyword);
        }
    }
}

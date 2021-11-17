<?php

namespace FormRelay\Core\Plugin;

interface PluginInterface
{
    public function getKeyword(): string;
    public function getWeight(): int;
}

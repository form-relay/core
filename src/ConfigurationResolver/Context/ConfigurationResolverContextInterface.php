<?php

namespace FormRelay\Core\ConfigurationResolver\Context;

use ArrayAccess;

interface ConfigurationResolverContextInterface extends ArrayAccess
{
    public function copy(): ConfigurationResolverContextInterface;
}

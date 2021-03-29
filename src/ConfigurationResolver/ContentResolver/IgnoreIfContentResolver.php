<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class IgnoreIfContentResolver extends IgnoreContentResolver
{
    protected function ignore($result): bool
    {
        return $this->evaluate($this->config);
    }

    public function getWeight(): int
    {
        return 0;
    }
}

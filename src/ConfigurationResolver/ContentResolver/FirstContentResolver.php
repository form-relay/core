<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Utility\GeneralUtility;

class FirstContentResolver extends AbstractWrapperContentResolver
{
    /**
     * @param string|FieldInterface|null $result
     * @param string|FieldInterface|null $content
     * @param mixed $key
     * @return bool flag whether or not the build process should continue
     */
    protected function add(&$result, $content, $key): bool
    {
        if ($content !== null) {
            $result = $content;
        }
        return GeneralUtility::isEmpty($result);
    }
}

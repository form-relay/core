<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Utility\GeneralUtility;

class FirstContentResolver extends AbstractWrapperContentResolver
{
    /**
     * @param string|FieldInterface|null $result
     * @param string|FieldInterface|null $content
     * @return bool flag whether or not the build process should continue
     */
    protected function add(&$result, $content): bool
    {
        if ($content !== null && GeneralUtility::isEmpty($result)) {
            $result = $content;
        }
        return GeneralUtility::isEmpty($result);
    }
}

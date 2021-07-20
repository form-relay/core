<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;

class IpAddressDataProvider extends DataProvider
{
    const KEY_FIELD = 'field';
    const DEFAULT_FIELD = 'ip_address';

    protected function processContext(SubmissionInterface $submission, RequestInterface $request)
    {
        $this->addToContext($submission, 'ip_address', $request->getIpAddress());
    }

    protected function process(SubmissionInterface $submission)
    {
        $this->setFieldFromContext(
            $submission,
            'ip_address',
            $this->getConfig(static::KEY_FIELD)
        );
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_FIELD => static::DEFAULT_FIELD,
        ];
    }
}

<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;

class IpAddressDataProvider extends DataProvider
{
    const KEY_FIELD = 'ip_address';
    const DEFAULT_FIELD = 'ip_address';

    protected function processContext(SubmissionInterface $submission)
    {
        $this->addToContext($submission, 'ip_address', $this->request->getIpAddress());
    }

    protected function process(SubmissionInterface $submission)
    {
        $this->setFieldFromContext(
            $submission,
            'ip_address',
            $this->getConfig(static::KEY_FIELD, static::DEFAULT_FIELD)
        );
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_FIELD => static::DEFAULT_FIELD,
        ];
    }
}

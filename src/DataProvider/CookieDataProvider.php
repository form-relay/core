<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;

class CookieDataProvider extends DataProvider
{
    const KEY_COOKIE_FIELD_MAP = 'cookieFieldMap';
    const DEFAULT_COOKIE_FIELD_MAP = [];

    protected function processContext(SubmissionInterface $submission)
    {
        $cookies = array_keys($this->getConfig(static::KEY_COOKIE_FIELD_MAP, []));
        foreach ($cookies as $cookie) {
            $this->addCookieToContext($submission, $cookie);
        }
    }

    protected function process(SubmissionInterface $submission)
    {
        $cookieFieldMap = $this->getConfig(static::KEY_COOKIE_FIELD_MAP, []);
        foreach ($cookieFieldMap as $cookie => $field) {
            $this->setFieldFromCookie($submission, $cookie, $field);
        }
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_COOKIE_FIELD_MAP => static::DEFAULT_COOKIE_FIELD_MAP,
        ];
    }
}

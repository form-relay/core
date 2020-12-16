<?php

namespace FormRelay\Core\ConfigurationResolver;

interface ProcessedFieldsTrackerInterface
{
    public function markAsProcessed($key);
    public function markAsUnprocessed($key);
    public function hasBeenProcessed($key);
    public function reset();
    public function getProcessedFields();
}

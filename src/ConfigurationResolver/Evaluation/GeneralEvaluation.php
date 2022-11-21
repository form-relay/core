<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Service\PluginRegistryInterface;

class GeneralEvaluation extends Evaluation implements GeneralConfigurationResolverInterface
{
    protected $then;
    protected $else;

    public function __construct(string $keyword, PluginRegistryInterface $registry, LoggerInterface $logger, $config, ConfigurationResolverContextInterface $context)
    {
        parent::__construct($keyword, $registry, $logger, $config, $context);
        $this->initThenElseParts();
    }

    protected function initThenElseParts()
    {
        if (is_array($this->configuration)) {
            if (array_key_exists('then', $this->configuration)) {
                $this->then = $this->configuration['then'];
                unset($this->configuration['then']);
            }
            if (array_key_exists('else', $this->configuration)) {
                $this->else = $this->configuration['else'];
                unset($this->configuration['else']);
            }
        }
    }

    public function eval(array $keysEvaluated = []): bool
    {
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('and', $this->configuration);
        return $evaluation->eval($keysEvaluated);
    }

    /**
     * the method "resolve" is calling "eval" and depending on its result
     * it will try to return a "then" or "else" part of the config.
     * if the needed part is missing in the config, it will return null
     *
     * @param array $keysEvaluated
     * @return mixed|null
     */
    public function resolve(array $keysEvaluated = [])
    {
        $result = $this->eval($keysEvaluated);
        return $result ? $this->then : $this->else;
    }
}

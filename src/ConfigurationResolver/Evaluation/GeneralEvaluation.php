<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;

class GeneralEvaluation extends Evaluation implements GeneralConfigurationResolverInterface
{
    protected $then = null;
    protected $else = null;

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
        if (is_array($this->configuration)) {
            if (isset($this->configuration['then'])) {
                $this->then = $this->configuration['then'];
                unset($this->configuration['then']);
            }
            if (isset($this->configuration['else'])) {
                $this->else = $this->configuration['else'];
                unset($this->configuration['else']);
            }
        }
        $result = $this->eval($keysEvaluated);
        return $result ? $this->then : $this->else;
    }
}

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
        $evaluation = $this->resolveKeyword('and', $this->config);
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
        if (is_array($this->config)) {
            if (isset($this->config['then'])) {
                $this->then = $this->config['then'];
                unset($this->config['then']);
            }
            if (isset($this->config['else'])) {
                $this->else = $this->config['else'];
                unset($this->config['else']);
            }
        }
        $result = $this->eval($keysEvaluated);
        return $result ? $this->then : $this->else;
    }
}

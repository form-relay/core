<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Utility\GeneralUtility;

class GateEvaluation extends Evaluation
{
    /*
     * # case 1: multiple keys, no passes
     *
     * gate = tx_formrelay_a,tx_formrelay_b
     * =>
     * or {
     *     1.gate {
     *         key = a
     *         pass = any
     *     }
     *     2.gate {
     *         key = b
     *         pass = any
     *     }
     * }
     */
    protected function evaluateMultipleExtensions($keysEvaluated)
    {
        $keys = GeneralUtility::castValueToArray($this->config);
        $gateConfig = ['or' => []];
        foreach ($keys as $key) {
            $gateConfig['or'][] = ['gate' => ['key' => $key, 'pass' => 'any']];
        }
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('general', $gateConfig);
        return $evaluation->eval($keysEvaluated);
    }

    /*
     * # case 2: one key, indirect passes (any|all)
     *
     * gate { key=formrelay_a, pass=any|all }
     * =>
     * or|and {
     *     1.gate { key=formrelay_a, pass=0 }
     *     2.gate { key=formrelay_a, pass=1 }
     *     # ...
     *     n.gate { key=formrelay_a, pass=n }
     * }
     */
    protected function evaluateMultiplePasses($keysEvaluated)
    {
        $key = $this->config['key'];
        $gateConfigs = [];
        $count = $this->context['config']->getRoutePassCount($key);
        for ($i = 0; $i < $count; $i++) {
            $gateConfigs[] = ['gate' => ['key' => $key, 'pass' => $i]];
        }
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('general', [$this->config['pass'] === 'any' ? 'or' : 'and' => $gateConfigs]);
        return $evaluation->eval($keysEvaluated);
    }

    /*
     * # case 3: one key, one pass
     * gate { key=tx_formrelay_a, pass=n }
     * =>
     * actual evaluation of extension gate
     */
    protected function evaluateSinglePass($keysEvaluated)
    {
        $result = true;
        $key = $this->config['key'];
        $pass = $this->config['pass'];
        if (isset($keysEvaluated[$key]) && in_array($pass, $keysEvaluated[$key])) {
            $result = false;
        } else {
            $keysEvaluated[$key][] = $pass;
            $settings = $this->context['config']->getRoutePassConfiguration($key, $pass);
            if (!isset($settings['enabled']) || !$settings['enabled']) {
                $result = false;
            } elseif (isset($settings['gate']) && !empty($settings['gate'])) {
                /** @var EvaluationInterface $evaluation */
                $evaluation = $this->resolveKeyword('general', $settings['gate']);
                $result = $evaluation->eval($keysEvaluated);
            } else {
                // no gate is an automatic pass
                $result = true;
            }
        }
        return $result;
    }

    public function eval(array $keysEvaluated = []): bool
    {
        if (!is_array($this->config)) {
            return $this->evaluateMultipleExtensions($keysEvaluated);
        }

        if ($this->config['pass'] === 'any' || $this->config['pass'] === 'all') {
            return $this->evaluateMultiplePasses($keysEvaluated);
        }

        return $this->evaluateSinglePass($keysEvaluated);
    }
}

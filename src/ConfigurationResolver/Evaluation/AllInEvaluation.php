<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

/** @deprecated */
class AllInEvaluation extends InEvaluation
{
    protected function multiValueIsDisjunctive()
    {
        return false;
    }
}

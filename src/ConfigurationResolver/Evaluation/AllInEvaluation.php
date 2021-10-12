<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class AllInEvaluation extends InEvaluation
{
    protected function multiValueIsDisjunctive()
    {
        return false;
    }
}

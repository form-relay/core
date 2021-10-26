<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\EqualsEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\SelfEvaluation;
use FormRelay\Core\ConfigurationResolver\FieldTracker;
use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\SelfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\Model\Submission\Submission;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Tests\MultiValueTestTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractConfigurationResolverTest extends TestCase
{
    use MultiValueTestTrait;

    /** @var array */
    protected $data = [];

    /** @var array */
    protected $configuration = [];

    /** @var array */
    protected $context = [];

    /** @var FieldTracker */
    protected $fieldTracker;

    protected $contentResolverClasses = [];
    protected $evaluationClasses = [];
    protected $valueMapperClasses = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker = new FieldTracker();
    }

    protected function addContentResolver(string $class)
    {
        $this->contentResolverClasses[$class::getKeyword()] = $class;
    }

    protected function addEvaluation(string $class)
    {
        $this->evaluationClasses[$class::getKeyword()] = $class;
    }

    protected function addValueMapper(string $class)
    {
        $this->valueMapperClasses[$class::getKeyword()] = $class;
    }

    protected function addBasicContentResolvers()
    {
        $this->addContentResolver(GeneralContentResolver::class);
        $this->addContentResolver(SelfContentResolver::class);
        $this->addContentResolver(MultiValueContentResolver::class);
    }

    protected function addBasicEvaluations()
    {
        $this->addEvaluation(GeneralEvaluation::class);
        $this->addEvaluation(SelfEvaluation::class);
        $this->addEvaluation(EqualsEvaluation::class);

        // TODO GeneralEvaluation should just extend AndEvaluation instead of invoking it
        $this->addEvaluation(AndEvaluation::class);
    }

    protected function addBasicValueMappers()
    {
        $this->addValueMapper(GeneralValueMapper::class);
        $this->addValueMapper(SelfValueMapper::class);
    }

    protected function setupDummyData(int $amount = 3) {
        $this->data = [];
        for ($i = 1; $i <= $amount; $i++) {
            $this->data['field' . $i] = 'value' . $i;
        }
    }

    abstract protected function getGeneralResolverClass(): string;

    protected function initializeRegistry()
    {
        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->method('getConfigurationResolver')
            ->willReturnCallback(function($resolverInterface, $keyword, $config, $context) use ($registry) {
                $classes = [];
                switch ($resolverInterface) {
                    case ContentResolverInterface::class:
                        $classes = $this->contentResolverClasses;
                        break;
                    case EvaluationInterface::class:
                        $classes = $this->evaluationClasses;
                        break;
                    case ValueMapperInterface::class:
                        $classes = $this->valueMapperClasses;
                        break;
                }
                if (isset($classes[$keyword])) {
                    $class = $classes[$keyword];
                    return new $class($registry, $config, $context);
                }
                return null;
            }
        );
        return $registry;
    }

    protected function processResolver(GeneralConfigurationResolverInterface $resolver)
    {
        return $resolver->resolve();
    }

    protected function runResolverTest($config)
    {
        $registry = $this->initializeRegistry();

        $submission = new Submission($this->data, $this->configuration);
        $context = new ConfigurationResolverContext($submission, $this->context, $this->fieldTracker);

        $resolverClass = $this->getGeneralResolverClass();
        $resolver = new $resolverClass($registry, $config, $context);

        return $this->processResolver($resolver);
    }
}

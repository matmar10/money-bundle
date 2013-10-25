<?php

namespace Matmar10\Bundle\MoneyBundle\Service;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;

class CompositePropertyService
{

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    /**
     * @var \Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy[]
     */
    protected $mappers = array();

    public function __construct(CurrencyManager $currencyManager, AnnotationReader $annotationReader)
    {
        $this->currencyManager = $currencyManager;
        $this->annotationReader = $annotationReader;
    }

    /**
     * Adds the mapped properties of the composite property
     *
     * @param \ReflectionClass $reflectionClass
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return null
     */
    public function addCompositePropertiesClassMetadata(ReflectionClass $reflectionClass, ClassMetadata $classMetadata)
    {
        if(!$this->classContainsMappedProperties($reflectionClass)) {
            return;
        }

        $this->walkCompositePropertiesAnnotations($reflectionClass, function($reflectionProperty, $annotation) use ($classMetadata) {
            /**
             * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty
             */
            $mappings = $annotation->getMap($reflectionProperty);
            foreach($mappings as $map) {
                $classMetadata->mapField($map);
            }
        });
    }

    /**
     * Flattens all annotated properties into the mapped scalar values
     *
     * @param object $entity The entity to flatten
     * @return null
     */
    public function flattenCompositeProperties(&$entity)
    {
        $reflectionClass = new ReflectionClass($entity);
        if(!$this->classContainsMappedProperties($reflectionClass)) {
            return;
        }

        $this->walkCompositePropertiesAnnotations($reflectionClass, function($reflectionProperty, $annotation) use ($entity) {
            /**
             * @var $strategy \Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy
             */
            $strategy = $this->getStrategyForAnnotation($annotation);
            $strategy->flattenCompositeProperty($entity, $reflectionProperty, $annotation);
        });
    }

    /**
     * Populates all annotated compound properties using the registered strategy
     *
     * @param object $entity The entity to composte composite propertie for
     * @return null
     */
    public function composeCompositeProperties(&$entity)
    {
        $reflectionClass = new ReflectionClass($entity);
        if(!$this->classContainsMappedProperties($reflectionClass)) {
            return;
        }

        $this->walkCompositePropertiesAnnotations($reflectionClass, function($reflectionProperty, $annotation) use ($entity)  {
            /**
             * @var $strategy \Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy
             */
            $strategy = $this->getStrategyForAnnotation($annotation);
            $strategy->composeCompositeProperty($entity, $reflectionProperty, $annotation);
        });
    }

    /**
     * Apply a callback to each composite property annotation
     *
     * @param \ReflectionClass $reflectionClass The class to walk
     * @param callable $callback The callback to apply to each composite property annotation
     */
    public function walkCompositePropertiesAnnotations(ReflectionClass $reflectionClass, callable $callback)
    {
        $properties = $reflectionClass->getProperties();
        foreach($properties as $reflectionProperty) {

            // set primitive fields from all annotated fields
            $annotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);
            foreach($annotations as $annotation) {

                // only process mapped entity annotations
                if(!($annotation instanceof CompositeProperty)) {
                    continue;
                }

                call_user_func($callback, $reflectionProperty, $annotation);
            }
        }
    }

    /**
     * Checks if the entity contains the class level annotation
     *
     * @param ReflectionClass $reflectionClass The entity to check
     * @return boolean Whether the entity contains the composite property annotation
     */
    public function classContainsMappedProperties(ReflectionClass $reflectionClass)
    {
        $isMappedEntity = $this->annotationReader->getClassAnnotation($reflectionClass, 'Matmar10\\Bundle\\MoneyBundle\\Annotation\\Entity');
        if($isMappedEntity) {
            return true;
        }
        return false;
    }

    /**
     * Registers a mapper for the specified classs
     *
     * @param string $annotationClassName The class name to register the mapper for
     * @param \Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy $mapperService The mapper to register
     * @return null
     */
    public function registerStrategy($annotationClassName, CompositePropertyStrategy $mapperService)
    {
        $this->mappers[$annotationClassName] = $mapperService;
    }

    /**
     * Gets the mapper for the requested annotation
     *
     * @param \Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty $annotation The annotation to retrieve a mapper for
     * @return \Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy
     * @throws \InvalidArgumentException
     */
    public function getStrategyForAnnotation(CompositeProperty $annotation)
    {
        $className = get_class($annotation);
        if(false === array_key_exists($className, $this->mappers)) {
            throw new InvalidArgumentException(sprintf('No mapper configured for annotation %s', $className));
        }

        return $this->mappers[$className];
    }
}
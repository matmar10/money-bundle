<?php

namespace Matmar10\Bundle\MoneyBundle\Service;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Doctrine\Common\Annotations\Reader as AnnotationReader;
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
     * Flattens all annotated properties into the mapped scalar values
     *
     * @param object $entity The entity to flatten
     * @return null
     */
    public function flattenCompositeProperties(&$entity)
    {
        $reflectionObject = new ReflectionObject($entity);
        $properties = $reflectionObject->getProperties();
        foreach($properties as $fromReflectionProperty) {

            // set primitive fields from all annotated fields
            $annotations = $this->annotationReader->getPropertyAnnotations($fromReflectionProperty);
            foreach($annotations as $annotation) {

                // only process mapped entity annotations
                if(!($annotation instanceof CompositeProperty)) {
                    continue;
                }

                /**
                 * @var $strategy \Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy
                 */
                $strategy = $this->getStrategyForAnnotation($annotation);
                $strategy->flattenCompositeProperty($entity, $fromReflectionProperty, $annotation);
            }
        }
    }

    /**
     * Populates all annotated compound properties using the registered strategy
     *
     * @param object $entity The entity to composte composite propertie for
     * @return null
     */
    public function composeCompositeProperties(&$entity)
    {
        $reflectionObject = new ReflectionObject($entity);
        $properties = $reflectionObject->getProperties();
        foreach($properties as $fromReflectionProperty) {

            // set primitive fields from all annotated fields
            $annotations = $this->annotationReader->getPropertyAnnotations($fromReflectionProperty);
            foreach($annotations as $annotation) {

                // only process mapped entity annotations
                if(!($annotation instanceof CompositeProperty)) {
                    continue;
                }

                /**
                 * @var $strategy \Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy
                 */
                $strategy = $this->getStrategyForAnnotation($annotation);
                $strategy->composeCompositeProperty($entity, $fromReflectionProperty, $annotation);
            }
        }
    }

    /**
     * Checks if the entity contains the class level annotation
     *
     * @param object $entity The entity to check
     * @return boolean Whether the entity contains the composite property annotation
     */
    public function entityContainsMappedProperties($entity)
    {
        $reflectionClass = new ReflectionClass($entity);
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
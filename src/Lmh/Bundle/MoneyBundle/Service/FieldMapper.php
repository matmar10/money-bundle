<?php

namespace Lmh\Bundle\MoneyBundle\Service;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use InvalidArgumentException;
use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Lmh\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

class FieldMapper
{

    protected $currencyManager;
    protected $annotationReader;
    protected $mappers = array();

    public function __construct(CurrencyManager $currencyManager, AnnotationReader $annotationReader)
    {
        $this->currencyManager = $currencyManager;
        $this->annotationReader = $annotationReader;
    }

    /**
     * Applies the pre-persist directional mapping (complex --> primitive mapping)
     *
     * @param object $entity The entity to apply field mappings
     * @return object
     */
    public function &prePersist(&$entity)
    {

        $reflectionObject = new ReflectionObject($entity);
        $properties = $reflectionObject->getProperties();
        foreach($properties as $fromReflectionProperty) {

            // set primitive fields from all annotated fields
            $annotations = $this->annotationReader->getPropertyAnnotations($fromReflectionProperty);
            foreach($annotations as $annotation) {

                // only process mapped entity annotations
                if(!($annotation instanceof MappedPropertyAnnotationInterface)) {
                    continue;
                }

                /**
                 * @var $mapper \Lmh\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface
                 */
                $mapper = $this->getMapperForAnnotation($annotation);
                $mapper->mapPrePersist($entity, $fromReflectionProperty, $annotation);
            }
        }

        return $entity;
    }

    public function &postPersist(&$entity)
    {

        $reflectionObject = new ReflectionObject($entity);
        $properties = $reflectionObject->getProperties();
        foreach($properties as $fromReflectionProperty) {

            // set primitive fields from all annotated fields
            $annotations = $this->annotationReader->getPropertyAnnotations($fromReflectionProperty);
            foreach($annotations as $annotation) {

                // only process mapped entity annotations
                if(!($annotation instanceof MappedPropertyAnnotationInterface)) {
                    continue;
                }

                /**
                 * @var $mapper \Lmh\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface
                 */
                $mapper = $this->getMapperForAnnotation($annotation);
                $mapper->mapPostPersist($entity, $fromReflectionProperty, $annotation);
            }
        }

        return $entity;
    }

    public function registerMapper($annotationClassName, $mapperService)
    {
        $this->mappers[$annotationClassName] = $mapperService;
    }

    /**
     * @return \Lmh\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface
     */
    public function getMapperForAnnotation(MappedPropertyAnnotationInterface $annotation)
    {
        $className = get_class($annotation);
        if(false === array_key_exists($className, $this->mappers)) {
            throw new InvalidArgumentException(sprintf('No mapper configured for annotation %s', $className));
        }

        return $this->mappers[$className];
    }
}
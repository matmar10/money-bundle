<?php

namespace Lmh\Bundle\MoneyBundle\Service;

use Doctrine\Common\Annotations\Reader as DoctrineAnnotationReader;
use LogicException;
use ReflectionObject;
use ReflectionMethod;

class AnnotationReader
{

    protected $reader;
    protected $annotationClasses;

    public function __construct(DoctrineAnnotationReader $reader, array $annotationClasses = array())
    {
        $this->reader = $reader;
        $this->annotationClasses = $annotationClasses;
    }

    /**
     * @param $entity
     * @return \Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface[]
     */
    public function read($entity)
    {
        $reflectionObject = new ReflectionObject($entity);
        $properties = $reflectionObject->getProperties();
        $entityAnnotations = array();
        foreach($properties as $propertyName => $reflectionProperty) {

            // only read annotations managed by money bundle
            foreach($this->annotationClasses as $annotationClassName) {
                $annotation = $this->reader->getPropertyAnnotation($entity, $annotationClassName);
                if(is_null($annotation)) {
                    continue;
                }
                $entityAnnotations[$propertyName] = $annotation;
            }
        }

        return $entityAnnotations;
    }
}

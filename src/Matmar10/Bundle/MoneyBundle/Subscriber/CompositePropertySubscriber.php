<?php

namespace Matmar10\Bundle\MoneyBundle\Subscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\EventSubscriber;
use Matmar10\Bundle\MoneyBundle\Service\CompositePropertyService;

class CompositePropertySubscriber implements EventSubscriber
{

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CompositePropertyService
     */
    protected $compositePropertyService;

    public function __construct(CompositePropertyService $compositePropertyService)
    {
        $this->compositePropertyService = $compositePropertyService;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postLoad',
            'loadClassMetadata',
        );
    }

    public function prePersist(LifecycleEventArgs &$eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $this->compositePropertyService->flattenCompositeProperties($entity);
    }

    public function preUpdate(PreUpdateEventArgs &$eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $this->compositePropertyService->flattenCompositeProperties($entity);
    }

    public function postLoad(LifecycleEventArgs &$eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $this->compositePropertyService->composeCompositeProperties($entity);
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs &$eventArgs)
    {
        /**
         * @var $classMetadata \Doctrine\Common\Persistence\Mapping\ClassMetadata
         */
        $classMetadata = $eventArgs->getClassMetadata();

        /**
         * @var $reflectionClass \ReflectionClass
         */
        $reflectionClass = $classMetadata->getReflectionClass();

        $this->compositePropertyService->addCompositePropertiesClassMetadata($reflectionClass, $classMetadata);
    }
}
<?php

namespace Matmar10\Bundle\MoneyBundle\Subscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
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
        );
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if(!$this->compositePropertyService->entityContainsMappedProperties($entity)) {
            return;
        }
        $this->compositePropertyService->flattenCompositeProperties($entity);
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if(!$this->compositePropertyService->entityContainsMappedProperties($entity)) {
            return;
        }
        $this->compositePropertyService->flattenCompositeProperties($entity);
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if(!$this->compositePropertyService->entityContainsMappedProperties($entity)) {
            return;
        }
        $this->compositePropertyService->composeCompoundProperties($entity);
    }
}
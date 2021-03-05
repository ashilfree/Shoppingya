<?php

namespace App\Listener;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

class CreatedByUserSubscriber implements EventSubscriber{


    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if(!$entity instanceof Product)
            return;
        /** @var User $user */
        $user = $this->security->getUser();
        $entity->setCreatedBy($user);

    }

}
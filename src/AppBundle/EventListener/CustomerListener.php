<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 23.04.18
 * Time: 22:02
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Customer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class CustomerListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof Customer) {
            return;
        }

        $changes = $eventArgs->getEntityChangeSet();
        if (array_key_exists('email', $changes)) {
            $old = $changes['email'][0];
            $new = $changes['email'][1];

            if (!empty($new) && $new !== $old) {
                $entity->newEmail = true;
            }
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (!$entity instanceof Customer) {
            return;
        }

        if ($entity->newEmail === false) {
            return;
        }

        $manager = $this->container->get('app.data.manager');
        $manager->reNotifyCustomer($entity);
    }
}
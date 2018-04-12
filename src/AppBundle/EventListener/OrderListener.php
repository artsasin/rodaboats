<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 13.04.18
 * Time: 0:59
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Boat;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderLog;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * OrderListener constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @ORM\PreUpdate
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof Order) {
            return;
        }

        $changes = $eventArgs->getEntityChangeSet();
        $flat = array();

        // Flatten the changes.
        foreach ($changes as $key => $change) {
            // Flatten properties which are objects.
            if (is_object($change[0])) {
                if ($change[0] instanceof Boat) {
                    $flat[$key] = [
                        $change[0]->getName(),
                        $change[1]->getName()
                    ];
                    continue;
                }

                if ($change[0] instanceof \DateTime) {
                    $flat[$key] = [
                        $change[0]->format(\DateTime::W3C),
                        $change[1]->format(\DateTime::W3C)
                    ];
                    continue;
                }

                if ($change[0] instanceof Customer) {
                    $flat[$key] = [
                        'firstName' => [
                            $change[0]->getFirstName(),
                            $change[1]->getFirstName()
                        ],
                        'lastName' => [
                            $change[0]->getLastName(),
                            $change[1]->getLastName()
                        ],
                        'email' => [
                            $change[0]->getEmail(),
                            $change[1]->getEmail()
                        ],
                        'phoneNumber' => [
                            $change[0]->getPhoneNumber(),
                            $change[1]->getPhoneNumber()
                        ]
                    ];
                }

                // Do not include other objects.
                continue;
            }

            // Copy other values.
            $flat[$key] = $change;
        }


        // Create a log entry and schedule it for storage.
        // Attempting to persist here is discouraged according to doctrine documentation.
        $log = new OrderLog();
        $log->setOrder($entity);
        $log->setChangeset(json_encode($flat));

        $user = $this->tokenStorage->getToken()->getUser();
        $log->setUser($user);

        $entity->log = $log;
    }

    /**
     * @ORM\PostUpdate
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        // Only act on bookings.
        $entity = $eventArgs->getEntity();
        if (!($entity instanceof Order)) {
            return;
        }

        // Do nothing if there is no log to persist.
        if ($entity->log === null) {
            return;
        }

        // Store a log entry.
        $em = $eventArgs->getObjectManager();
        $em->persist($entity->log);
        $em->flush();
        $entity->log = null;
    }
}

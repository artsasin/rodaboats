<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Boat;
use AppBundle\Entity\BookingLog;
use AppBundle\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BookingListener
{
	
	protected $tokenStorage;
	
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
		
		// Only act on bookings.
		$booking = $eventArgs->getEntity();
		if (!($booking instanceof Booking))
			return;
		
		$changes = $eventArgs->getEntityChangeSet();
		$flat = array();
		 
		 
		// Flatten the changes.
		foreach($changes as $key => $change)
		{
	
			// Flatten properties which are objects.
			if(is_object($change[0]))
			{
				if($change[0] instanceof Boat)
				{
					$flat[$key] = [
							$change[0]->getName(),
							$change[1]->getName()
					];
					continue;
				}
				
				if($change[0] instanceof \DateTime)
				{
					$flat[$key] = [
							$change[0]->format(\DateTime::W3C),
							$change[1]->format(\DateTime::W3C)
					];
					continue;
				}
				 
				// Do not include other objects.
				continue;
			}
	
			// Copy other values.
			$flat[$key] = $change;
		}
		 
	
		// Create a log entry and schedule it for storage.
		// Attempting to persist here is discouraged according to doctrine documentation.
		$booking->log = new BookingLog();
		$booking->log->setBooking($booking);
		$booking->log->setChangeset(json_encode($flat));
		
		// Lookup the current user.
		$user = $this->tokenStorage->getToken()->getUser();
		$booking->log->setUser($user);
	}
	
	/**
	 * @ORM\PostUpdate
	 * @param LifecycleEventArgs $eventArgs
	 */
	public function postUpdate(LifecycleEventArgs $eventArgs)
	{
		
		// Only act on bookings.
		$booking = $eventArgs->getEntity();
		if (!($booking instanceof Booking))
			return;
		
		// Do nothing if there is no log to persist.
		if($booking->log == null)
			return;
		 
		// Store a log entry.
		$em = $eventArgs->getObjectManager();
		$em->persist($booking->log);
		$em->flush();
		$booking->log = null;
	}
}
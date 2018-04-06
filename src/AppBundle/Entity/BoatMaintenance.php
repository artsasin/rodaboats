<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="boat_maintenance")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BoatMaintenanceRepository")
 */
class BoatMaintenance
{
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Boat", inversedBy="maintenance")
	 * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
	 */
	protected $boat;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $engineOilCheck;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $shaftOilCheck;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $petrolFilterChange;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $batteryCheck;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sparkChange;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $impellorChange;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $oilFilterChange;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $steeringWheelGrease;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $throttleShiftingGrease;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $engineCleaning;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $propellerChange;
	
	public function __construct()
	{
		
	}
	
}

<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="boats")})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BoatRepository")
 */
class Boat
{
	
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DELETED = 3;
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="integer")
	 * @Assert\NotBlank()
	 */
	private $status;
	
	/**
	 * @ORM\Column(type="string", length=50)
	 * @Assert\NotBlank()
	 */
	private $name;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Location", inversedBy="boats", fetch="EAGER")
	 * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
	 */
	protected $location;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $hin;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $registration;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $trackerImei;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $trackerName;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $insurancePolicy;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank()
	 */
	private $harbor;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $shaft;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $steeringCable;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $steeringCableDate;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $batteryType;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $batteryDate;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $engineSerial;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $propeller;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $propellerDate;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $anchor;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $tank1;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $tank2;
	
	/**
	 * @ORM\OneToMany(targetEntity="Booking", mappedBy="boat")
	 */
	protected $bookings;
	
	/**
	 * @ORM\OneToMany(targetEntity="BoatPrice", mappedBy="boat")
	 * @ORM\OrderBy({"start" = "DESC"})
	 */
	protected $prices;
	
	/**
	 * @ORM\OneToMany(targetEntity="Document", mappedBy="boat")
	 * @ORM\OrderBy({"name" = "ASC"})
	 */
	protected $documents;
	
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
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $engineHours;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $engineHoursCheck;

    /**
     * @ORM\Column(name="waxed_and_polished", type="date", nullable=true)
     * @var \DateTime
     */
	private $waxedAndPolished;

    /**
     * @ORM\Column(name="charging_checked", type="date", nullable=true)
     * @var \DateTime
     */
	private $chargingChecked;

    /**
     * @ORM\Column(name="greasing_steering", type="date", nullable=true)
     * @var \DateTime
     */
	private $greasingSteering;

    /**
     * @ORM\Column(name="vaseline_upholstery", type="date", nullable=true)
     * @var \DateTime
     */
	private $vaselineUpholstery;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string
	 */
	private $comments;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Order", mappedBy="boat")
     * @var ArrayCollection
     */
	protected $orders;
	
	public function __construct()
    {
		$this->bookings = new ArrayCollection();
		$this->prices = new ArrayCollection();
		$this->documents = new ArrayCollection();
		$this->orders = new ArrayCollection();
	}
	
	/**
	 * Returns the name of the passed status code.
	 * @param int $status
     * @return string
	 */
	public static function statusName($status)
	{
		switch($status)
		{
			case Boat::STATUS_ACTIVE:
				return "Active";
			case Boat::STATUS_DELETED:
				return "Deleted";
			case Boat::STATUS_INACTIVE:
				return "Inactive";
			default:
				return "UNKNOWN";
		}
	}
	
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Boat
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set hin
     *
     * @param string $hin
     *
     * @return Boat
     */
    public function setHin($hin)
    {
        $this->hin = $hin;

        return $this;
    }

    /**
     * Get hin
     *
     * @return string
     */
    public function getHin()
    {
        return $this->hin;
    }

    /**
     * Set registration
     *
     * @param string $registration
     *
     * @return Boat
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Get registration
     *
     * @return string
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Set trackerImei
     *
     * @param string $trackerImei
     *
     * @return Boat
     */
    public function setTrackerImei($trackerImei)
    {
        $this->trackerImei = $trackerImei;

        return $this;
    }

    /**
     * Get trackerImei
     *
     * @return string
     */
    public function getTrackerImei()
    {
        return $this->trackerImei;
    }

    /**
     * Set trackerName
     *
     * @param string $trackerName
     *
     * @return Boat
     */
    public function setTrackerName($trackerName)
    {
        $this->trackerName = $trackerName;

        return $this;
    }

    /**
     * Get trackerName
     *
     * @return string
     */
    public function getTrackerName()
    {
        return $this->trackerName;
    }

    /**
     * Set insurancePolicy
     *
     * @param string $insurancePolicy
     *
     * @return Boat
     */
    public function setInsurancePolicy($insurancePolicy)
    {
        $this->insurancePolicy = $insurancePolicy;

        return $this;
    }

    /**
     * Get insurancePolicy
     *
     * @return string
     */
    public function getInsurancePolicy()
    {
        return $this->insurancePolicy;
    }

    /**
     * Set harbor
     *
     * @param string $harbor
     *
     * @return Boat
     */
    public function setHarbor($harbor)
    {
        $this->harbor = $harbor;

        return $this;
    }

    /**
     * Get harbor
     *
     * @return string
     */
    public function getHarbor()
    {
        return $this->harbor;
    }

    /**
     * Set shaft
     *
     * @param string $shaft
     *
     * @return Boat
     */
    public function setShaft($shaft)
    {
        $this->shaft = $shaft;

        return $this;
    }

    /**
     * Get shaft
     *
     * @return string
     */
    public function getShaft()
    {
        return $this->shaft;
    }

    /**
     * Set steeringCable
     *
     * @param string $steeringCable
     *
     * @return Boat
     */
    public function setSteeringCable($steeringCable)
    {
        $this->steeringCable = $steeringCable;

        return $this;
    }

    /**
     * Get steeringCable
     *
     * @return string
     */
    public function getSteeringCable()
    {
        return $this->steeringCable;
    }

    /**
     * Set steeringCableDate
     *
     * @param \DateTime $steeringCableDate
     *
     * @return Boat
     */
    public function setSteeringCableDate($steeringCableDate)
    {
        $this->steeringCableDate = $steeringCableDate;

        return $this;
    }

    /**
     * Get steeringCableDate
     *
     * @return \DateTime
     */
    public function getSteeringCableDate()
    {
        return $this->steeringCableDate;
    }

    /**
     * Set batteryType
     *
     * @param string $batteryType
     *
     * @return Boat
     */
    public function setBatteryType($batteryType)
    {
        $this->batteryType = $batteryType;

        return $this;
    }

    /**
     * Get batteryType
     *
     * @return string
     */
    public function getBatteryType()
    {
        return $this->batteryType;
    }

    /**
     * Set batteryDate
     *
     * @param \DateTime $batteryDate
     *
     * @return Boat
     */
    public function setBatteryDate($batteryDate)
    {
        $this->batteryDate = $batteryDate;

        return $this;
    }

    /**
     * Get batteryDate
     *
     * @return \DateTime
     */
    public function getBatteryDate()
    {
        return $this->batteryDate;
    }

    /**
     * Set engineSerial
     *
     * @param string $engineSerial
     *
     * @return Boat
     */
    public function setEngineSerial($engineSerial)
    {
        $this->engineSerial = $engineSerial;

        return $this;
    }

    /**
     * Get engineSerial
     *
     * @return string
     */
    public function getEngineSerial()
    {
        return $this->engineSerial;
    }

    /**
     * Set propeller
     *
     * @param string $propeller
     *
     * @return Boat
     */
    public function setPropeller($propeller)
    {
        $this->propeller = $propeller;

        return $this;
    }

    /**
     * Get propeller
     *
     * @return string
     */
    public function getPropeller()
    {
        return $this->propeller;
    }

    /**
     * Set propellerDate
     *
     * @param \DateTime $propellerDate
     *
     * @return Boat
     */
    public function setPropellerDate($propellerDate)
    {
        $this->propellerDate = $propellerDate;

        return $this;
    }

    /**
     * Get propellerDate
     *
     * @return \DateTime
     */
    public function getPropellerDate()
    {
        return $this->propellerDate;
    }

    /**
     * Set anchor
     *
     * @param string $anchor
     *
     * @return Boat
     */
    public function setAnchor($anchor)
    {
        $this->anchor = $anchor;

        return $this;
    }

    /**
     * Get anchor
     *
     * @return string
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     * Set tank1
     *
     * @param string $tank1
     *
     * @return Boat
     */
    public function setTank1($tank1)
    {
        $this->tank1 = $tank1;

        return $this;
    }

    /**
     * Get tank1
     *
     * @return string
     */
    public function getTank1()
    {
        return $this->tank1;
    }

    /**
     * Set tank2
     *
     * @param string $tank2
     *
     * @return Boat
     */
    public function setTank2($tank2)
    {
        $this->tank2 = $tank2;

        return $this;
    }

    /**
     * Get tank2
     *
     * @return string
     */
    public function getTank2()
    {
        return $this->tank2;
    }

    /**
     * Add booking
     *
     * @param \AppBundle\Entity\Booking $booking
     *
     * @return Boat
     */
    public function addBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->bookings[] = $booking;

        return $this;
    }

    /**
     * Remove booking
     *
     * @param \AppBundle\Entity\Booking $booking
     */
    public function removeBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->bookings->removeElement($booking);
    }

    /**
     * Get bookings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookings()
    {
        return $this->bookings;
    }
    
    /**
     * Get prices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrices()
    {
    	return $this->prices;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Boat
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Gets human readable status name.
     * @return string
     */
    public function getStatusName()
    {
    	return Boat::statusName($this->status);
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return Boat
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Add price
     *
     * @param \AppBundle\Entity\BoatPrice $price
     *
     * @return Boat
     */
    public function addPrice(\AppBundle\Entity\BoatPrice $price)
    {
        $this->prices[] = $price;

        return $this;
    }

    /**
     * Remove price
     *
     * @param \AppBundle\Entity\BoatPrice $price
     */
    public function removePrice(\AppBundle\Entity\BoatPrice $price)
    {
        $this->prices->removeElement($price);
    }

    /**
     * Add document
     *
     * @param \AppBundle\Entity\Document $document
     *
     * @return Boat
     */
    public function addDocument(\AppBundle\Entity\Document $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \AppBundle\Entity\Document $document
     */
    public function removeDocument(\AppBundle\Entity\Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set engineOilCheck
     *
     * @param \DateTime $engineOilCheck
     *
     * @return Boat
     */
    public function setEngineOilCheck($engineOilCheck)
    {
        $this->engineOilCheck = $engineOilCheck;

        return $this;
    }

    /**
     * Get engineOilCheck
     *
     * @return \DateTime
     */
    public function getEngineOilCheck()
    {
        return $this->engineOilCheck;
    }

    /**
     * Set shaftOilCheck
     *
     * @param \DateTime $shaftOilCheck
     *
     * @return Boat
     */
    public function setShaftOilCheck($shaftOilCheck)
    {
        $this->shaftOilCheck = $shaftOilCheck;

        return $this;
    }

    /**
     * Get shaftOilCheck
     *
     * @return \DateTime
     */
    public function getShaftOilCheck()
    {
        return $this->shaftOilCheck;
    }

    /**
     * Set petrolFilterChange
     *
     * @param \DateTime $petrolFilterChange
     *
     * @return Boat
     */
    public function setPetrolFilterChange($petrolFilterChange)
    {
        $this->petrolFilterChange = $petrolFilterChange;

        return $this;
    }

    /**
     * Get petrolFilterChange
     *
     * @return \DateTime
     */
    public function getPetrolFilterChange()
    {
        return $this->petrolFilterChange;
    }

    /**
     * Set batteryCheck
     *
     * @param \DateTime $batteryCheck
     *
     * @return Boat
     */
    public function setBatteryCheck($batteryCheck)
    {
        $this->batteryCheck = $batteryCheck;

        return $this;
    }

    /**
     * Get batteryCheck
     *
     * @return \DateTime
     */
    public function getBatteryCheck()
    {
        return $this->batteryCheck;
    }

    /**
     * Set sparkChange
     *
     * @param \DateTime $sparkChange
     *
     * @return Boat
     */
    public function setSparkChange($sparkChange)
    {
        $this->sparkChange = $sparkChange;

        return $this;
    }

    /**
     * Get sparkChange
     *
     * @return \DateTime
     */
    public function getSparkChange()
    {
        return $this->sparkChange;
    }

    /**
     * Set impellorChange
     *
     * @param \DateTime $impellorChange
     *
     * @return Boat
     */
    public function setImpellorChange($impellorChange)
    {
        $this->impellorChange = $impellorChange;

        return $this;
    }

    /**
     * Get impellorChange
     *
     * @return \DateTime
     */
    public function getImpellorChange()
    {
        return $this->impellorChange;
    }

    /**
     * Set oilFilterChange
     *
     * @param \DateTime $oilFilterChange
     *
     * @return Boat
     */
    public function setOilFilterChange($oilFilterChange)
    {
        $this->oilFilterChange = $oilFilterChange;

        return $this;
    }

    /**
     * Get oilFilterChange
     *
     * @return \DateTime
     */
    public function getOilFilterChange()
    {
        return $this->oilFilterChange;
    }

    /**
     * Set steeringWheelGrease
     *
     * @param \DateTime $steeringWheelGrease
     *
     * @return Boat
     */
    public function setSteeringWheelGrease($steeringWheelGrease)
    {
        $this->steeringWheelGrease = $steeringWheelGrease;

        return $this;
    }

    /**
     * Get steeringWheelGrease
     *
     * @return \DateTime
     */
    public function getSteeringWheelGrease()
    {
        return $this->steeringWheelGrease;
    }

    /**
     * Set throttleShiftingGrease
     *
     * @param \DateTime $throttleShiftingGrease
     *
     * @return Boat
     */
    public function setThrottleShiftingGrease($throttleShiftingGrease)
    {
        $this->throttleShiftingGrease = $throttleShiftingGrease;

        return $this;
    }

    /**
     * Get throttleShiftingGrease
     *
     * @return \DateTime
     */
    public function getThrottleShiftingGrease()
    {
        return $this->throttleShiftingGrease;
    }

    /**
     * Set engineCleaning
     *
     * @param \DateTime $engineCleaning
     *
     * @return Boat
     */
    public function setEngineCleaning($engineCleaning)
    {
        $this->engineCleaning = $engineCleaning;

        return $this;
    }

    /**
     * Get engineCleaning
     *
     * @return \DateTime
     */
    public function getEngineCleaning()
    {
        return $this->engineCleaning;
    }

    /**
     * Set propellerChange
     *
     * @param \DateTime $propellerChange
     *
     * @return Boat
     */
    public function setPropellerChange($propellerChange)
    {
        $this->propellerChange = $propellerChange;

        return $this;
    }

    /**
     * Get propellerChange
     *
     * @return \DateTime
     */
    public function getPropellerChange()
    {
        return $this->propellerChange;
    }
    
    /**
     * Attempts to retrieve the current price record for the passed date (defaults to now).
     * @return BoatPrice
     */
    public function getCurrentPrice(\DateTime $date = null)
    {
    	
    	if($date == null)
    		$date = new \DateTime();
    	
    	$criteria = Criteria::create()
    	->andWhere(Criteria::expr()->lte("start", $date))
    	->andWhere(Criteria::expr()->gte("end", $date));
    	
    	$prices = $this->prices->matching($criteria);
    	if($prices->count() == 0)
    		return null;
    	
    	$prices->first();
    	return $prices->current();
    }

    /**
     * Set engineHours
     *
     * @param integer $engineHours
     *
     * @return Boat
     */
    public function setEngineHours($engineHours)
    {
        $this->engineHours = $engineHours;

        return $this;
    }

    /**
     * Get engineHours
     *
     * @return integer
     */
    public function getEngineHours()
    {
        return $this->engineHours;
    }

    /**
     * Set engineHoursCheck
     *
     * @param \DateTime $engineHoursCheck
     *
     * @return Boat
     */
    public function setEngineHoursCheck($engineHoursCheck)
    {
        $this->engineHoursCheck = $engineHoursCheck;

        return $this;
    }

    /**
     * Get engineHoursCheck
     *
     * @return \DateTime
     */
    public function getEngineHoursCheck()
    {
        return $this->engineHoursCheck;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return Boat
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function addOrder(Order $order)
    {
        $this->orders[] = $order;
        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function removeOrder(Order $order)
    {
        $this->orders->removeElement($order);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return \DateTime
     */
    public function getWaxedAndPolished()
    {
        return $this->waxedAndPolished;
    }

    /**
     * @param \DateTime $waxedAndPolished
     * @return Boat
     */
    public function setWaxedAndPolished($waxedAndPolished)
    {
        $this->waxedAndPolished = $waxedAndPolished;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getChargingChecked()
    {
        return $this->chargingChecked;
    }

    /**
     * @param \DateTime $chargingChecked
     * @return Boat
     */
    public function setChargingChecked($chargingChecked)
    {
        $this->chargingChecked = $chargingChecked;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getGreasingSteering()
    {
        return $this->greasingSteering;
    }

    /**
     * @param \DateTime $greasingSteering
     * @return Boat
     */
    public function setGreasingSteering($greasingSteering)
    {
        $this->greasingSteering = $greasingSteering;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getVaselineUpholstery()
    {
        return $this->vaselineUpholstery;
    }

    /**
     * @param \DateTime $vaselineUpholstery
     * @return Boat
     */
    public function setVaselineUpholstery($vaselineUpholstery)
    {
        $this->vaselineUpholstery = $vaselineUpholstery;
        return $this;
    }
}

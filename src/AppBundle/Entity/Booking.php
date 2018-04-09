<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="bookings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookingRepository")
 */
class Booking
{
	
	const STATUS_CONFIRMED = 1;
	const STATUS_CANCELLED = 2;
	const STATUS_DELIVERED = 3;
	const STATUS_CLOSED = 4;
	
	const CANCALLATION_NONE = 0;
	const CANCELLATION_WEATHER = 1;
	const CANCELLATION_NOSHOW = 2;
	const CANCELLATION_REPAIR = 3;
	const CANCELLATION_PRIVATE = 4;
	
	const TYPE_REGULAR = 1;
	const TYPE_FISHING = 2;
	
	public function __construct()
	{
		
		$this->type = Booking::TYPE_REGULAR;
		$this->rent = 0;
		$this->damage = '';
		$this->damageAmount = 0;
		$this->rentDiscount = 0;
		$this->commission = 0;
		$this->petrolCost = 0;
		$this->deposit = 0;
		$this->kickback = 0;
		
		$this->numberOfPeople = 0;
		
		$this->status = Booking::STATUS_CONFIRMED;
		
		$this->cancellation = Booking::CANCALLATION_NONE;
	}
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	private $status;
	
	public function getStatusName()
	{
		return Booking::statusName($this->status);
	}
	
	/**
	 * @ORM\Column(type="integer")
	 */
	private $type;
	
	public function getTypeName()
	{
		return Booking::typeName($this->type);
	}
	
	/**
     * @ORM\ManyToOne(targetEntity="Boat", inversedBy="bookings")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
	protected $boat;
	
	/**
	 * @ORM\Column(type="date")
	 */
	private $date;
	
	/**
	 * @ORM\Column(type="time")
	 */
	private $start;

	/**
	 * @ORM\Column(type="time")
	 */
	private $end;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Location", inversedBy="boats", fetch="EAGER")
	 * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
	 */
	protected $location;
	
	/**
	 * Language in which the contract and communication are to be done.
	 * @ORM\Column(type="string", length=50)
	 */
	private $language;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $rent;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $rentDiscount;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $petrolCost;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $damageAmount;
	
	/**
	 * Used for correction and damage payments.
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $damage;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $paymentMethodDamage;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $paymentMethodRent;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $deposit;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $commission;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $commissionPaidTo;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $kickback;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $paymentMethodDeposit;
	
	/**
	 * Name of the person who rented the boat.
	 * @ORM\Column(type="string", length=255)
	 */
	private $lesseeFirstName;
	
	/**
	 * Name of the person who rented the boat.
	 * @ORM\Column(type="string", length=255)
	 */
	private $lesseeLastName;
	
	/**
	 * Email address of the person who rented the boat.
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $lesseeEmail;
	
	/**
	 * @ORM\Column(type="string", length=25)
	 */
	private $lesseePhone;
	
	/**
	 * Idenfication card or passport number of the lessee.
	 * @ORM\Column(type="string", length=25, nullable=true)
	 */
	private $lesseeIdentityNumber;
	
	/**
	 * @ORM\Column(type="integer")
     * @Assert\GreaterThan(value = 0)
	 */
	private $numberOfPeople;
	
	/**
	 * Contains log item generated from update.
	 * @var unknown
	 */
	public $log;
	
	/**
	 * @ORM\Column(type="integer")
	 * @var unknown
	 */
	private $cancellation;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $handler;
	
	/**
	 * Name of the person who made the booking (agent).
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $bookedBy;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var unknown
	 */
	private $comments;
	
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Booking
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Booking
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Booking
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Booking
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Booking
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set rent
     *
     * @param float $rent
     *
     * @return Booking
     */
    public function setRent($rent)
    {
        $this->rent = $rent;

        return $this;
    }

    /**
     * Get rent
     *
     * @return float
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * Set rentDiscount
     *
     * @param float $rentDiscount
     *
     * @return Booking
     */
    public function setRentDiscount($rentDiscount)
    {
        $this->rentDiscount = $rentDiscount;

        return $this;
    }

    /**
     * Get rentDiscount
     *
     * @return float
     */
    public function getRentDiscount()
    {
        return $this->rentDiscount;
    }

    /**
     * Set petrolCost
     *
     * @param float $petrolCost
     *
     * @return Booking
     */
    public function setPetrolCost($petrolCost)
    {
        $this->petrolCost = $petrolCost;

        return $this;
    }

    /**
     * Get petrolCost
     *
     * @return float
     */
    public function getPetrolCost()
    {
        return $this->petrolCost;
    }

    /**
     * Set damageAmount
     *
     * @param float $damageAmount
     *
     * @return Booking
     */
    public function setDamageAmount($damageAmount)
    {
        $this->damageAmount = $damageAmount;

        return $this;
    }

    /**
     * Get damageAmount
     *
     * @return float
     */
    public function getDamageAmount()
    {
        return $this->damageAmount;
    }

    /**
     * Set damage
     *
     * @param string $damage
     *
     * @return Booking
     */
    public function setDamage($damage)
    {
        $this->damage = $damage;

        return $this;
    }

    /**
     * Get damage
     *
     * @return string
     */
    public function getDamage()
    {
        return $this->damage;
    }

    /**
     * Set paymentMethodRent
     *
     * @param string $paymentMethodRent
     *
     * @return Booking
     */
    public function setPaymentMethodRent($paymentMethodRent)
    {
        $this->paymentMethodRent = $paymentMethodRent;

        return $this;
    }

    /**
     * Get paymentMethodRent
     *
     * @return string
     */
    public function getPaymentMethodRent()
    {
        return $this->paymentMethodRent;
    }

    /**
     * Set paymentMethodDeposit
     *
     * @param string $paymentMethodDeposit
     *
     * @return Booking
     */
    public function setPaymentMethodDeposit($paymentMethodDeposit)
    {
        $this->paymentMethodDeposit = $paymentMethodDeposit;

        return $this;
    }

    /**
     * Get paymentMethodDeposit
     *
     * @return string
     */
    public function getPaymentMethodDeposit()
    {
        return $this->paymentMethodDeposit;
    }

    /**
     * Set numberOfPeople
     *
     * @param integer $numberOfPeople
     *
     * @return Booking
     */
    public function setNumberOfPeople($numberOfPeople)
    {
        $this->numberOfPeople = $numberOfPeople;

        return $this;
    }

    /**
     * Get numberOfPeople
     *
     * @return integer
     */
    public function getNumberOfPeople()
    {
        return $this->numberOfPeople;
    }

    /**
     * Set boat
     *
     * @param \AppBundle\Entity\Boat $boat
     *
     * @return Booking
     */
    public function setBoat(\AppBundle\Entity\Boat $boat = null)
    {
        $this->boat = $boat;

        return $this;
    }

    /**
     * Get boat
     *
     * @return \AppBundle\Entity\Boat
     */
    public function getBoat()
    {
        return $this->boat;
    }

    /**
     * Set lesseePhone
     *
     * @param string $lesseePhone
     *
     * @return Booking
     */
    public function setLesseePhone($lesseePhone)
    {
        $this->lesseePhone = $lesseePhone;

        return $this;
    }

    /**
     * Get lesseePhone
     *
     * @return string
     */
    public function getLesseePhone()
    {
        return $this->lesseePhone;
    }

    /**
     * Set lesseeIdentityNumber
     *
     * @param string $lesseeIdentityNumber
     *
     * @return Booking
     */
    public function setLesseeIdentityNumber($lesseeIdentityNumber)
    {
        $this->lesseeIdentityNumber = $lesseeIdentityNumber;

        return $this;
    }

    /**
     * Get lesseeIdentityNumber
     *
     * @return string
     */
    public function getLesseeIdentityNumber()
    {
        return $this->lesseeIdentityNumber;
    }
    
    /**
     * Returns the name of the passed status code.
     * @param unknown $status
     */
    public static function statusName($status)
    {
    	$statuses = Booking::statuses();
    	if(!array_key_exists($status, $statuses))
    		return "UNKNOWN";
    	
    	return $statuses[$status];
    }
    
    public static function statuses()
    {
    	return array(
    			Booking::STATUS_CONFIRMED => "Confirmed",
    			Booking::STATUS_CANCELLED => "Cancelled",
    			//Booking::STATUS_DELIVERED => "Delivered",
    			Booking::STATUS_CLOSED => "Closed",
    	);
    }
    
    /**
     * Returns the name of the passed cancellation code.
     * @param unknown $status
     */
    public static function cancellationName($status)
    {
    	$reasons = Booking::cancellationReasons();
    	if(!array_key_exists($status, $reasons))
    		return "UNKNOWN";
    	return $reasons[$status];
    }
    
    public function getCancellationName()
    {
    	return Booking::cancellationName($this->cancellation);
    }
    
    public static function paymentMethods($includeEmpty = false)
    {
    	
    	$methods = [];
    	if($includeEmpty)
    		$methods[''] = '';
    	$methods['Credit card'] = 'CreditCard';
    	//$methods['Debit card'] = 'DebitCard';
    	$methods['Cash'] = 'Cash';
    	
    	return $methods;
    }
    
    public static function cancellationReasons($includeEmpty = false)
    {
    	$reasons = array(
    			Booking::CANCALLATION_NONE => "None",
    			Booking::CANCELLATION_NOSHOW => "No show",
    			Booking::CANCELLATION_REPAIR => "Repairs",
    			Booking::CANCELLATION_WEATHER => "Weather",
    			Booking::CANCELLATION_PRIVATE => "Private",
    	);
    	
    	if($includeEmpty)
    		array_unshift($reasons, array('' => ''));
    		
    	return $reasons;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Booking
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
     * Set deposit
     *
     * @param float $deposit
     *
     * @return Booking
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return float
     */
    public function getDeposit()
    {
        return $this->deposit;
    }
    
    /**
     * Returns the length of the booking in hours rounded up.
     */
    public function getHours()
    {
    	
    	// Ensure that a duration can be calculated.
    	if($this->start === null || $this->end === null)
    		return null;
    	
    	$diff = $this->start->diff($this->end, true);
    	return $diff->h;
    	
    }
    
    

    /**
     * Set commission
     *
     * @param float $commission
     *
     * @return Booking
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set cancellation
     *
     * @param integer $cancellation
     *
     * @return Booking
     */
    public function setCancellation($cancellation)
    {
        $this->cancellation = $cancellation;

        return $this;
    }

    /**
     * Get cancellation
     *
     * @return integer
     */
    public function getCancellation()
    {
        return $this->cancellation;
    }

    /**
     * Set creator
     *
     * @param string $creator
     *
     * @return Booking
     */
    public function setBookedBy($bookedBy)
    {
        $this->bookedBy = $bookedBy;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \AppBundle\Entity\User
     */
    public function getBookedBy()
    {
        return $this->bookedBy;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return Booking
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
     * Set handler
     *
     * @param \AppBundle\Entity\User $handler
     *
     * @return Booking
     */
    public function setHandler(\AppBundle\Entity\User $handler = null)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Get handler
     *
     * @return \AppBundle\Entity\User
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set lesseeEmail
     *
     * @param string $lesseeEmail
     *
     * @return Booking
     */
    public function setLesseeEmail($lesseeEmail)
    {
        $this->lesseeEmail = $lesseeEmail;

        return $this;
    }

    /**
     * Get lesseeEmail
     *
     * @return string
     */
    public function getLesseeEmail()
    {
        return $this->lesseeEmail;
    }

    /**
     * Set commissionPaidTo
     *
     * @param string $commissionPaidTo
     *
     * @return Booking
     */
    public function setCommissionPaidTo($commissionPaidTo)
    {
        $this->commissionPaidTo = $commissionPaidTo;

        return $this;
    }

    /**
     * Get commissionPaidTo
     *
     * @return string
     */
    public function getCommissionPaidTo()
    {
        return $this->commissionPaidTo;
    }

    /**
     * Set kickback
     *
     * @param float $kickback
     *
     * @return Booking
     */
    public function setKickback($kickback)
    {
        $this->kickback = $kickback;

        return $this;
    }

    /**
     * Get kickback
     *
     * @return float
     */
    public function getKickback()
    {
        return $this->kickback;
    }

    /**
     * Set lesseeFirstName
     *
     * @param string $lesseeFirstName
     *
     * @return Booking
     */
    public function setLesseeFirstName($lesseeFirstName)
    {
        $this->lesseeFirstName = $lesseeFirstName;

        return $this;
    }

    /**
     * Get lesseeFirstName
     *
     * @return string
     */
    public function getLesseeFirstName()
    {
        return $this->lesseeFirstName;
    }

    /**
     * Set lesseeLastName
     *
     * @param string $lesseeLastName
     *
     * @return Booking
     */
    public function setLesseeLastName($lesseeLastName)
    {
        $this->lesseeLastName = $lesseeLastName;

        return $this;
    }

    /**
     * Get lesseeLastName
     *
     * @return string
     */
    public function getLesseeLastName()
    {
        return $this->lesseeLastName;
    }
    
    public function getLesseeName()
    {
    	return $this->lesseeFirstName . ' ' . $this->lesseeLastName;
    }
    
    public static function typeName($type)
    {
    	$types = Booking::types();
    	if(!array_key_exists($type, $types))
    		return "UNKNOWN";
    	 
    	return $types[$type];
    }
    
    public static function types()
    {
    	return array(
    			Booking::TYPE_REGULAR => "Regular",
    			Booking::TYPE_FISHING => "Fishing",
    	);
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Booking
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set paymentMethodDamage
     *
     * @param string $paymentMethodDamage
     *
     * @return Booking
     */
    public function setPaymentMethodDamage($paymentMethodDamage)
    {
        $this->paymentMethodDamage = $paymentMethodDamage;

        return $this;
    }

    /**
     * Get paymentMethodDamage
     *
     * @return string
     */
    public function getPaymentMethodDamage()
    {
        return $this->paymentMethodDamage;
    }
}

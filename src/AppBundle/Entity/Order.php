<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 07.04.18
 * Time: 1:43
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\DataProvider\OrderDataProvider;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Order
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $type;

    /**
     * @ORM\Column(type="date")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     * @var \DateTime
     */
    private $start;

    /**
     * @ORM\Column(type="time")
     * @var \DateTime
     */
    private $end;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $rent;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $rentDiscount;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $petrolCost;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $damageAmount;

    /**
     * Used for correction and damage payments.
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $damage;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @var string
     */
    private $paymentMethodDamage;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @var string
     */
    private $paymentMethodRent;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $deposit;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $commission;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @var string
     */
    private $commissionPaidTo;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $kickback;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @var string
     */
    private $paymentMethodDeposit;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(value = 0)
     * @var int
     */
    private $numberOfPeople;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $cancellation;

    /**
     * Name of the person who made the booking (agent).
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $bookedBy;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $comments;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @var array
     */
    private $extra;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="orders")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false, onDelete="SET NULL")
     * @var Customer
     */
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Boat", inversedBy="orders")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id", onDelete="SET NULL")
     * @var Boat
     */
    protected $boat;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="orders", fetch="EAGER")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id", onDelete="SET NULL")
     * @var Location
     */
    protected $location;

    /**
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     * @var User
     */
    protected $handler;

    /**
     * @ORM\Column(name="customer_data", type="text", nullable=true)
     * @var string
     */
    protected $customerData;

    /**
     * @ORM\Column(name="boat_data", type="text", nullable=true)
     * @var string
     */
    protected $boatData;

    /**
     * @ORM\Column(name="location_data", type="text", nullable=true)
     * @var string
     */
    protected $locationData;

    /**
     * Contains log item generated from update.
     * @var OrderLog
     */
    public $log;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->type = OrderDataProvider::TYPE_REGULAR;
        $this->rent = 0;
        $this->damage = '';
        $this->damageAmount = 0;
        $this->rentDiscount = 0;
        $this->commission = 0;
        $this->petrolCost = 0;
        $this->deposit = 0;
        $this->kickback = 0;
        $this->numberOfPeople = 0;
        $this->status = OrderDataProvider::STATUS_CONFIRMED;
        $this->cancellation = OrderDataProvider::CANCELLATION_REASON_NONE;
        $this->extra = [];
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return OrderDataProvider::statusName($this->status);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Order
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return OrderDataProvider::typeName($this->type);
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Order
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return Order
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return Order
     */
    public function setEnd($end)
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return float
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * @param float $rent
     * @return Order
     */
    public function setRent($rent)
    {
        $this->rent = $rent;
        return $this;
    }

    /**
     * @return float
     */
    public function getRentDiscount()
    {
        return $this->rentDiscount;
    }

    /**
     * @param float $rentDiscount
     * @return Order
     */
    public function setRentDiscount($rentDiscount)
    {
        $this->rentDiscount = $rentDiscount;
        return $this;
    }

    /**
     * @return float
     */
    public function getPetrolCost()
    {
        return $this->petrolCost;
    }

    /**
     * @param float $petrolCost
     * @return Order
     */
    public function setPetrolCost($petrolCost)
    {
        $this->petrolCost = $petrolCost;
        return $this;
    }

    /**
     * @return float
     */
    public function getDamageAmount()
    {
        return $this->damageAmount;
    }

    /**
     * @param float $damageAmount
     * @return Order
     */
    public function setDamageAmount($damageAmount)
    {
        $this->damageAmount = $damageAmount;
        return $this;
    }

    /**
     * @return string
     */
    public function getDamage()
    {
        return $this->damage;
    }

    /**
     * @param string $damage
     * @return Order
     */
    public function setDamage($damage)
    {
        $this->damage = $damage;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethodDamage()
    {
        return $this->paymentMethodDamage;
    }

    /**
     * @param string $paymentMethodDamage
     * @return Order
     */
    public function setPaymentMethodDamage($paymentMethodDamage)
    {
        $this->paymentMethodDamage = $paymentMethodDamage;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethodRent()
    {
        return $this->paymentMethodRent;
    }

    /**
     * @param string $paymentMethodRent
     * @return Order
     */
    public function setPaymentMethodRent($paymentMethodRent)
    {
        $this->paymentMethodRent = $paymentMethodRent;
        return $this;
    }

    /**
     * @return float
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * @param float $deposit
     * @return Order
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;
        return $this;
    }

    /**
     * @return float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @param float $commission
     * @return Order
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommissionPaidTo()
    {
        return $this->commissionPaidTo;
    }

    /**
     * @param string $commissionPaidTo
     * @return Order
     */
    public function setCommissionPaidTo($commissionPaidTo)
    {
        $this->commissionPaidTo = $commissionPaidTo;
        return $this;
    }

    /**
     * @return float
     */
    public function getKickback()
    {
        return $this->kickback;
    }

    /**
     * @param float $kickback
     * @return Order
     */
    public function setKickback($kickback)
    {
        $this->kickback = $kickback;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethodDeposit()
    {
        return $this->paymentMethodDeposit;
    }

    /**
     * @param string $paymentMethodDeposit
     * @return Order
     */
    public function setPaymentMethodDeposit($paymentMethodDeposit)
    {
        $this->paymentMethodDeposit = $paymentMethodDeposit;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfPeople()
    {
        return $this->numberOfPeople;
    }

    /**
     * @param int $numberOfPeople
     * @return Order
     */
    public function setNumberOfPeople($numberOfPeople)
    {
        $this->numberOfPeople = $numberOfPeople;
        return $this;
    }

    /**
     * @return int
     */
    public function getCancellation()
    {
        return $this->cancellation;
    }

    /**
     * @param int $cancellation
     * @return Order
     */
    public function setCancellation($cancellation)
    {
        $this->cancellation = $cancellation;
        return $this;
    }

    /**
     * @return string
     */
    public function getCancellationName()
    {
        return OrderDataProvider::cancellationReasonName($this->cancellation);
    }

    /**
     * @return string
     */
    public function getBookedBy()
    {
        return $this->bookedBy;
    }

    /**
     * @param string $bookedBy
     * @return Order
     */
    public function setBookedBy($bookedBy)
    {
        $this->bookedBy = $bookedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     * @return Order
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     * @return Order
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return Order
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return Boat
     */
    public function getBoat()
    {
        return $this->boat;
    }

    /**
     * @param Boat $boat
     * @return Order
     */
    public function setBoat($boat)
    {
        $this->boat = $boat;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     * @return Order
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return User
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param User $handler
     * @return Order
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Returns the length of the booking in hours rounded up.
     * @return int|null
     */
    public function getHours()
    {
        // Ensure that a duration can be calculated.
        if ($this->start === null || $this->end === null) {
            return null;
        }

        $diff = $this->start->diff($this->end, true);

        return $diff->h;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateStaticData()
    {
        $this->customerData = json_encode([
            'first_name'    => $this->customer->getFirstName(),
            'last_name'     => $this->customer->getLastName(),
            'phone_number'  => $this->customer->getPhoneNumber(),
            'email'         => $this->customer->getEmail(),
            'language'      => $this->customer->getLanguage()
        ]);

        $this->locationData = json_encode([
            'name'          => $this->location->getName()
        ]);

        $this->boatData = json_encode([
            'name'          => $this->boat->getName()
        ]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 14.04.18
 * Time: 15:05
 */

namespace AppBundle\Model\DTO;

class Order implements DTOInterface
{
    public $id;

    public $type;

    public $date;

    public $start;

    public $end;

    public $numberOfPeople;

    public $bookedBy;

    public $extra;

    public $comments;

    public $customerId;

    public $boatId;

    // rent fields
    public $rent;

    public $rentDiscount;

    public $paymentMethodRent;

    // petrol
    public $petrolCost;

    // deposit
    public $deposit;

    public $paymentMethodDeposit;

    /**
     * @param \AppBundle\Entity\Order $entity
     */
    public function fromEntity($entity)
    {
        $this->id = $entity->getId();
        $this->type = $entity->getType();
        $this->date = $entity->getDate()->format('Y-m-d');

        $start = $entity->getStart();
        if ($start !== null) {
            $this->start = $start->format('c');
        }

        $end = $entity->getEnd();
        if ($end !== null) {
            $this->end = $end->format('c');
        }

        $this->numberOfPeople = $entity->getNumberOfPeople();
        $this->bookedBy = $entity->getBookedBy();
        $this->extra = $entity->getExtra();
        $this->comments = $entity->getComments();

        $customer = $entity->getCustomer();
        if ($customer !== null) {
            $this->customerId = $customer->getId();
        }

        $boat = $entity->getBoat();
        if ($boat !== null) {
            $this->boatId = $boat->getId();
        }

        $this->rent = $entity->getRent();
        $this->rentDiscount = $entity->getRentDiscount();
        $this->paymentMethodRent = $entity->getPaymentMethodRent();

        $this->petrolCost = $entity->getPetrolCost();

        $this->deposit = $entity->getDeposit();
        $this->paymentMethodDeposit = $entity->getPaymentMethodDeposit();
    }

    public function fromJson($json)
    {
        // TODO: Implement fromJson() method.
    }

    public function isValid()
    {
        return true;
    }
}
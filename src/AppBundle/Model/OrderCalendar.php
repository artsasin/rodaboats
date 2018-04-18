<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 18.04.18
 * Time: 15:34
 */

namespace AppBundle\Model;

use AppBundle\Entity\Boat;
use AppBundle\Entity\Order;

class OrderCalendar
{
    /**
     * @var Boat[]
     */
    protected $boats;

    /**
     * Start hour of displayed calendar.
     * @var integer
     */
    protected $start;

    /**
     * End hour of displayed calendar.
     * @var integer
     */
    protected $end;

    /**
     * List of the time slots to render.
     * @var OrderCalendarSlot[]
     */
    public $slots;

    public function __construct()
    {
        $this->boats = [];
    }

    /**
     * @param integer $start
     * @param integer $end
     */
    public function populateSlots($start, $end)
    {
        $this->slots = array();
        $this->start = $start;
        $this->end = $end;

        for($minutes = $start * 60; $minutes <= $end * 60; $minutes = $minutes + 15)
        {
            $slot = new OrderCalendarSlot($minutes);
            $this->slots[$minutes] = $slot;
        }
    }

    /**
     * @return Boat[]
     */
    public function getBoats()
    {
        return $this->boats;
    }

    /**
     * @param Boat $boat
     */
    public function addBoat(Boat $boat)
    {
        $this->boats[] = $boat;
    }

    /**
     * Retrieves the order for the passed boat for the passed slot. Returns null if there is no order
     * for the boat in the passed slot.
     * @param Boat $boat
     * @param OrderCalendarSlot $slot
     *
     * @return Order
     */
    public function getOrder(Boat $boat, OrderCalendarSlot $slot)
    {
        $orders = $slot->getOrders();
        if(!array_key_exists($boat->getId(), $orders)) {
            return null;
        }


        $order = $orders[$boat->getId()];
        return $order;
    }

    /**
     * @param Boat $boat
     * @param OrderCalendarSlot $slot
     * @return bool
     */
    public function busySlot(Boat $boat, OrderCalendarSlot $slot)
    {
        $startSlot = null;
        foreach ($this->slots as $item) {
            if ($item->getCalendarIndex() > $slot->getCalendarIndex()) {
                break;
            }
            $b = $this->getOrder($boat, $item);

            if ($b && $b->getBoat() === $boat) {
                $startSlot = $item;
            }
        }

        if ($startSlot === null) {
            return false;
        }

        $order = $this->getOrder($boat, $startSlot);

        return ($slot->getCalendarIndex() >= $order->getStartCalendarIndex() && $slot->getCalendarIndex() < $order->getEndCalendarIndex());
    }

    /**
     * @param Order $order
     */
    public function addOrder(Order $order)
    {
        $slot = $this->slots[$order->getStartCalendarIndex($this->start)];
        if ($slot instanceof OrderCalendarSlot) {
            $slot->addOrder($order);
        }
    }
}

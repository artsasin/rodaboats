<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 18.04.18
 * Time: 15:35
 */

namespace AppBundle\Model;

use AppBundle\Entity\Order;

class OrderCalendarSlot
{
    /**
     * @var Order[]
     */
    protected $orders;

    /**
     * @var integer
     */
    public $hour;

    /**
     * @var integer
     */
    public $minutes;

    /**
     * OrderCalendarSlot constructor.
     * @param integer $minutes
     */
    public function __construct($minutes)
    {
        $this->minutes = $minutes;
        $this->orders = [];
    }

    /**
     * @return Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function addOrder(Order $order)
    {
        $this->orders[$order->getBoat()->getId()] = $order;
    }

    /**
     * @return int
     */
    public function getCalendarIndex()
    {
        return intval($this->minutes);
    }

    /**
     * @return string
     */
    public function format()
    {
        $minutes = $this->minutes % 60;
        $hours = intval(($this->minutes - $minutes) / 60);

        return sprintf('%s:%s', $hours, ($minutes === 0) ? '0' . $minutes : $minutes);
    }
}
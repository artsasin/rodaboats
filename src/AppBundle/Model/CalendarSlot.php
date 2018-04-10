<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 06.04.18
 * Time: 23:05
 */

namespace AppBundle\Model;

use AppBundle\Entity\Booking;

class CalendarSlot
{
    /**
     * Key-value map mapping boat-id to booking for this slot.
     * @var Booking[]
     */
    protected $bookings;

    /**
     * @var integer
     */
    public $hour;

    /**
     * @var integer
     */
    public $minutes;

    /**
     * CalendarSlot constructor.
     * @param integer $hour
     * @param integer $minutes
     */
    public function __construct($hour, $minutes)
    {
        $this->hour = $hour;
        $this->minutes = $minutes;
        $this->bookings = array();
    }

    /**
     * @return Booking[]
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * @param Booking $booking
     */
    public function addBooking(Booking $booking)
    {
        $boatId = $booking->getBoat()->getId();
        $this->bookings[$boatId] = $booking;
    }

    /**
     * @return int
     */
    public function getCalendarIndex()
    {
        return intval(($this->hour * 60) + $this->minutes);
    }
}

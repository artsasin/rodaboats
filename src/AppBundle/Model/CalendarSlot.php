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
    public function __construct($minutes)
    {
//        $this->hour = $hour;
        $this->minutes = $minutes;
        $this->bookings = array();
    }

    public function format()
    {
        $minutes = $this->minutes % 60;
        $hours = intval(($this->minutes - $minutes) / 60);

        return sprintf('%s:%s', $hours, ($minutes === 0) ? '0' . $minutes : $minutes);
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
        return intval($this->minutes);
    }
}

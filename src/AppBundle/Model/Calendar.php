<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 06.04.18
 * Time: 23:03
 */

namespace AppBundle\Model;

use AppBundle\Entity\Boat;
use AppBundle\Entity\Booking;

class Calendar
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
     * @var CalendarSlot[]
     */
    public $slots;

    /**
     * Calendar constructor.
     */
    public function __construct()
    {
        $this->boats = array();
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

        for($hour = $start; $hour <= $end; $hour++)
        {

            $slot = new CalendarSlot($hour);
            $this->slots[$hour] = $slot;
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
     * Retrieves the booking for the passed boat for the passed slot. Returns null if there is no booking
     * for the boat in the passed slot.
     * @param Boat $boat
     * @param CalendarSlot $slot
     *
     * @return Booking
     */
    public function getBooking(Boat $boat, CalendarSlot $slot)
    {
        $bookings = $slot->getBookings();
        if(!array_key_exists($boat->getId(), $bookings))
            return null;


        $booking = $bookings[$boat->getId()];
        return $booking;
    }

    /**
     * @param Booking $booking
     */
    public function addBooking(Booking $booking)
    {
        // Find the slot corresponding to this booking. Ensure that the calendar cap is honoured.
        $start = $booking->getStart();
        $hour = intval($start->format('G'));
        $hour = $hour < $this->start ? $this->start : $hour;

        $slot = $this->slots[$hour];
        $slot->addBooking($booking);
    }
}

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
            $index = $hour * 60;
            $slot = new CalendarSlot($hour, 0);
            $this->slots[$index] = $slot;
            if ($hour !== $end) {
                $slot = new CalendarSlot($hour, 30);
                $index = $index + 30;
                $this->slots[$index] = $slot;
            }
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
     * @param Boat $boat
     * @param CalendarSlot $slot
     * @return bool
     */
    public function busySlot(Boat $boat, CalendarSlot $slot)
    {
        $startSlot = null;
        foreach ($this->slots as $item) {
            if ($item->getCalendarIndex() > $slot->getCalendarIndex()) {
                break;
            }
            $b = $this->getBooking($boat, $item);

            if ($b && $b->getBoat() === $boat) {
                $startSlot = $item;
            }
        }

        if ($slot->hour == 17 && $slot->minutes == 0 && $boat->getId() == 1) {
            dump($startSlot);
        }

        if ($startSlot === null) {
            return false;
        }

        $booking = $this->getBooking($boat, $startSlot);

        $hour = $booking->getStart()->format('G');
        $minute = intval($booking->getStart()->format('i'));
        $startIndex = intval($hour * 60);
        if ($minute > 30) {
            $startIndex = intval($startIndex + 30);
        }

        $hour = $booking->getEnd()->format('G');
        $minute = intval($booking->getEnd()->format('i'));
        $endIndex = intval($hour * 60);
        if ($minute > 30) {
            $endIndex = intval($endIndex + 30);
        }

        $slotIndex = ($slot->hour * 60) + $slot->minutes;

        return ($slot->getCalendarIndex() >= $booking->getStartCalendarIndex() && $slot->getCalendarIndex() < $booking->getEndCalendarIndex());
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
        $minute = intval($start->format('i'));
        $index = intval($hour * 60);
        if ($minute >= 30) {
            $index = intval($index + 30);
        }

        $slot = $this->slots[$index];
        $slot->addBooking($booking);
    }
}

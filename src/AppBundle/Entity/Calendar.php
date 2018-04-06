<?php

namespace AppBundle\Entity;

/**
 * Reflects the calendar dashboard showing todays reservations and all the active boats.
 * @author chris
 *
 */
class Calendar 
{
	
	public function __construct()
	{
		
		$this->boats = array();
	}
	
	protected $boats;
	
	/**
	 * Start hour of displayed calendar.
	 * @var unknown
	 */
	protected $start;
	
	/**
	 * End hour of displayed calendar.
	 * @var unknown
	 */
	protected $end;
	
	public function getBoats()
	{
		return $this->boats;
	}
	
	/**
	 * List of the time slots to render.
	 * @var unknown
	 */
	public $slots;
	
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
	
	public function addBoat($boat)
	{
		$this->boats[] = $boat;
	}
	
	/**
	 * Retrieves the booking for the passed boat for the passed slot. Returns null if there is no booking
	 * for the boat in the passed slot.
	 * @param unknown $boat
	 * @param unknown $slot
	 */
	public function getBooking($boat, $slot)
	{
		$bookings = $slot->getBookings();
		if(!array_key_exists($boat->getId(), $bookings))
			return null;
		
		
		$booking = $bookings[$boat->getId()];
		return $booking;
	}
	
	public function addBooking($booking)
	{
		
		// Find the slot corresponding to this booking. Ensure that the calendar cap is honoured.
		$start = $booking->getStart();
		$hour = intval($start->format('G'));
		$hour = $hour < $this->start ? $this->start : $hour;
		
		$slot = $this->slots[$hour];
		$slot->addBooking($booking);
	}
}

class CalendarSlot
{
	
	public function __construct($hour)
	{
		
		$this->hour = $hour;
		$this->bookings = array();
	}
	
	public $hour;
	
	/**
	 * Key-value map mapping boat-id to booking for this slot.
	 * @var unknown
	 */
	protected $bookings;
	
	public function getBookings()
	{
		return $this->bookings;
	}
	
	public function addBooking($booking)
	{
		$boatId = $booking->getBoat()->getId();
		$this->bookings[$boatId] = $booking;
	}
}
<?php

namespace AppBundle\Controller;

use AppBundle\Model\Calendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Boat;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Location;
use \DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="calendar")
     */
    public function indexAction(Request $request)
    {
    	
    	// Default to today.
    	return $this->calendarAction($request, new DateTime());
    }

    /**
     * @Route("/calendar/{date}", name="calendardate")
     */
    public function calendarAction(Request $request, $date)
    {
    	
    	if(!($date instanceof DateTime))
    		$date = new DateTime($date);
    	
    	// Load all active boats.
    	// Check if a location filter needs to be applied.
    	$location = $request->query->get('location');
    	if(!empty($location))
    	{
    		$location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($location);
    		$boats = $this->getDoctrine()->getRepository('AppBundle:Boat')->findBy(array(
    				'status' => Boat::STATUS_ACTIVE,
    				'location' => $location
    		));
    	} else 
    	{
    		$boats = $this->getDoctrine()->getRepository('AppBundle:Boat')->findBy(array(
    				'status' => Boat::STATUS_ACTIVE));
    	}
    	
    	$nextDate = clone $date;
    	$nextDate = $nextDate->modify('+1 day');
    	$prevDate = clone $date;
    	$prevDate = $prevDate->modify('-1 day');
    	
    	// Build the calendar
    	$calendar = new Calendar();
    	$calendar->populateSlots(5, 22);
    	 
    	
    	
    	foreach($boats as $boat)
    		$calendar->addBoat($boat);
    	 
    	$bookings = $this->getDoctrine()->getRepository('AppBundle:Booking')->findBy(array(
    			'date' => $date,
    			'status' => array(Booking::STATUS_CONFIRMED, Booking::STATUS_CLOSED, Booking::STATUS_DELIVERED)
    			));
    	foreach($bookings as $booking)
    		$calendar->addBooking($booking);
    	
    	// Look up active locations
    	$locations = $this->getDoctrine()->getRepository('AppBundle:Location')->findBy(array(
    			'status' => Location::STATUS_ACTIVE
    	));
    	 
    	// replace this example code with whatever you need
    	return $this->render('default/index.html.twig', array(
    			'calendar' => $calendar,
    			'date' => $date,
    			'locations' => $locations,
    			'next_date' => $nextDate->format('Y-m-d'),
    			'prev_date' => $prevDate->format('Y-m-d'),
    	));
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\Boat;
use AppBundle\Entity\User;
use AppBundle\Entity\Location;
use AppBundle\Entity\Booking;
use Sabre\VObject;

class ApiController extends Controller
{
    /**
     * @Route("/api/location/{id}/calendar/{key}", name="apilocationcalendar")
     */
    public function calendarAction(Request $request, $id, $key)
    {
    	
    	$user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('apiKey' => $key));
    	if($user === null)
    		throw new Exception("Invalid API key");
    	
    	$repo = $this->getDoctrine()->getRepository('AppBundle:Booking');
    	$builder = $repo->createQueryBuilder('b');
    	
    	// Setup date range
    	$startDate = new \DateTime();
    	$startDate->modify('-1 week');
    	$endDate = new \DateTime();
    	$endDate = $endDate->modify('+4 week');
    	$builder->andWhere($builder->expr()->andX(
				$builder->expr()->gte("b.date", ":start"),
				$builder->expr()->lte("b.date", ":end")
		))
    	->setParameter("start", $startDate)
    	->setParameter("end", $endDate);
    	
    	// Only match non cancelled bookings.
    	$builder->andWhere($builder->expr()->in('b.status', array(
    			Booking::STATUS_CLOSED,
    			Booking::STATUS_CONFIRMED,
    			Booking::STATUS_DELIVERED
    	)));
    	
    	// Only match location
    	$builder->andWhere($builder->expr()->eq('b.location', ':location'))
    	->setParameter("location", $id);
    	
    	
    	
    	// Builder calendar
    	$calendar = new VObject\Component\VCalendar();
    	
    	// Fetch bookings
    	$bookings = $builder->getQuery()->getResult();
    	foreach($bookings as $booking)
    	{
    		
    		// Why does the PHP DateTime class have to be suck a bitch?
    		$start = $booking->getDate()->format('Y-m-d') . ' ' . $booking->getStart()->format('H:i:s');
    		$start = new \DateTime($start);
    		$end = $booking->getDate()->format('Y-m-d') . ' ' . $booking->getEnd()->format('H:i:s');
    		$end = new \DateTime($end);
    		
    		$url = $this->generateUrl('viewbooking', array('id' => $booking->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
    		
	    	$calendar->add('VEVENT', [
	    			'SUMMARY' => $booking->getLesseeLastName(),
	    			'DESCRIPTION' => $booking->getBoat()->getName(), 
	    			'LOCATION' => $booking->getLocation()->getName(),
	    			'DTSTART' => $start,
	    			'DTEND' => $end,
	    			'URL' => $url
	    	]);
    	}
    	
    	$content = $calendar->serialize();
    	
    	$response = new Response($content);
    	$response->headers->set('Content-Type', 'text/calendar');
    	return $response;
    }
    
}

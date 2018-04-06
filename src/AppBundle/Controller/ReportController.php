<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Entity\Boat;
use AppBundle\Entity\Booking;
use AppBundle\Entity\User;
use AppBundle\Entity\Location;
use AppBundle\Entity\Report;

class ReportController extends Controller
{
	
	protected function buildForm($report)
	{
	
	
		$form = $this->createFormBuilder($report)
	
		->add('boat', 'entity', array(
				'class' => 'AppBundle:Boat',
				'choice_label' => 'name',
				'required' => false,
		))
		->add('start', 'date')
		->add('end', 'date')
		->add('location', 'entity', array(
				'class' => 'AppBundle:Location',
				'choice_label' => 'name',
				'required' => false,
		))
		
		->add('output', 'choice', array(
				'choices' => array(
						'HTML' => 'HTML',
						'Excel XML' => 'XML',
				),
				'choices_as_values' => true
		))
		
		->add('status', 'choice', array(
				'choices' => Booking::statuses(),
				'required' => false,
		))
		
		->add('cancellationReason', 'choice', array(
				'choices' => Booking::cancellationReasons(),
				'required' => false,
		))
		
		
	
		->add('save', 'submit', array('label' => 'Execute'))
		->getForm();
	
		return $form;
	}
	
	protected function buildDailyForm()
	{
	
		$default = array(
				'date' => new \DateTime(date('Y-m-d')),
				'location' => null,
				'print' => false
		);
		
		$form = $this->createFormBuilder($default)
	
		->add('date', 'date')
		->add('location', 'entity', array(
				'class' => 'AppBundle:Location',
				'choice_label' => 'name',
				'required' => false,
		))
		->add('paymentMethod', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'required' => false,
				'choices_as_values' => true,
		))
		->add('print', 'checkbox', array(
				'required' => false
		))
	
		->add('save', 'submit', array('label' => 'Execute'))
		->getForm();
	
		return $form;
	}
	
	protected function buildMonthForm()
	{
	
		$default = array(
				'date' => new \DateTime(date('Y-m-d')),
				'location' => null,
				'print' => false
		);
		
		$form = $this->createFormBuilder($default)
	
		->add('date', 'date', array(
				'days' => array(1),
				'label' => 'Month'
		))
		->add('location', 'entity', array(
				'class' => 'AppBundle:Location',
				'choice_label' => 'name',
				'required' => false,
		))
		->add('paymentMethod', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'required' => false,
				'choices_as_values' => true,
		))
		->add('print', 'checkbox', array(
				'required' => false
		))
	
		->add('save', 'submit', array('label' => 'Execute'))
		->getForm();
	
		return $form;
	}
	
    /**
     * @Route("/report", name="report")
     */
    public function indexAction(Request $request)
    {
    	
    	$report = new Report();
    	$report->start = new \DateTime();
    	$report->start = $report->start->modify('first day of this month');
    	$report->end = new \DateTime();
    	$report->end = $report->end->modify('last day of this month');
    	$form = $this->buildForm($report);
    	
    	$form->handleRequest($request);
    	if($form->isValid())
    	{
    		
    		$repository = $this->getDoctrine()->getRepository('AppBundle:Booking');
    		$builder = $repository->createQueryBuilder('booking');
    		
    		// Constrain by date range.
    		$builder = $builder->andWhere('booking.date >= :start')
    		->andWhere('booking.date <= :end')
    		->setParameter('start', $report->start)
    		->setParameter('end', $report->end);
    		
    		// Constrain by boat if desired.
    		if($report->boat != null)
    		{
    			$builder = $builder->andWhere('booking.boat = :boat')
    			->setParameter('boat', $report->boat);
    		}
    		
    		// Constrain by location if desired.
    		if($report->location != null)
    		{
    			$builder = $builder->andWhere('booking.location = :location')
    			->setParameter('location', $report->location);
    		}
    		
    		// Constrain by status if desired.
    		if($report->status != null)
    		{
    			$builder = $builder->andWhere('booking.status = :status')
    			->setParameter('status', $report->status);
    		}
    		
    		// Optionally apply the payment filter.
    		if($report->status == Booking::STATUS_CANCELLED && !empty($report->cancellationReason))
    		{
    			$builder->andWhere('booking.cancellation = :cancellationReason')
    			->setParameter('cancellationReason', $report->cancellationReason);
    		}
    		
    		// Execute the query.
    		$query = $builder->getQuery();
    		$results = $query->getResult();
    		
    		
    	} else {
    		$results = null;
    	}
    	
    	switch($report->output)
    	{
    		default:
    		case 'HTML':
    			
    			return $this->render('report/index.html.twig', array(
    					'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
    					'form' => $form->createView(),
    					'output' => $results,
    			));
    			
    			break;
    		case 'XML':
    			
    			$response = $this->render('report/report.xml.twig', array(
    					'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
    					'output' => $results,
    			));
    			
    			$response->headers->set('Content-Type', 'application/xml; charset=utf-8');
    			$response->headers->set('Content-Disposition', 'attachment;filename=report.xml');
    			return $response;
    			
    			break;
    	}
        
    }
    
    public function csvOutput($result)
    {


    	// get the service container to pass to the closure
    	$container = $this->container;
    	$response = new StreamedResponse(function() use($container, $result) {
    	
    		
    		$handle = fopen('php://output', 'r+');
    	
    		foreach ($result as $booking) 
    		{
    			// add a line in the csv file. You need to implement a toArray() method
    			// to transform your object into an array
    			$row = [
    					$booking->getId(),
    					
    			];
    			fputcsv($handle, $row);
    		}
    	
    		fclose($handle);
    	});
    	
    	$response->headers->set('Content-Type', 'application/force-download');
    	$response->headers->set('Content-Disposition','attachment; filename="export.csv"');
    	
    	return $response;
    }
    
    /**
     * @Route("/report/daily", name="reportdaily")
     */
    public function dailyReport(Request $request)
    {
    	
    	// Build the form.
    	$form = $this->buildDailyForm();
    	$form->handleRequest($request);
    	$data = $form->getData();
    	
    	// Retrieve all bookings for this date.
    	$repository = $this->getDoctrine()->getRepository('AppBundle:Booking');
    	$builder = $repository->createQueryBuilder('booking')
    	->andWhere('booking.date = :date')
    	->setParameter('date', $data['date'])
    	->orderBy('booking.date', 'ASC');
    	
    	// Optionally apply the location filter.
    	if(!empty($data['location']))
    	{
    		$builder->andWhere('booking.location = :location')
    		->setParameter('location', $data['location']);
    	}
    	
    	// Optionally apply the payment filter.
    	if(!empty($data['paymentMethod']))
    	{
    		$builder->andWhere('booking.paymentMethodRent = :paymentMethod OR booking.paymentMethodDamage = :paymentMethod')
    		->setParameter('paymentMethod', $data['paymentMethod']);
    	}
    	
    	// Only select confirmed bookings
    	$builder->andWhere($builder->expr()->in('booking.status', array(
    			Booking::STATUS_CONFIRMED,
    			Booking::STATUS_DELIVERED,
    			Booking::STATUS_CLOSED
    	)));
    	
    	$bookings = $builder->getQuery()->getResult();
    	
    	$total = array(
    			'rentCash' => 0,
    			'rentCard' => 0,
    			'petrolCash' => 0,
    			'petrolCard' => 0,
    			'damageCash' => 0,
    			'damageCard' => 0,
    			'commission' => 0,
    			'kickback' => 0
    	);
    	
    	foreach($bookings as $booking)
    	{
    		if($booking->getPaymentMethodRent() == 'Cash')
    		{
    			$total['rentCash'] += $booking->getRent();
    			$total['petrolCash'] += $booking->getPetrolCost();
    		} else {
    			$total['rentCard'] += $booking->getRent();
    			$total['petrolCard'] += $booking->getPetrolCost();
    		}
    		
    		switch($booking->getPaymentMethodDamage())
    		{
    			case 'Cash':
    				$total['damageCash'] += $booking->getDamageAmount();
    				break;
    			case 'CreditCard':
    			case 'DebitCard':
    				$total['damageCard'] += $booking->getDamageAmount();
    				break;
    		}
    		
    		$total['commission'] += $booking->getCommission();
    		$total['kickback'] += $booking->getKickBack();
    	}
    	
    	if($data['print'])
    		$view = 'report/dailyprint.html.twig';
    	else 
    		$view = 'report/daily.html.twig';
    	
    	return $this->render($view, array(
    			'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
    			'bookings' => $bookings,
    			'total' => $total,
    			'form' => $form->createView(),
    			'date' => $data['date'],
    			'location' => $data['location']
    	));
    }
    
    /**
     * @Route("/report/month", name="reportmonth")
     */
    public function monthReport(Request $request)
    {
    	 
    	// Build the form.
    	$form = $this->buildMonthForm();
    	$form->handleRequest($request);
    	$data = $form->getData();
    	
    	$startDate = new \DateTime('first day of '.$data['date']->format('F-Y'));
    	$endDate = new \DateTime('last day of '.$data['date']->format('F-Y'));
    	
    	// Retrieve all bookings for this date.
    	$repository = $this->getDoctrine()->getRepository('AppBundle:Booking');
    	$builder = $repository->createQueryBuilder('booking')
    	->andWhere('booking.date >= :date_start AND booking.date <= :date_end')
    	->setParameter('date_start', $startDate)
    	->setParameter('date_end', $endDate)
    	->orderBy('booking.date', 'ASC');
    	 
    	// Optionally apply the location filter.
    	if(!empty($data['location']))
    	{
    		$builder->andWhere('booking.location = :location')
    		->setParameter('location', $data['location']);
    	}
    	
    	// Optionally apply the payment filter.
    	if(!empty($data['paymentMethod']))
    	{
    		$builder->andWhere('booking.paymentMethodRent = :paymentMethod OR booking.paymentMethodDamage = :paymentMethod')
    		->setParameter('paymentMethod', $data['paymentMethod']);
    	}
    	 
    	// Only select confirmed bookings
    	$builder->andWhere($builder->expr()->in('booking.status', array(
    			Booking::STATUS_CONFIRMED,
    			Booking::STATUS_DELIVERED,
    			Booking::STATUS_CLOSED
    	)));
    	 
    	$bookings = $builder->getQuery()->getResult();
    	 
    	$total = array(
    			'rentCash' => 0,
    			'rentCard' => 0,
    			'petrolCash' => 0,
    			'petrolCard' => 0,
    			'damageCash' => 0,
    			'damageCard' => 0,
    			'commission' => 0,
    			'kickback' => 0
    	);
    	 
    	foreach($bookings as $booking)
    	{
    		if($booking->getPaymentMethodRent() == 'Cash')
    		{
    			$total['rentCash'] += $booking->getRent();
    			$total['petrolCash'] += $booking->getPetrolCost();
    		} else {
    			$total['rentCard'] += $booking->getRent();
    			$total['petrolCard'] += $booking->getPetrolCost();
    		}
    
    		switch($booking->getPaymentMethodDamage())
    		{
    			case 'Cash':
    				$total['damageCash'] += $booking->getDamageAmount();
    				break;
    			case 'CreditCard':
    			case 'DebitCard':
    				$total['damageCard'] += $booking->getDamageAmount();
    				break;
    		}
    
    		$total['commission'] += $booking->getCommission();
    		$total['kickback'] += $booking->getKickBack();
    	}
    	 
    	if($data['print'])
    		$view = 'report/monthprint.html.twig';
    	else
    		$view = 'report/month.html.twig';
    	 
    	return $this->render($view, array(
    			'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
    			'bookings' => $bookings,
    			'total' => $total,
    			'form' => $form->createView(),
    			'date' => $data['date'],
    			'location' => $data['location']
    	));
    }
    
    public function dailyReportOld()
    {
    	 
    	$formData = array(
    			'date' => new \DateTime(date('Y-m-d')),
    			'location' => null
    	);
    	$form = $this->buildDailyForm($formData);
    	 
    	$date = new \DateTime(date('Y-m-d'));
    	 
    	// Retrieve all bookings for this date.
    	$repository = $this->getDoctrine()->getRepository('AppBundle:Booking');
    	$query = $repository->createQueryBuilder('booking')
    	->andWhere('booking.date = :date')
    	->setParameter('date', $date)
    	->getQuery();
    	$bookings = $query->getResult();
    	 
    	$confirmedBookings = array_filter($bookings, function(Booking $booking) {
    		return $booking->getStatus() != Booking::STATUS_CANCELLED;
    	});
    	$cancelledBookings = array_filter($bookings, function(Booking $booking) {
    		return $booking->getStatus() == Booking::STATUS_CANCELLED;
    	});
    		 
    		// Sum turnover per payment method
    		$turnoverPerMethod = array('CreditCard' => 0,
    				'DebitCard' => 0,
    				'Cash' => 0);
    		$totalTurnover = 0;
    		foreach($confirmedBookings as $booking)
    		{
    			$turnover = $booking->getRent() + $booking->getPetrolCost() - $booking->getRentDiscount();
    			$totalTurnover += $turnover;
    			$turnoverPerMethod[$booking->getPaymentMethodRent()] += $turnover;
    		}
    		 
    		// Earnings per location
    		$turnoverPerLocation = array();
    		foreach($confirmedBookings as $booking)
    		{
    
    			$location = $booking->getLocation();
    			if($location == null)
    				continue;
    
    			if(!array_key_exists($location->getId(), $turnoverPerLocation))
    			{
    				$turnoverPerLocation[$location->getId()] = array('Name' => $location->getName(),
    						'CreditCard' => 0,
    						'DebitCard' => 0,
    						'Cash' => 0
    				);
    			}
    
    			$turnover = $booking->getRent() + $booking->getPetrolCost() - $booking->getRentDiscount();
    			$turnoverPerLocation[$location->getId()][$booking->getPaymentMethodRent()] += $turnover;
    
    		}
    		 
    		 
    		 
    		 
    		//     	$conn = $this->get('database_connection');
    		//     	$stm = $conn->prepare("SELECT SUM(bookings.* FROM bookings WHERE ")
    		//     	$stm->
    		//         $users = $conn->fetchAll('SELECT * FROM users');
    
    		return $this->render('report/daily.html.twig', array(
    				'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
    				'bookings' => $bookings,
    				'confirmedBookings' => $confirmedBookings,
    				'cancelledBookings' => $cancelledBookings,
    				'turnoverPerMethod' => $turnoverPerMethod,
    				'totalTurnover' => $totalTurnover,
    				'turnoverPerLocation' => $turnoverPerLocation,
    				'form' => $form->createView()
    		));
    }
}

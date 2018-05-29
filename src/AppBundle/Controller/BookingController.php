<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Boat;
use AppBundle\Entity\Booking;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BookingController extends Controller
{

	protected function buildCreateForm($boat)
	{
		

		$form = $this->createFormBuilder($boat)
		
		->add('boat', 'entity', array(
			'class' => 'AppBundle:Boat',
			'choice_label' => 'name',
		))
		->add('type', 'choice', array(
				'choices' => Booking::types(),
		))
		->add('date', 'date')
		->add('start', 'time')
		->add('end', 'time')
		
		->add('bookedBy', 'text', array('required' => false))
		
		->add('lesseeFirstName', 'text', array('label' => 'First name'))
		->add('lesseeLastName', 'text', array('label' => 'Last name'))
		->add('lesseePhone', 'text')
		->add('lesseeEmail', 'email', array('required' => false))
		->add('lesseeIdentityNumber', 'text', array('label' => 'Lessee ID nr', 'required' => false))
		
		->add('language', 'choice', array(
				'choices' => array(
						'German' => 'DE',
						'English' => 'EN',
						'Spanish' => 'ES',
				),
				'choices_as_values' => true
		))
		->add('numberOfPeople', 'integer')
		
		->add('comments', 'textarea', array('required' => false))
		
		
		
		->add('rent', 'money')
		->add('rentDiscount', 'money')
		->add('petrolCost', 'money')
		->add('deposit', 'money')
		
		->add('paymentMethodRent', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'choices_as_values' => true
		))
		->add('paymentMethodDeposit', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'choices_as_values' => true
		))
		
		
		->add('save', 'submit', array('label' => 'Create booking'))
		->getForm();
		
		return $form;
	}
	
	protected function buildEditForm($boat)
	{
	
	
		$form = $this->createFormBuilder($boat)
	
		->add('boat', 'entity', array(
				'class' => 'AppBundle:Boat',
				'choice_label' => 'name',
		))
		->add('type', 'choice', array(
				'choices' => Booking::types(),
		))
		->add('date', 'date')
		->add('start', 'time', array(
				'attr' => array(
						'class' => ''
				)
		))
		->add('end', 'time')
		->add('location', 'entity', array(
				'class' => 'AppBundle:Location',
				'choice_label' => 'name',
				'required' => false,
		))
		->add('bookedBy', 'text', array('required' => false))
		
		->add('rent', 'money')
		->add('rentDiscount', 'money')
		->add('petrolCost', 'money')
		->add('commission', 'money', array('read_only' => true))
		->add('deposit', 'money')
		
		->add('paymentMethodRent', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'choices_as_values' => true
		))
		->add('paymentMethodDeposit', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'choices_as_values' => true
		))
	
		->add('lesseeFirstName', 'text', array('label' => 'First name'))
		->add('lesseeLastName', 'text', array('label' => 'Last name'))
		->add('lesseePhone', 'text')
		->add('lesseeEmail', 'email', array('required' => false))
		->add('lesseeIdentityNumber', 'text', array('label' => 'Lessee ID nr', 'required' => false))
	
		->add('language', 'choice', array(
				'choices' => array(
						'German' => 'DE',
						'English' => 'EN',
						'Spanish' => 'ES',
				),
				'choices_as_values' => true
		))
		->add('numberOfPeople', 'integer')
		
		->add('damage', 'textarea', array('required' => false))
		->add('damageAmount', 'money', array('label' => 'Damage / correction amount'))
		->add('paymentMethodDamage', 'choice', array(
				'label' => 'Damage / correction payment method',
				'choices' => Booking::paymentMethods(true),
				'choices_as_values' => true
		))
		
		->add('cancellation', 'choice', array(
				'choices' => Booking::cancellationReasons(),
				'read_only' => true
		))
		
		->add('comments', 'textarea', array('required' => false))
	
		->add('save', 'submit', array('label' => 'Save'))
		->getForm();
	
		return $form;
	}
	
	protected function buildAdminEditForm($boat)
	{
	
	
		$form = $this->createFormBuilder($boat)
	
		->add('boat', 'entity', array(
				'class' => 'AppBundle:Boat',
				'choice_label' => 'name',
		))
		->add('type', 'choice', array(
				'choices' => Booking::types(),
		))
		->add('status', 'choice', array(
				'choices' => Booking::statuses(),
		))
		->add('date', 'date')
		->add('start', 'time', array(
				'attr' => array(
						'class' => ''
				)
		))
		->add('end', 'time')
		->add('location', 'entity', array(
				'class' => 'AppBundle:Location',
				'choice_label' => 'name',
				'required' => false,
		))
		->add('bookedBy', 'text', array('required' => false))
	
		->add('rent', 'money')
		->add('rentDiscount', 'money')
		->add('petrolCost', 'money')
		->add('commission', 'money')
		->add('commissionPaidTo', 'text', array('required' => false))
		->add('deposit', 'money')
	
		->add('paymentMethodRent', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'choices_as_values' => true
		))
		->add('paymentMethodDeposit', 'choice', array(
				'choices' => Booking::paymentMethods(),
				'choices_as_values' => true
		))
	
		->add('lesseeFirstName', 'text', array('label' => 'First name'))
		->add('lesseeLastName', 'text', array('label' => 'Last name'))
		->add('lesseePhone', 'text')
		->add('lesseeEmail', 'email', array('required' => false))
		->add('lesseeIdentityNumber', 'text', array('label' => 'Lessee ID nr', 'required' => false))
	
		->add('language', 'choice', array(
				'choices' => array(
						'German' => 'DE',
						'English' => 'EN',
						'Spanish' => 'ES',
				),
				'choices_as_values' => true
		))
		->add('numberOfPeople', 'integer')
	
		->add('damage', 'textarea', array('required' => false))
		->add('damageAmount', 'money', array('label' => 'Damage / correction amount'))
		->add('paymentMethodDamage', 'choice', array(
				'label' => 'Damage / correction payment method',
				'choices' => Booking::paymentMethods(true),
				'choices_as_values' => true
		))
		
		->add('cancellation', 'choice', array(
				'choices' => Booking::cancellationReasons(),
				'read_only' => true
		))
		
		->add('creator', 'entity', array(
				'class' => 'AppBundle:User',
				'choice_label' => 'Created by',
				'required' => false,
		))
		
		->add('comments', 'textarea', array('required' => false))
	
		->add('save', 'submit', array('label' => 'Save'))
		->getForm();
	
		return $form;
	}
	
	protected function buildCancelForm($boat)
	{
	
	
		$form = $this->createFormBuilder($boat)
		->add('cancellation', 'choice', array(
				'choices' => Booking::cancellationReasons(),
		))
	
		->add('save', 'submit', array('label' => 'Cancel'))
		->getForm();
	
		return $form;
	}
	
	protected function buildCloseForm($boat)
	{
	
	
		$form = $this->createFormBuilder($boat)
		->add('commission', 'money')
		->add('commissionPaidTo', 'text', array('required' => false))
		->add('kickback', 'money')
		
		->add('damage', 'textarea', array('required' => false))
		->add('damageAmount', 'money', array('label' => 'Damage / correction amount'))
		->add('paymentMethodDamage', 'choice', array(
				'label' => 'Damage / correction payment method',
				'choices' => Booking::paymentMethods(true),
				'choices_as_values' => true
		))
	
		->add('save', 'submit', array('label' => 'Close'))
		->getForm();
	
		return $form;
	}

    /**
     * @Route(path="/old-bookings-not-imported", name="app_bookings_old_not_imported")
     * @param Request $request
     * @return Response
     */
	public function oldBookingsNotImportedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Booking::class);
        $qb = $repo->createQueryBuilder('b');
        $qb->where($qb->expr()->eq('b.imported', ':imported'));
        $qb->setParameter('imported', false);
        $qb->orderBy('b.date', 'DESC');
        $page = $request->query->getInt('page', 1);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page,25);

        return $this->render('booking/old_not_imported.html.twig', [
            'pagination' => $pagination
        ]);
    }
	
	/**
	 * @Route("/booking", name="booking")
	 */
	public function indexAction(Request $request)
	{
	    return $this->redirectToRoute('app_order_index');

	    $repo = $this->getDoctrine()->getRepository('AppBundle:Booking');
        $qb = $repo->getPaginationQueryBuilder($request->query->get('filter'));
        $page = $request->query->getInt('page', 1);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page,25);

		return $this->render('booking/index.html.twig', array(
				'pagination' => $pagination
		));
	}
	
	/**
	 * @Route("/booking/edit", name="addbooking")
	 */
	public function addBooking(Request $request)
	{
	    return $this->redirectToRoute('app_order_index');
		return $this->addBookingBoat($request, null, null);
	}
	
	/**
	 * @Route("/booking/edit/{boat}/{date}", name="addbookingspecific")
	 */
	public function addBookingBoat(Request $request, $boat = null, $date = null)
	{
        return $this->redirectToRoute('app_order_index');
		// Build the form around a fresh boat object.
		$booking = new Booking();
		
		// Preset the boat and date if they are passed.
		if($boat != null)
		{
			$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($boat);
			$booking->setBoat($boat);
		}
		if($date != null)
			$date = new \DateTime($date);
		else
			$date = new \DateTime();
		$booking->setDate($date);
		
		// Preset the start time if supplied.
		$time = $request->query->get('time');
		if(empty($time))
			$time = '08:00';
		$booking->setStart(new \DateTime($time));
		
		$form = $this->buildCreateForm($booking);
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
			
			$boat = $booking->getBoat();
			
			// Check for conflicting bookings
			$conflicts = $this->getConflicting($booking);
			if(!empty($conflicts))
			{
				$conflict = $conflicts[0];
				$this->addFlash('danger', 'Unable to book boat due to conflicting booking for: '.$conflict->getLesseeName().' by: '.$conflict->getBookedBy());
				return $this->render('booking/add.html.twig', array(
					'form' => $form->createView(),
				));
			}
			
			// Set the creator of the booking.
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$booking->setHandler($user);
			
			// Copy location from boat.
			$booking->setLocation($boat->getLocation());
			if($boat->getLocation() == null)
				$this->addFlash('warning', 'The selected boat does not have a location. Please enter the location manually.');
	
			// Save the new boat.
			$em = $this->getDoctrine()->getManager();
			$em->persist($booking);
			$em->flush();
	
			// Notify and redirect.
			$this->addFlash('notice', 'The booking has been saved.');
			$this->notifyBooking($booking);
			$this->notifyBookingLessee($booking);
			return $this->redirectToRoute('viewbooking', array('id' => $booking->getId()));
		}
		 
		return $this->render('booking/add.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	protected function notifyBooking(Booking $booking)
	{
		
		// Don't send the notification if the location has no proper notification address.
		$recipients = array();
		$location = $booking->getLocation();
		if($location != null)
		{
			$recipient = $location->getNotificationEmail();
			if(!empty($recipient))
				$recipients[] = $recipient;
		}
		$admin = $this->container->getParameter("admin_email");
		if(!empty($admin))
			$recipients[] = $admin;
		if(empty($recipients))
			return;
		
		// Render the message body.
		$body = $this->renderView('emails/newbooking.html.twig', array(
				'booking' => $booking
		));
		$subject = sprintf("New booking of %s | %s %s - %s | %s", $booking->getBoat()->getName(),
				$booking->getDate()->format('l j M Y'),
				$booking->getStart()->format('G:i'),
				$booking->getEnd()->format('G:i'),
				$booking->getLesseeName());
		
		// Compose and send the message.
		$message = \Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom("info@rodaboats.eu")
		->setTo($recipients)
		->setBody($body, 'text/html')
		;
		$this->get('mailer')->send($message);
	}
	
	protected function notifyBookingCancellation(Booking $booking)
	{
	
		// Do not send cancellation notices for bookings in the past.
		if($booking->getDate() < new \DateTime(date('Y-m-d')))
			return;
		
	
		// Don't send the notification if the location has no proper notification address.
		$recipients = array();
		$location = $booking->getLocation();
		if($location != null)
		{
			$recipient = $location->getNotificationEmail();
			if(!empty($recipient))
				$recipients[] = $recipient;
		}
		$admin = $this->container->getParameter("admin_email");
		if(!empty($admin))
			$recipients[] = $admin;
		if(empty($recipients))
			return;
	
		// Render the message body.
		$body = $this->renderView('emails/bookingcancelled.html.twig', array(
				'booking' => $booking
		));
		$subject = sprintf("Booking cancellation of %s | %s %s - %s | %s", $booking->getBoat()->getName(),
				$booking->getDate()->format('l j M Y'),
				$booking->getStart()->format('G:i'),
				$booking->getEnd()->format('G:i'),
				$booking->getLesseeName());
	
		// Compose and send the message.
		$message = \Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom("info@rodaboats.eu")
		->setTo($recipients)
		->setBody($body, 'text/html')
		;
		$this->get('mailer')->send($message);
	}
	
	/**
	 * Notifies the lessee of his/her booking.
	 * @param Booking $booking
	 */
	protected function notifyBookingLessee(Booking $booking)
	{
	
		// Don't send the notification if the location has no proper notification address.
		$recipient = $booking->getLesseeEmail();
		if(empty($recipient))
			return;
		
		// Lookup the template
		$template = $this->getDoctrine()->getRepository('AppBundle:EmailTemplate')
		->findOneBy(array(
				'name' => 'TEMPLATE_CONFIRMATION',
				'language' => strtoupper($booking->getLanguage())
		));
	
		// Render the message body.
		$body = $this->renderView('emails/bookingconfirmation.html.twig', array(
				'booking' => $booking,
				'template' => $template
		));
		$subject = sprintf("Confirmation of booking | %s %s - %s | %s",
				$booking->getDate()->format('l j M Y'),
				$booking->getStart()->format('G:i'),
				$booking->getEnd()->format('G:i'),
				$booking->getLesseeName());
	
		// Compose and send the message.
		$message = \Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom("info@rodaboats.eu")
		->setTo($recipient)
		->setBody($body, 'text/html')
		;
		$this->get('mailer')->send($message);
	}
	
	/**
	 * @Route("/booking/check_conflicting", name="bookingcheck")
	 */
	public function checkConflicting(Request $request)
	{
	
		// Construct a booking record.
		$booking = new Booking();
		$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($request->query->get('boat_id'));
		$booking->setBoat($boat);
		$booking->setDate($request->query->get('date'));
		$booking->setStart($request->query->get('start'));
		$booking->setEnd($request->query->get('end'));
		$booking->setType($request->query->get('type'));
		
		$response = new \stdClass();
		$response->error = false;
		$response->message = "";
		
		
		// Check for conflicts.
		$conflicts = $this->getConflicting($booking);
		if(!empty($conflicts))
		{
			$conflict = $conflicts[0];
			$response->error = true;
			$response->message = 'Unable to book boat due to conflicting booking for: '.$conflict->getLesseeName().' by: '.$conflict->getBookedBy();
		}
		
		$response = new JsonResponse($response, 200);
		return $response;
	}
	
	/**
	 * @Route("/booking/{id}/price", name="editbookingprice")
	 */
	public function editBookingPrice(Request $request, $id)
	{
		
		$booking = $this->getDoctrine()->getRepository('AppBundle:Booking')->find($id);
		$form = $this->buildPriceForm($booking);
		$form->handleRequest($request);
		
		if($form->isValid())
		{
				
			$em = $this->getDoctrine()->getManager();
			$em->flush();
				
			// Notify and redirect.
			$this->addFlash('notice', 'The price of the booking have been saved.');
			return $this->redirectToRoute('viewbooking', array('id' => $booking->getId()));
		}
		
		return $this->render('booking/edit.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	/**
	 * @Route("/booking/{id}/close", name="closebooking")
	 */
	public function closeBooking(Request $request, $id)
	{
	
		$booking = $this->getDoctrine()->getRepository('AppBundle:Booking')->find($id);
		$form = $this->buildCloseForm($booking);
		$form->handleRequest($request);
	
		if($form->isValid())
		{
	
			$booking->setStatus(Booking::STATUS_CLOSED);
			$em = $this->getDoctrine()->getManager();
			$em->flush();
	
			// Notify and redirect.
			$this->addFlash('notice', 'The booking has been closed.');
			return $this->redirectToRoute('booking');
		}
	
		return $this->render('booking/close.html.twig', array(
				'form' => $form->createView(),
				'booking' => $booking
		));
	}
	
	/**
	 * @Route("/booking/{id}/cancel", name="cancelbooking")
	 */
	public function cancelBooking(Request $request, $id)
	{
	
		$booking = $this->getDoctrine()->getRepository('AppBundle:Booking')->find($id);
		$form = $this->buildCancelForm($booking);
		$form->handleRequest($request);
	
		if($form->isValid())
		{
	
			$booking->setStatus(Booking::STATUS_CANCELLED);
			$em = $this->getDoctrine()->getManager();
			$em->flush();
	
			// Notify and redirect.
			$this->addFlash('notice', 'The booking has been cancelled.');
			$this->notifyBookingCancellation($booking);
			return $this->redirectToRoute('booking');
		}
	
		return $this->render('booking/cancel.html.twig', array(
				'form' => $form->createView(),
				'booking' => $booking
		));
	}
	
	/**
	 * @Route("/booking/{id}/edit", name="editbooking")
	 */
	public function editBooking(Request $request, $id)
	{
		
		$booking = $this->getDoctrine()->getRepository('AppBundle:Booking')->find($id);
		$form = $this->buildEditForm($booking);
		$form->handleRequest($request);
		
		if($form->isValid())
		{
			
			// Check for conflicting bookings
			$conflicts = $this->getConflicting($booking);
			if(!empty($conflicts))
			{
				$conflict = $conflicts[0];
				$this->addFlash('danger', 'Unable to edit booking due to conflicting booking for: '.$conflict->getLesseeName().' by: '.$conflict->getBookedBy());
				return $this->render('booking/edit.html.twig', array(
						'form' => $form->createView(),
				));
			}
			
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			
			// Notify and redirect.
			$this->addFlash('notice', 'The changs to the booking have been saved.');
			return $this->redirectToRoute('booking');
		}
		
		return $this->render('booking/edit.html.twig', array(
    		'form' => $form->createView(),
    	));
	}
	
	/**
	 * @Route("/booking/{id}", name="viewbooking")
	 */
	public function viewBooking(Request $request, $id)
	{
	
		$booking = $this->getDoctrine()->getRepository('AppBundle:Booking')->find($id);
		$logs = $this->getDoctrine()->getRepository('AppBundle:BookingLog')->findByBooking($booking);
	
		return $this->render('booking/view.html.twig', array(
				'booking' => $booking,
				'logs' => $logs
		));
	}
	
	/**
	 * @Route("/booking/{id}/log", name="bookinglog")
	 */
	public function viewLog(Request $request, $id)
	{
	
		$booking = $this->getDoctrine()->getRepository('AppBundle:Booking')->find($id);
		$logs = $this->getDoctrine()->getRepository('AppBundle:BookingLog')->findByBooking($booking);
		
		return $this->render('booking/log.html.twig', array(
			'booking' => $booking,
			'logs' => $logs
		));
	}

	/**
	 * Retrieves bookings which are conflicting with the passed booking.
	 * @param Booking $booking
	 */
	protected function getConflicting(Booking $booking)
	{
	
		$repo = $this->getDoctrine()->getRepository('AppBundle:Booking');
		$builder = $repo->createQueryBuilder('b')
		->andWhere('b.date = :date')
		->setParameter("date", $booking->getDate())
		->andWhere('b.boat = :boat')
		->setParameter("boat", $booking->getBoat());
		
		// Only match non cancelled bookings.
		$builder->andWhere($builder->expr()->in('b.status', array(
				Booking::STATUS_CLOSED,
				Booking::STATUS_CONFIRMED,
				Booking::STATUS_DELIVERED
		)));
		
		// If we are booking a fishing booking, other fishing bookings are not conflicting.
		if($booking->getType() == Booking::TYPE_FISHING)
			$builder->andWhere($builder->expr()->neq('b.type', ":type"))
			->setParameter("type", Booking::TYPE_FISHING);
	
		// Lookup bookings which (partially) overlap the selected period. Either their start and/or end time fall within
		// an existing booking, or they completely overlap and existing booking.
		$builder->andWhere(
				$builder->expr()->orX(
						$builder->expr()->andX(
								$builder->expr()->gt("b.start", ":start"),
								$builder->expr()->lt("b.start", ":end")
						),
						$builder->expr()->andX(
								$builder->expr()->gt("b.end", ":start"),
								$builder->expr()->lt("b.end", ":end")
						),
						$builder->expr()->andX(
								$builder->expr()->lte("b.start", ":start"),
								$builder->expr()->gte("b.end", ":end")
						)
				)
		)
		->setParameter("start", $booking->getStart())
		->setParameter("end", $booking->getEnd());
		
		// Exclude the current booking in case it is already persisted.
		if($booking->getId() != null)
		{
			$builder->andWhere('b.id != :id')
			->setParameter("id", $booking->getId());
		}
		
		$query = $builder->getQuery();
		return $query->getResult();
	}
    
}


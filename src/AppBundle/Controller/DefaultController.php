<?php

namespace AppBundle\Controller;

use AppBundle\DataProvider\OrderDataProvider;
use AppBundle\Model\Calendar;
use AppBundle\Model\OrderCalendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Boat;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Location;
use \DateTime;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route(path="/test-print", name="app.default.test_print")
     * @param Request $request
     * @return Response
     */
    public function testPrintAction(Request $request)
    {
        return $this->render('default/test_print.html.twig', ['paper_size' => 'A4']);
    }

    /**
     * @Route(path="/", name="calendar")
     * @param Request $request
     * @return Response
     */
    public function dashboardAction(Request $request)
    {
        return $this->calendarAction($request, new \DateTime());
    }

    /**
     * @Route(path="/calendar/{date}", options={"expose"=true}, name="calendardate")
     * @param Request $request
     * @param $date
     * @return Response
     */
    public function calendarAction(Request $request, $date)
    {
        $manager = $this->get('app.data.manager');

        if (!($date instanceof DateTime)) {
            $date = new DateTime($date);
        }

        $location = $request->query->get('location');
        if (!empty($location)) {
            $location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($location);
            $boats = $manager->getBoatRepository()->findBy([
                'status'    => Boat::STATUS_ACTIVE,
                'location'  => $location
            ]);
        } else {
            $boats = $manager->getBoatRepository()->findBy([
                'status'    => Boat::STATUS_ACTIVE
            ]);
        }

        $nextDate = clone $date;
        $nextDate->modify('+1 day');
        $prevDate = clone $date;
        $prevDate->modify('-1 day');

        $calendar = new OrderCalendar();
        $calendar->populateSlots(5, 22);

        foreach ($boats as $boat) {
            $calendar->addBoat($boat);
        }

        $orders = $manager->getOrderRepository()->findBy([
            'date'      => $date,
            'status'    => [
                OrderDataProvider::STATUS_CONFIRMED,
                OrderDataProvider::STATUS_CLOSED,
                OrderDataProvider::STATUS_DELIVERED
            ]
        ]);

        foreach ($orders as $order) {
            $calendar->addOrder($order);
        }

        $locations = $this->getDoctrine()->getRepository('AppBundle:Location')->findBy(array(
            'status' => Location::STATUS_ACTIVE
        ));

        $printLink = '#';
        if (!empty($location)) {
            $printLink = $this->generateUrl('app.default.calendar.print', [
                'date'      => $date->format('Y-m-d'),
                'location'  => $location->getId()
            ]);
        }

        return $this->render('dashboard/calendar.html.twig', array(
            'calendar'  => $calendar,
            'date'      => $date,
            'locations' => $locations,
            'next_date' => $nextDate->format('Y-m-d'),
            'prev_date' => $prevDate->format('Y-m-d'),
            'location'  => $location,
            'printLink' => $printLink
        ));
    }

    /**
     * @Route(path="/calendar-print", name="app.default.calendar.print")
     * @param Request $request
     */
    public function printTimeSheetAction(Request $request)
    {
        $date = $request->query->get('date');
        $location = $request->query->get('location');

        if (!($date instanceof DateTime)) {
            $date = new DateTime($date);
        }

        $manager = $this->get('app.data.manager');


        $location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($location);
        $boats = $manager->getBoatRepository()->findBy([
            'status'    => Boat::STATUS_ACTIVE,
            'location'  => $location
        ]);

        $calendar = new OrderCalendar();
        $calendar->populateSlots(5, 22);

        foreach ($boats as $boat) {
            $calendar->addBoat($boat);
        }

        $orders = $manager->getOrderRepository()->findBy([
            'date'      => $date,
            'status'    => [
                OrderDataProvider::STATUS_CONFIRMED,
                OrderDataProvider::STATUS_CLOSED,
                OrderDataProvider::STATUS_DELIVERED
            ]
        ]);

        foreach ($orders as $order) {
            $calendar->addOrder($order);
        }

        return $this->render('dashboard/calendar_print.html.twig', array(
            'calendar'  => $calendar,
            'date'      => $date,
            'location'  => $location,
            'landscape' => true
        ));
    }

    /**
     * @Route("/old_dashboard", name="old_dashboard")
     */
    public function oldDashboardAction(Request $request)
    {
        return $this->oldCalendarAction($request, new DateTime());
    }

    /**
     * @Route("/old_calendar/{date}", options={"expose"=true}, name="old_calendardate")
     * @param Request $request
     * @param \DateTime|string $date
     * @return Response
     */
    public function oldCalendarAction(Request $request, $date)
    {
        if (!($date instanceof DateTime)) {
            $date = new DateTime($date);
        }

        // Load all active boats.
        // Check if a location filter needs to be applied.
        $location = $request->query->get('location');
        if (!empty($location)) {
            $location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($location);
            $boats = $this->getDoctrine()->getRepository('AppBundle:Boat')->findBy(array(
                'status' => Boat::STATUS_ACTIVE,
                'location' => $location
            ));
        } else {
            $boats = $this->getDoctrine()->getRepository('AppBundle:Boat')->findBy(array(
                'status' => Boat::STATUS_ACTIVE
            ));
        }

        $nextDate = clone $date;
        $nextDate = $nextDate->modify('+1 day');
        $prevDate = clone $date;
        $prevDate = $prevDate->modify('-1 day');

        // Build the calendar
        $calendar = new Calendar();
        $calendar->populateSlots(5, 22);


        foreach ($boats as $boat) {
            $calendar->addBoat($boat);
        }

        $bookings = $this->getDoctrine()->getRepository('AppBundle:Booking')->findBy(array(
            'date' => $date,
            'status' => array(Booking::STATUS_CONFIRMED, Booking::STATUS_CLOSED, Booking::STATUS_DELIVERED)
        ));

        foreach ($bookings as $booking) {
            $calendar->addBooking($booking);
        }
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

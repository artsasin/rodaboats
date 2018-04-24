<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Boat;
use AppBundle\Entity\BoatPrice;
use AppBundle\Entity\Booking;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class BoatPriceController extends Controller
{

    protected function buildForm(BoatPrice $price)
    {


        $form = $this->createFormBuilder($price)
            ->add('boat', 'entity', array(
                'class' => 'AppBundle:Boat',
                'choice_label' => 'name',
            ))
            ->add('start', 'date')
            ->add('end', 'date')
            ->add('petrol1hour', 'money')
            ->add('petrol2hour', 'money')
            ->add('petrol4hour', 'money')
            ->add('petrol8hour', 'money')
            ->add('rent1hour', 'money')
            ->add('rent2hour', 'money')
            ->add('rent4hour', 'money')
            ->add('rent8hour', 'money')
            ->add('deposit', 'money')
            ->add('fishing', 'money', array('required' => false))
            ->add('rentWeek', 'money', array('required' => false))
            ->add('save', 'submit', array('label' => 'Save'))
            ->getForm();

        return $form;
    }

    /**
     * @Route("/boat/{boat}/price/edit", name="addboatprice")
     * @Route("/admin/price/edit", name="addprice")
     */
    public function addPrice(Request $request, $boat = null)
    {

        $price = new BoatPrice();
        if ($boat !== null) {
            $boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($boat);
            $price->setBoat($boat);
        }

        $form = $this->buildForm($price);
        $form->handleRequest($request);

        if ($form->isValid()) {

            // Save the new price.
            $em = $this->getDoctrine()->getManager();
            $em->persist($price);
            $em->flush();

            // Notify and redirect.
            $this->addFlash('notice', 'The boat price has been added.');
            return $this->redirectToRoute('admin');
        }

        return $this->render('price/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/price/{id}", name="editprice")
     */
    public function editPrice(Request $request, $id)
    {

        $price = $this->getDoctrine()->getRepository('AppBundle:BoatPrice')->find($id);
        $form = $this->buildForm($price);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // Notify and redirect.
            $this->addFlash('notice', 'The changs to the boat price have been saved.');
            return $this->redirectToRoute('viewboat', array('id' => $price->getBoat()->getId()));
        }

        return $this->render('price/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/boat/price/suggest", options={"expose"=true}, name="suggestprice")
     * @param Request $request
     * @return JsonResponse
     */
    public function suggestPrice(Request $request)
    {
        $date = new \DateTime($request->query->get('date'));
        $start = new \DateTime();
        $start->setTimestamp($request->query->get('start'));
        $end = new \DateTime();
        $end->setTimestamp($request->query->get('end'));
        $boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($request->query->get('id'));
        $type = $request->query->get('type');

        // Calculate duration in hours
        $diff = $start->diff($end, true);
        $duration = $diff->h;

        $response = new \stdClass();
        $response->success = false;

        /** @var BoatPrice $price */
        $price = $boat->getCurrentPrice($date);
        if ($duration !== null && $price !== null) {
            $response->success = true;
            if ($type == Booking::TYPE_FISHING) {
                // Fishing bookings do not have any petrol or deposit cost.
                $response->rent = $price->getFishing();
                $response->petrolCost = 0;
                $response->deposit = 0;
            } else {
                $response->deposit = $price->getDeposit();
                if ($duration <= 1) {
                    $response->rent = $price->getRent1Hour();
                    $response->petrolCost = $price->getPetrol1Hour();
                }
                if ($duration <= 2) {
                    $response->rent = $price->getRent2Hour();
                    $response->petrolCost = $price->getPetrol2Hour();
                } else if ($duration <= 4) {
                    $response->rent = $price->getRent4Hour();
                    $response->petrolCost = $price->getPetrol4Hour();
                } else if ($duration <= 8) {
                    $response->rent = $price->getRent8Hour();
                    $response->petrolCost = $price->getPetrol8Hour();
                }
            }
        }

        return new JsonResponse($response, 200);
    }
}

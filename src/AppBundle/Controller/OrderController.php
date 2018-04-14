<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 13.04.18
 * Time: 13:44
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Boat;
use AppBundle\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderController
 * @package AppBundle\Controller
 * @Route(path="/orders")
 */
class OrderController extends Controller
{
    /**
     * @Route(path="", name="app_order_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Order::class);
        $qb = $repository->getPaginationQueryBuilder($request->query->get('filter'));
        $page = $request->query->getInt('page', 1);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page,25);

        return $this->render('order/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route(path="/add", name="app_order_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $boats = $em->getRepository(Boat::class)->findBy(
            ['status' => Boat::STATUS_ACTIVE],
            ['location' => 'ASC']
        );

        $dtoBoats = [];
        foreach ($boats as $boat) {
            $dto = new \AppBundle\Model\DTO\Boat();
            $dto->fromEntity($boat);
            $dtoBoats[] = $dto;
        }

        $order = new Order();
        $dtoOrder = new \AppBundle\Model\DTO\Order();
        $dtoOrder->fromEntity($order);

        $hours = array('05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22');
        $minutes = array('00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55');

        return $this->render('order/add.html.twig', [
            'boats'         => $dtoBoats,
            'orderModel'    => $dtoOrder,
            'hours'         => $hours,
            'minutes'       => $minutes
        ]);
    }
}
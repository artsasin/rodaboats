<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 13.04.18
 * Time: 13:44
 */

namespace AppBundle\Controller;

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

    public function addAction(Request $request)
    {

    }
}
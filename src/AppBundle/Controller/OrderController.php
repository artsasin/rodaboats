<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 13.04.18
 * Time: 13:44
 */

namespace AppBundle\Controller;

use AppBundle\DataProvider\CustomerDataProvider;
use AppBundle\DataProvider\OrderDataProvider;
use AppBundle\Entity\Boat;
use AppBundle\Entity\Order;
use AppBundle\Model\DTO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @Route(path="/edit/{id}", options={"expose"=true}, name="app_order_edit")
     * @Route(path="/add", options={"expose"=true}, defaults={"id"=null}, name="app_order_add")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        $manager = $this->get('app.data.manager');

        // reference data
        $boats = [];
        $list = $manager->getBoatRepository()->findBy(['status' => Boat::STATUS_ACTIVE], ['location' => 'ASC']);
        foreach ($list as $boat) {
            $model = new DTO\Boat();
            $model->fromEntity($boat);
            $boats[] = $model;
        }

        $hours = OrderDataProvider::hours();
        $minutes = OrderDataProvider::minutes();
        $languages = CustomerDataProvider::languages();
        $countries = CustomerDataProvider::countries();

        $paymentMethods = [];
        foreach (OrderDataProvider::paymentMethods() as $code => $value) {
            $paymentMethods[] = [
                'code'  => $code,
                'text'  => $value
            ];
        }

        $extras = [];
        foreach (OrderDataProvider::extras() as $code => $value) {
            $extras[] = [
                'code'  => $code,
                'text'  => $value,
                'abbr'  => OrderDataProvider::extrasAbbrevations()[$code]
            ];
        }

        $types = [];
        foreach (OrderDataProvider::types() as $code => $value) {
            $types[] = [
                'code'  => $code,
                'text'  => $value
            ];
        }

        $cancellationReasons = [];
        foreach (OrderDataProvider::cancellationReasons() as $code => $value) {
            $cancellationReasons[] = [
                'code'  => $code,
                'text'  => $value
            ];
        }

        // main model
        $order = OrderDataProvider::model(true);

        $boatId = $request->query->get('boat', null);
        $date = $request->query->get('date', null);
        $start = $request->query->get('start', null);

        if ($boatId !== null) {
            $boat = $manager->getBoatRepository()->find($boatId);
            if (!$boat instanceof Boat) {
                throw new NotFoundHttpException();
            }
            $order->boatId = $boat->getId();
        }

        if ($date !== null) {
            try {
                $d = new \DateTime($date);
                $order->date = $d->format('Y-m-d');
            } catch (\Exception $e) {
                // so the date is incorrect and we leave it as current date
            }
        }

        if ($start !== null) {
            $m = $start % 60;
            $h = intval(($start - $m) / 60);
            $d = new \DateTime('01-01-1970');
            $d->setTime($h, $m, 0);
            $order->start = $d->format('c');
            $d->modify('+1 hour');
            $order->end = $d->format('c');
        }

        $customer = new DTO\Customer();
        $customerModel = new DTO\Customer();
        if ($id !== null) {
            $entity = $manager->getOrderRepository()->find($id);
            if (!$entity instanceof Order) {
                throw new NotFoundHttpException();
            }
            $customer->fromEntity($entity->getCustomer());
            $order->fromEntity($entity);
        }

        return $this->render('order/edit.html.twig', [
            'boats'                 => $boats,
            'hours'                 => $hours,
            'minutes'               => $minutes,
            'languages'             => $languages,
            'countries'             => $countries,
            'paymentMethods'        => $paymentMethods,
            'extras'                => $extras,
            'types'                 => $types,
            'order'                 => $order,
            'customer'              => $customer,
            'customerModel'         => $customerModel,
            'statuses'              => OrderDataProvider::statuses(),
            'cancellationReasons'   => $cancellationReasons
        ]);
    }

    /**
     * @Route(path="/log/{id}", options={"expose"=true}, name="app.order.log")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function logAction(Request $request, $id)
    {
        $manager = $this->get('app.data.manager');
        $order = $manager->getOrderRepository()->find($id);
        if (!$order) {
            throw new NotFoundHttpException();
        }

        $logs = $manager->getOrderLogRepository()->findBy(['order' => $order], ['creationDate' => 'ASC']);

        return $this->render('order/log.html.twig', ['logs' => $logs]);
    }
}
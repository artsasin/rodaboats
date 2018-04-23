<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 17.04.18
 * Time: 22:44
 */

namespace AppBundle\Controller\Api;

use AppBundle\DataProvider\OrderDataProvider;
use AppBundle\Exception\RodaboatsException;
use AppBundle\Model\ApiResponse;
use AppBundle\Model\DTO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderController
 * @package AppBundle\Controller\Api
 * @Route(path="/orders")
 */
class OrderController extends Controller
{
    /**
     * @Route(path="/save", options={"expose"=true}, name="app_api_orders_save")
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $json = $request->getContent();
        $model = new DTO\Order();
        $model->fromJson($json);

        $manager = $this->get('app.data.manager');

        $response = new ApiResponse();

        try {
            $conflicts = $manager->isConflicts($model);
            if (count($conflicts) === 0) {
                $order = $manager->saveOrder($model);
                if ($model->id === null) {
                    $this->addFlash('notice', 'The order was saved!');
                    $manager->notifyStaff($order, [0 => $this->getParameter('admin_email')]);
                    $manager->notifyCustomer($order);
                }
                $model->fromEntity($order);
                $response->payload = $model;
            } else {
                $response->status = -1;
                $response->payload = $conflicts;
                $response->message = 'Unable to book boat due to conflicting booking(s)';
            }
        } catch (RodaboatsException $e) {
            $response->status = -1;
            $response->message = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route(path="/close", options={"expose"=true}, name="app_api_orders_close")
     * @param Request $request
     * @return JsonResponse
     */
    public function closeAction(Request $request)
    {
        $json = $request->getContent();
        $model = new DTO\Order();
        $model->fromJson($json);

        $manager = $this->get('app.data.manager');

        $response = new ApiResponse();

        try {
            $model->status = OrderDataProvider::STATUS_CLOSED;
            $order = $manager->saveOrder($model);
            $model->fromEntity($order);
            $response->payload = $model;
        } catch (RodaboatsException $e) {
            $response->status = -1;
            $response->message = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route(path="/cancel", options={"expose"=true}, name="app_api_orders_cancel")
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelAction(Request $request)
    {
        $json = $request->getContent();
        $model = new DTO\Order();
        $model->fromJson($json);

        $manager = $this->get('app.data.manager');

        $response = new ApiResponse();

        try {
            $model->status = OrderDataProvider::STATUS_CANCELLED;
            $order = $manager->saveOrder($model);
            $manager->notifyOrderCancellation(
                $order,
                [0 => $this->getParameter('admin_email')]
            );
            $model->fromEntity($order);
            $response->payload = $model;
        } catch (RodaboatsException $e) {
            $response->status = -1;
            $response->message = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route(path="/check-conflict", options={"expose"=true}, name="app_api_orders_check_conflicts")
     * @Method({"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function checkConflictAction(Request $request)
    {
        $json = $request->getContent();
        $manager = $this->get('app.data.manager');
        $order = new DTO\Order();
        $order->fromJson($json);

        $data = [
            'status'    => 0,
            'valid'     => true,
            'payload'   => [],
            'message'   => null
        ];

        try {
            $conflicts = $manager->isConflicts($order);
            if (count($conflicts) > 0) {
                $data['valid'] = false;
                foreach ($conflicts as $item) {
                    $model = new DTO\Order();
                    $model->fromEntity($item);
                    $data['payload'][] = $model;
                }
            }
        } catch (RodaboatsException $e) {
            $data['status'] = -1;
            $data['message'] = $e->getMessage();
        }

        return new JsonResponse($data);
    }
}
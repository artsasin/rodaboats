<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 17.04.18
 * Time: 22:44
 */

namespace AppBundle\Controller\Api;

use AppBundle\Exception\RodaboatsException;
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
     * @Route(path="/check-conflict", options={"expose"=true}, name="app_api_orders_check_conflicts")
     * @Method({"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function checkConflictAction(Request $request)
    {
        $json = json_decode($request->getContent(), true);
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
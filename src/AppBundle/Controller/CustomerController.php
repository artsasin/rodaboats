<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 09.04.18
 * Time: 16:32
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerController
 * @package AppBundle\Controller
 * @Route(path="/customers")
 */
class CustomerController extends Controller
{
    /**
     * @Route(path="", name="app.customer.index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('customer/index.html.twig', []);
    }

    /**
     * @Route(path="/list", options={"expose"=true}, name="app.customer.get.list")
     * @Method({"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $rowsPerPage = $request->query->get('rowsPerPage', 10);

        $em = $this->getDoctrine()->getManager();

        /** @var CustomerRepository $repository */
        $repository = $em->getRepository(Customer::class);

        $data = [
            'status'    => 0,
            'total'     => 0,
            'items'     => []
        ];

        try {
            $data['total'] = intval($repository->total());
            $customers = $repository->page($page, $rowsPerPage);
            foreach ($customers as $customer) {
                $model = new \AppBundle\Model\DTO\Customer();
                $model->fromEntity($customer);
                $data['items'][] = $model;
            }
        } catch (\Exception $e) {
            $data['status'] = -1;
            $data['message'] = $e->getMessage();
        }

        // $customers = $em->getRepository(Customer::class)->find

        return new JsonResponse($data);
    }
}
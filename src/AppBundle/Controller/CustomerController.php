<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 09.04.18
 * Time: 16:32
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Model\ApiResponse;
use AppBundle\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;

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
        $customerModel = new \AppBundle\Model\DTO\Customer();

        $list = Intl::getRegionBundle()->getCountryNames();
        $countries = [
            ['value' => '', 'text' => '...']
        ];
        foreach ($list as $key => $value) {
            $countries[] = ['value' => $key, 'text' => $value];
        }
        $list = Intl::getLanguageBundle()->getLanguageNames();
        $langs = [
            ['value' => '', 'text' => '...']
        ];
        foreach ($list as $key => $value) {
            $langs[] = ['value' => $key, 'text' => $value];
        }

        return $this->render('customer/index.html.twig', [
            'customerModel' => $customerModel,
            'countries'     => $countries,
            'langs'         => $langs
        ]);
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
        $limit = $request->query->get('limit', 10);
        $query = $request->query->get('query', null);
        $orderBy = $request->request->get('orderBy', null);
        $ascending = $request->request->get('ascending', null);
        $byColumn = $request->request->get('byColumn', 0);

        $em = $this->getDoctrine()->getManager();

        /** @var CustomerRepository $repository */
        $repository = $em->getRepository(Customer::class);

        $data = [
            'status'    => 0,
            'count'     => 0,
            'data'     => []
        ];

        try {
            $data['count'] = intval($repository->total($query));
            $customers = $repository->page($page, $limit, $query);
            foreach ($customers as $customer) {
                $model = new \AppBundle\Model\DTO\Customer();
                $model->fromEntity($customer);
                $data['data'][] = $model;
            }
        } catch (\Exception $e) {
            $data['status'] = -1;
            $data['message'] = $e->getMessage();
        }

        return new JsonResponse($data);
    }

    /**
     * @Route(path="/save", options={"expose"=true}, name="app.customer.save")
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $content = $request->getContent();

        $model = new \AppBundle\Model\DTO\Customer();
        $model->fromJson($content);

        $data = new ApiResponse();

        if ($model->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($model->id) {
                $customer = $em->getRepository(Customer::class)->find($model->id);
            } else {
                $customer = new Customer();
            }

            if (!$customer instanceof Customer) {
                throw new NotFoundHttpException();
            }

            $customer->setFirstName($model->firstName);
            $customer->setLastName($model->lastName);
            $customer->setCountry($model->country);
            $customer->setLanguage($model->language);
            $customer->setPhoneNumber($model->phoneNumber);
            $customer->setEmail($model->email);
            $customer->setComment($model->comment);

            $em->persist($customer);
            $em->flush();

            $result = new \AppBundle\Model\DTO\Customer();
            $result->fromEntity($customer);
            $data->payload = $result;
        } else {
            $data->status = -1;
            $data->message = 'Fill all required fields';
        }

        return new JsonResponse($data);
    }

    /**
     * @Route(path="/details/{id}", options={"expose"=true}, name="app.customer.rest.details")
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function detailsAction(Request $request, $id)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $repository = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repository->find($id);

        $data = [
            'status'    => 0,
            'customer'  => null,
            'message'   => null
        ];

        if (!$customer instanceof Customer) {
            $data['status'] = -1;
            $data['message'] = sprintf('Customer with id %s not found', $id);
        }

        $dto = new \AppBundle\Model\DTO\Customer();
        $dto->fromEntity($customer);

        $data['customer'] = $dto;

        return new JsonResponse($data);
    }
}
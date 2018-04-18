<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 17.04.18
 * Time: 20:00
 */

namespace AppBundle\Manager;


use AppBundle\DataProvider\OrderDataProvider;
use AppBundle\Entity\Boat;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order;
use AppBundle\Exception\RodaboatsException;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Model\DTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DataManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * DataManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return \AppBundle\Repository\OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->em->getRepository(Order::class);
    }

    /**
     * @return \AppBundle\Repository\BoatRepository
     */
    public function getBoatRepository()
    {
        return $this->em->getRepository(Boat::class);
    }

    /**
     * @return \AppBundle\Repository\CustomerRepository
     */
    public function getCustomerRepository()
    {
        return $this->em->getRepository(Customer::class);
    }

    /**
     * @param DTO\Order $model
     * @return Order|null
     * @throws RodaboatsException
     */
    public function saveOrder(DTO\Order $model)
    {
        $order = null;

        if ($model->isValid()) {
            $order = new Order();
            if ($model->id) {
                $order = $this->getOrderRepository()->find($model->id);
            }

            if (!$order instanceof Order) {
                throw new RodaboatsException('Order is not found');
            }

            $boat = $this->getBoatRepository()->find($model->boatId);
            if (!$boat instanceof Boat) {
                throw new RodaboatsException('Boat is not found');
            }

            $customer = $this->getCustomerRepository()->find($model->customerId);
            if (!$customer instanceof Customer) {
                throw new RodaboatsException('Customer is not found');
            }

            if ($order->getId() && $order->getCustomer() !== $customer) {
                $order->getCustomer()->removeOrder($order);
            }
            $order->setCustomer($customer);

            if ($order->getId() && $order->getBoat() !== $boat) {
                $order->getBoat()->removeOrder($order);
                $order->getLocation()->removeOrder($order);
            }
            $order->setBoat($boat);
            $order->setLocation($boat->getLocation());

            $order->setDate(new \DateTime($model->date));
            $order->setStart(new \DateTime($model->start));
            $order->setEnd(new \DateTime($model->end));

            $order->setType($model->type);
            $order->setNumberOfPeople($model->numberOfPeople);
            $order->setBookedBy($model->bookedBy);
            $order->setExtra($model->extra);

            $order->setRent($model->rent);
            $order->setRentDiscount($model->rentDiscount);
            $order->setPaymentMethodRent($model->paymentMethodRent);
            $order->setPetrolCost($model->petrolCost);
            $order->setDeposit($model->deposit);
            $order->setPaymentMethodDeposit($model->paymentMethodDeposit);

            $order->setComments($model->comments);

            $user = $this->tokenStorage->getToken()->getUser();
            $order->setHandler($user);

            $this->em->persist($order);
            $this->em->flush();
        }
        return $order;
    }

    /**
     * @param DTO\Order $order
     * @return Order[]
     * @throws RodaboatsException
     */
    public function isConflicts(DTO\Order $order)
    {
        try {
            $date = new \DateTime($order->date);
        } catch (\Exception $e) {
            throw new RodaboatsException('Order date is invalid');
        }

        try {
            $start = new \DateTime($order->start);
        } catch (\Exception $e) {
            throw new RodaboatsException('Order start has incorrect value');
        }

        try {
            $end = new \DateTime($order->end);
        } catch (\Exception $e) {
            throw new RodaboatsException('Order end has incorrect value');
        }


        $qb = $this->getOrderRepository()->createQueryBuilder('orders');

        $qb->andWhere($qb->expr()->eq('orders.date', ':date'));
        $qb->setParameter('date', $date);

        $qb->andWhere($qb->expr()->eq('orders.boat', ':boat'));
        $qb->setParameter('boat', $order->boatId);

        $qb->andWhere($qb->expr()->in('orders.status', [
            OrderDataProvider::STATUS_CONFIRMED,
            OrderDataProvider::STATUS_DELIVERED,
            OrderDataProvider::STATUS_CLOSED
        ]));

        if ($order->type === OrderDataProvider::TYPE_FISHING) {
            $qb->andWhere($qb->expr()->neq('orders.type', ':type'));
            $qb->setParameter('type', OrderDataProvider::TYPE_FISHING);
        }

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gt('orders.start', ':start'),
                    $qb->expr()->lt('orders.start', ':end')
                ),
                $qb->expr()->andX(
                    $qb->expr()->gt('orders.end', ':start'),
                    $qb->expr()->lt('orders.end', ':end')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('orders.start', ':start'),
                    $qb->expr()->gte('orders.end', ':end')
                )
            )
        );
        $qb->setParameter('start', $start);
        $qb->setParameter('end', $end);

        if ($order->id !== null) {
            $qb->andWhere($qb->expr()->neq('orders.id', ':id'));
            $qb->setParameter('id', $order->id);
        }

        return $qb->getQuery()->getResult();
    }
}
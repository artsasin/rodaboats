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
use AppBundle\Entity\Order;
use AppBundle\Exception\RodaboatsException;
use Doctrine\ORM\EntityManagerInterface;

class DataManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DataManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return \AppBundle\Repository\OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->em->getRepository(Order::class);
    }

    /**
     * @param \AppBundle\Model\DTO\Order $order
     * @return Order[]
     * @throws RodaboatsException
     */
    public function isConflicts(\AppBundle\Model\DTO\Order $order)
    {
        try {
            $start = new \DateTime($order->start);
        } catch (\Exception $e) {
            throw new RodaboatsException($e->getMessage(), $e->getCode());
        }

        try {
            $end = new \DateTime($order->end);
        } catch (\Exception $e) {
            throw new RodaboatsException($e->getMessage(), $e->getCode());
        }

        try {
            $date = new \DateTime($order->date);
        } catch (\Exception $e) {
            throw new RodaboatsException($e->getMessage(), $e->getCode());
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
            $qb->setParameter('type',OrderDataProvider::TYPE_FISHING);
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
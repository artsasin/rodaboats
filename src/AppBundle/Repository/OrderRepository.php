<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 07.04.18
 * Time: 1:45
 */

namespace AppBundle\Repository;

use AppBundle\DataProvider\OrderDataProvider;
use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    public function getPaginationQueryBuilder($filter = null)
    {
        $qb = $this->createQueryBuilder('o');

        switch ($filter) {
            case 'today':
                $qb->andWhere($qb->expr()->in('o.status', [
                    OrderDataProvider::STATUS_CLOSED,
                    OrderDataProvider::STATUS_CONFIRMED,
                    OrderDataProvider::STATUS_DELIVERED
                ]));
                $qb->andWhere('o.date=:date');
                $qb->setParameter('date', date('Y-m-d'));
                break;
            case 'active':
                $qb->andWhere($qb->expr()->in('o.status', [
                    OrderDataProvider::STATUS_CONFIRMED,
                    OrderDataProvider::STATUS_DELIVERED
                ]));
                break;
            case 'unprocessed':
                $qb->andWhere($qb->expr()->in('o.status', [
                    OrderDataProvider::STATUS_DELIVERED
                ]));
                break;
            case 'all':
                break;
            default:
                break;
        }

        $qb->orderBy('o.date', 'ASC');

        return $qb;
    }
}
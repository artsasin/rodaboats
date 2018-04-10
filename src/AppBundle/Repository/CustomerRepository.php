<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 07.04.18
 * Time: 1:00
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CustomerRepository extends EntityRepository
{
    /**
     * @param string $query
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function total($query = null)
    {
        $qb = $this->createQueryBuilder('customer');
        $qb->select('COUNT(customer.id)');
        if ($query !== null && $query !== '') {
            $likes = explode(' ', $query);
            if (count($likes) > 0) {
                foreach ($likes as $like) {
                    $qb->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->like("CONCAT(customer.firstName, ' ', customer.lastName)", $qb->expr()->literal('%' . $like . '%')),
                            $qb->expr()->like("customer.phoneNumber", $qb->expr()->literal('%' . $like . '%'))
                        )
                    );
                }
            }
        }


        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $query
     * @return array
     */
    public function page($page, $limit, $query = null)
    {
        $qb = $this->createQueryBuilder('customer');
        $qb->select('customer');

        if ($query !== null && $query !== '') {
            $likes = explode(' ', $query);
            if (count($likes) > 0) {
                foreach ($likes as $like) {
                    $qb->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->like("CONCAT(customer.firstName, ' ', customer.lastName)", $qb->expr()->literal('%' . $like . '%')),
                            $qb->expr()->like("customer.phoneNumber", $qb->expr()->literal('%' . $like . '%'))
                        )
                    );
                }
            }
        }

        $qb->orderBy('customer.firstName, customer.lastName');
        $qb->setFirstResult(($page - 1) * $limit);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
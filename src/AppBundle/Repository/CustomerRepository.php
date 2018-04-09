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
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function total()
    {
        $qb = $this->createQueryBuilder('customer');
        $qb->select('COUNT(customer.id)');


        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * @param $page
     * @param $rowsPerPage
     * @return array
     */
    public function page($page, $rowsPerPage)
    {
        $qb = $this->createQueryBuilder('customer');
        $qb->select('customer');
        $qb->orderBy('customer.firstName, customer.lastName');
        $qb->setFirstResult(($page - 1) * $rowsPerPage);
        $qb->setMaxResults($rowsPerPage);

        return $qb->getQuery()->getResult();
    }
}
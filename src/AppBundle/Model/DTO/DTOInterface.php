<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 09.04.18
 * Time: 17:55
 */

namespace AppBundle\Model\DTO;


interface DTOInterface
{
    /**
     * @param object $entity
     * @return void
     */
    public function fromEntity($entity);

    /**
     * @param string $json
     * @return void
     */
    public function fromJson($json);
}
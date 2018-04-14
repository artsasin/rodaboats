<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 14.04.18
 * Time: 15:33
 */

namespace AppBundle\Model\DTO;


class Boat implements DTOInterface
{
    public $id;

    public $name;

    public $locationId;

    public $locationName;

    /**
     * @param \AppBundle\Entity\Boat $entity
     */
    public function fromEntity($entity)
    {
        $this->id = $entity->getId();
        $this->name = $entity->getName();

        $location = $entity->getLocation();
        if ($location !== null) {
            $this->locationId = $entity->getLocation()->getId();
            $this->locationName = $entity->getLocation()->getName();
        }
    }

    /**
     * @param string $json
     */
    public function fromJson($json)
    {
        return;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return true;
    }
}
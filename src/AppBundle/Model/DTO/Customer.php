<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 09.04.18
 * Time: 17:48
 */

namespace AppBundle\Model\DTO;


class Customer implements DTOInterface
{
    public $id;

    public $firstName;

    public $lastName;

    public $country;

    public $language;

    public $phoneNumber;

    public $email;

    /**
     * @param \AppBundle\Entity\Customer $customer
     */
    public function fromEntity($customer)
    {
        $this->id = $customer->getId();
        $this->firstName = $customer->getFirstName();
        $this->lastName = $customer->getLastName();
        $this->country = $customer->getCountry();
        $this->language = $customer->getLanguage();
        $this->phoneNumber = $customer->getPhoneNumber();
        $this->email = $customer->getEmail();
    }

    public function fromJson($json)
    {
        $data = json_decode($json, true);
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return (
            $this->firstName !== null &&
            $this->lastName !== null &&
            $this->country !== null &&
            $this->language !== null &&
            $this->phoneNumber !== null
        );
    }
}
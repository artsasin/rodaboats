<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 09.04.18
 * Time: 17:48
 */

namespace AppBundle\Model\DTO;


use AppBundle\Exception\RodaboatsException;

class Customer implements DTOInterface
{
    public $id;

    public $firstName;

    public $lastName;

    public $country;

    public $language;

    public $phoneNumber;

    public $email;

    public $comment;

    public $identityNumber;

    public $licenseNumber;

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
        $this->comment = $customer->getComment();
        $this->identityNumber = $customer->getIdentityNumber();
        $this->licenseNumber = $customer->getLicenseNumber();
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
     * @throws RodaboatsException
     */
    public function isValid()
    {
        if (
            $this->firstName === null ||
            $this->lastName === null ||
            $this->country === null ||
            $this->language === null ||
            $this->phoneNumber === null
        ) {
            throw new RodaboatsException('Fill all required fields');
        }

        if (!empty($this->email)) {
            if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
                throw new RodaboatsException('Email is invalid');
            }
        }

        return true;
    }
}
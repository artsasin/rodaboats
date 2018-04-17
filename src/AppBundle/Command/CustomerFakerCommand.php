<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 07.04.18
 * Time: 15:41
 */

namespace AppBundle\Command;

use AppBundle\DataProvider\CustomerDataProvider;
use AppBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Faker;
use Symfony\Component\Intl\Intl;

class CustomerFakerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('app:customer:faker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Faker\Factory::create();
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        for ($i = 0; $i < 100; $i++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName);
            $customer->setLastName($faker->lastName);
            $customer->setCountry(array_rand(CustomerDataProvider::countries()));
            $customer->setLanguage(array_rand(CustomerDataProvider::languages()));
            $customer->setPhoneNumber($faker->e164PhoneNumber);
            $dice = rand(1, 100);
            if ($dice > 10) {
                $customer->setEmail($faker->email);
            }
            $em->persist($customer);
        }
        $em->flush();
    }
}
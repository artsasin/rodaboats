<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 07.04.18
 * Time: 15:41
 */

namespace AppBundle\Command;

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
            $customer->setCountry($faker->countryCode);
            $customer->setLanguage($faker->languageCode);
            $customer->setPhoneNumber($faker->e164PhoneNumber);
            $dice = rand(1, 100);
            if ($dice > 10) {
                $customer->setEmail($faker->email);
            }
            $em->persist($customer);
        }
        $em->flush();
//        $output->writeln($faker->firstName);
//        $output->writeln($faker->lastName);
//        $output->writeln($faker->email);
//        $output->writeln($faker->languageCode);
//        $output->writeln($faker->countryCode);
//        $formatter = $faker->getFormatter('PhoneNumber');
//        dump($formatter);
//        $output->writeln($faker->e164PhoneNumber);
//        dump(Intl::getLanguageBundle()->getLanguageNames());
    }
}
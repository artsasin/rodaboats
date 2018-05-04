<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 04.05.18
 * Time: 16:09
 */

namespace AppBundle\Command;


use AppBundle\DataProvider\CustomerDataProvider;
use AppBundle\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerLangActualizerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:customer:lang:actualizer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('app.data.manager');

        $customers = $manager->getCustomerRepository()->findAll();
        $countryKeys = array_keys(CustomerDataProvider::countries());

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            if (!in_array($customer->getCountry(), $countryKeys)) {
                $output->writeln('Update missed country key ' . $customer->getCountry() . ' to OTHER');
                $customer->setCountry(CustomerDataProvider::COUNTRY_OTHER);
            }
        }

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $em->flush();
    }
}
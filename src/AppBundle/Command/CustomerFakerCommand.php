<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 07.04.18
 * Time: 15:41
 */

namespace AppBundle\Command;

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
        $output->writeln($faker->firstName);
        $output->writeln($faker->lastName);
        $output->writeln($faker->email);
        $output->writeln($faker->languageCode);
        $output->writeln($faker->countryCode);
        $output->writeln($faker->phoneNumber);
//        dump(Intl::getLanguageBundle()->getLanguageNames());
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 04.05.18
 * Time: 16:38
 */

namespace AppBundle\Command;


use AppBundle\DataProvider\CustomerDataProvider;
use AppBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportOldDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:import:old_data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $bookings = $em->getRepository(Booking::class)->findBy(['imported' => false]);

        $languages = CustomerDataProvider::languages();

        $emails = [];
        $phones = [];
        $count = 0;

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $langKey = $booking->getLanguage();
            $fname = $booking->getLesseeFirstName();
            $fnameFlag = preg_replace('/[^A-Za-z]/', '', $fname);
            $lname = $booking->getLesseeLastName();
            $lnameFlag = preg_replace('/[^A-Za-z]/', '', $lname);
            $phone = $booking->getLesseePhone();
            $phone = preg_replace('/[^\d+]/', '', $phone);
            $email = $booking->getLesseeEmail();
            $lang = $languages[strtolower($langKey)];

            if (empty($phone)) {
                continue;
            }

            if (empty($fnameFlag) || empty($lnameFlag)) {
                continue;
            }

            if (!in_array($phone, $phones) && !in_array($email, $emails)) {
                $phones[] = $phone;
                if (!empty($email)) {
                    $emails[] = $email;
                }
                $count++;
                $output->writeln(sprintf('Customer data: %s %s, %s, %s', $fname, $lname, $phone, $email, $lang));
            }
        }

        $output->writeln(sprintf('Total bookings: %s', count($bookings)));
        $output->writeln(sprintf('Unique customers: %s', $count));
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 04.05.18
 * Time: 16:38
 */

namespace AppBundle\Command;


use AppBundle\DataProvider\CustomerDataProvider;
use AppBundle\DataProvider\OrderDataProvider;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportOldDataCommand extends ContainerAwareCommand
{
    private $customerMap;

    private $processedBookings = [];

    protected function configure()
    {
        $this->setName('app:import:old_data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $count = 0;
        $bCount = 0;
        $languages = CustomerDataProvider::languages();
        $bookings = $this->getBookings();

        while (!empty($bookings)) {
            $this->createCustomerMap();
            /** @var Booking $booking */
            foreach ($bookings as $booking) {
                $output->writeln(sprintf('Processing booking #%s', $booking->getId()));

                $this->processedBookings[] = $booking->getId();
                $bCount++;

                $langKey = $booking->getLanguage();
                $fname = trim($booking->getLesseeFirstName());
                $fnameFlag = preg_replace('/[^A-Za-z]/', '', $fname);
                $lname = trim($booking->getLesseeLastName());
                $lnameFlag = preg_replace('/[^A-Za-z]/', '', $lname);
                $phone = $booking->getLesseePhone();
                $phone = preg_replace('/[^\d+]/', '', $phone);
                $email = filter_var(trim($booking->getLesseeEmail()), FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
                $lang = array_key_exists(strtolower($langKey), $languages) ? strtolower($langKey) : CustomerDataProvider::LANG_EN;

                if (empty($phone)) {
                    $output->writeln('SKIP: empty phone number');
                    continue;
                }

                if (empty($fnameFlag) || empty($lnameFlag)) {
                    $output->writeln('SKIP: empty first name or last name');
                    continue;
                }

                if (array_key_exists($phone, $this->customerMap)) {
                    $customer = $em->getReference(Customer::class, $this->customerMap[$phone]);
                } else if (array_key_exists($email, $this->customerMap)) {
                    $customer = $em->getReference(Customer::class, $this->customerMap[$email]);
                } else if (mb_strlen($phone) < 9 && array_key_exists($phone.'|'.$fname.'|'.$lname, $this->customerMap)) {
                    $customer = $em->getReference(Customer::class, $this->customerMap[$phone.'|'.$fname.'|'.$lname]);
                } else {
                    $output->writeln('Create customer');
                    $count++;
                    $customer = new Customer();
                    $customer->setCountry(CustomerDataProvider::COUNTRY_OTHER);
                    $customer->setEmail($email);
                    $customer->setFirstName($fname);
                    $customer->setLastName($lname);
                    $customer->setLanguage($lang);
                    $customer->setPhoneNumber($phone);
                    $em->persist($customer);
                    $em->flush();
                    if (!empty($email)) {
                        $this->customerMap[$email] = $customer->getId();
                    }
                    if (mb_strlen($phone) < 9) {
                        $this->customerMap[$phone.'|'.$fname.'|'.$lname] = $customer->getId();
                    } else {
                        $this->customerMap[$phone] = $customer->getId();
                    }
                }
                $output->writeln('Create order');
                $order = new Order();
                $order->setDate($booking->getDate());
                $order->setBoat($booking->getBoat());
                $order->setBookedBy($booking->getBookedBy());
                $order->setCancellation($booking->getCancellation());
                $order->setComments($booking->getComments());
                $order->setCommission($booking->getCommission());
                $order->setCommissionPaidTo($booking->getCommissionPaidTo());
                $order->setCustomer($customer);
                $order->setDamage($booking->getDamage());
                $order->setDamageAmount($booking->getDamageAmount());
                $order->setDeposit($booking->getDeposit());

                $end = clone $booking->getEnd();
                $end->setDate(1970, 1, 1);
                $order->setEnd($end);

                $order->setHandler($booking->getHandler());
                $order->setKickback($booking->getKickback());
                $order->setLocation($booking->getLocation());
                $order->setNumberOfPeople($booking->getNumberOfPeople());
                $order->setPaymentMethodDamage(OrderDataProvider::guessPaymentMethod($booking->getPaymentMethodDamage()));
                $order->setPaymentMethodDeposit(OrderDataProvider::guessPaymentMethod($booking->getPaymentMethodDeposit()));
                $order->setPaymentMethodRent(OrderDataProvider::guessPaymentMethod($booking->getPaymentMethodRent()));
                $order->setPetrolCost($booking->getPetrolCost());
                $order->setRent($booking->getRent());
                $order->setRentDiscount($booking->getRentDiscount());

                $start = clone $booking->getStart();
                $start->setDate(1970, 1, 1);
                $order->setStart($start);

                $order->setStatus($booking->getStatus());
                $order->setType($booking->getType());

                $booking->setImported(true);
                $em->persist($order);
                $em->flush();
            }
            $em->clear();
            $bookings = $this->getBookings();
        }

        $output->writeln(sprintf('Total bookings: %s', $bCount));
        $output->writeln(sprintf('Unique customers: %s', $count));
    }

    private function createCustomerMap()
    {
        $this->customerMap = [];
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $customers = $em->getRepository(Customer::class)->findAll();
        /** @var Customer $customer */
        foreach ($customers as $customer) {
            if (!empty($customer->getEmail())) {
                $this->customerMap[$customer->getEmail()] = $customer->getId();
            }
            $key = $customer->getPhoneNumber();
            if (mb_strlen($key) < 9) {
                $key = $customer->getPhoneNumber().'|'.$customer->getFirstName().'|'.$customer->getLastName();
            }
            $this->customerMap[$key] = $customer->getId();
        }
    }

    private function getBookings()
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $qb = $em->getRepository(Booking::class)->createQueryBuilder('b');
        $qb->select('b');
        $qb->where($qb->expr()->eq('b.imported', ':imported'));
        if (!empty($this->processedBookings)) {
            $qb->andWhere($qb->expr()->notIn('b.id', ':processed'));
            $qb->setParameter('processed', $this->processedBookings);
        }
        $qb->setMaxResults(500);
        $qb->setParameter('imported', false);

        return $qb->getQuery()->getResult();
    }
}
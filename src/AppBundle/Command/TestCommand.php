<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 06.05.18
 * Time: 14:57
 */

namespace AppBundle\Command;


use AppBundle\DataProvider\DateTimeProvider;
use AppBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
//        $order = $em->getRepository(Order::class)->find(1);

        /** @var \DateTime $s */
//        $s = $order->getStart();
//        $start = clone $s;
//        $start->setTimezone(new \DateTimeZone('UTC'));
//        $start->setTime(intval($s->format('G')), intval($s->format('i')), intval($s->format('s')));
//        dump($s);
//        dump($start);

        $s = new \DateTime();
        $d = DateTimeProvider::utc($s);
        $d->setTimestamp(36000);
        dump($d);
    }
}
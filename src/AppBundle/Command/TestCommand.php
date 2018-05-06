<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 06.05.18
 * Time: 14:57
 */

namespace AppBundle\Command;


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
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /** @var Order $order */
        $order = $em->getRepository(Order::class)->find(83);
        $output->writeln($order->getMinutes());
        dump($order);
    }
}
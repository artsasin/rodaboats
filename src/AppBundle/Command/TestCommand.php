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
        $message = \Swift_Message::newInstance()
            ->setSubject("RodaBoats: test message")
            ->setFrom("mailgun@mg.artsasin.info")
            ->setTo(['artem-sasin@yandex.ru'])
            ->setBody('<h1>Hello from MailGun!</h1>', 'text/html');

        $c = $this->getContainer();

        $mailer = $c->get('mailer');
        $mailer->send($message);

        $spool = $mailer->getTransport()->getSpool();
        $transport = $c->get('swiftmailer.mailer.default.transport.real');
        $spool->flushQueue($transport);
    }
}
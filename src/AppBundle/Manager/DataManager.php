<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 17.04.18
 * Time: 20:00
 */

namespace AppBundle\Manager;

use AppBundle\DataProvider\OrderDataProvider;
use AppBundle\Entity\Boat;
use AppBundle\Entity\Customer;
use AppBundle\Entity\EmailTemplate;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderLog;
use AppBundle\Exception\RodaboatsException;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Model\DTO;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DataManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * DataManager constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param  \Swift_Mailer $mailer
     * @param EngineInterface $twig
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        \Swift_Mailer $mailer,
        EngineInterface $twig,
        FormFactoryInterface $formFactory
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    /**
     * @return \AppBundle\Repository\OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->em->getRepository(Order::class);
    }

    /**
     * @return \AppBundle\Repository\BoatRepository
     */
    public function getBoatRepository()
    {
        return $this->em->getRepository(Boat::class);
    }

    /**
     * @return \AppBundle\Repository\CustomerRepository
     */
    public function getCustomerRepository()
    {
        return $this->em->getRepository(Customer::class);
    }

    /**
     * @return \AppBundle\Repository\EmailTemplateRepository
     */
    public function getEmailTemplateRepository()
    {
        return $this->em->getRepository(EmailTemplate::class);
    }

    /**
     * @return \AppBundle\Repository\OrderLogRepository
     */
    public function getOrderLogRepository()
    {
        return $this->em->getRepository(OrderLog::class);
    }

    /**
     * @param DTO\Order $model
     * @return Order|null
     * @throws RodaboatsException
     */
    public function saveOrder(DTO\Order $model)
    {
        $order = null;

        if ($model->isValid()) {
            $order = new Order();
            if ($model->id) {
                $order = $this->getOrderRepository()->find($model->id);
            }

            if (!$order instanceof Order) {
                throw new RodaboatsException('Order is not found');
            }

            $boat = $this->getBoatRepository()->find($model->boatId);
            if (!$boat instanceof Boat) {
                throw new RodaboatsException('Boat is not found');
            }

            $customer = $this->getCustomerRepository()->find($model->customerId);
            if (!$customer instanceof Customer) {
                throw new RodaboatsException('Customer is not found');
            }

            if ($order->getCustomer() !== $customer) {
                if ($order->getId() !== null) {
                    $order->getCustomer()->removeOrder($order);
                }
                $order->setCustomer($customer);
            }

            if ($order->getBoat() !== $boat) {
                if ($order->getId() !== null) {
                    $order->getBoat()->removeOrder($order);
                    $order->getLocation()->removeOrder($order);
                }
                $order->setBoat($boat);
                $order->setLocation($boat->getLocation());
            }

            $order->setDate(new \DateTime($model->date));
            $start = new \DateTime();
            $start->setTimestamp($model->start);
            $order->setStart($start);
            $end = new \DateTime();
            $end->setTimestamp($model->end);
            $order->setEnd($end);

            $order->setType($model->type);
            $order->setNumberOfPeople($model->numberOfPeople);
            $order->setBookedBy($model->bookedBy);
            $order->setExtra($model->extra);

            $order->setRent($model->rent);
            $order->setRentDiscount($model->rentDiscount);
            $order->setPaymentMethodRent($model->paymentMethodRent);
            $order->setPetrolCost($model->petrolCost);
            $order->setDeposit($model->deposit);
            $order->setPaymentMethodDeposit($model->paymentMethodDeposit);

            $order->setComments($model->comments);

            if ($order->getId() === null) {
                $user = $this->tokenStorage->getToken()->getUser();
                $order->setHandler($user);
            }

            if ($order->getId() !== null) {
                $order->setStatus($model->status);
                $order->setCommission($model->commission);
                $order->setCommissionPaidTo($model->commissionPaidTo);
                $order->setDamage($model->damage);
                $order->setDamageAmount($model->damageAmount);
                $order->setPaymentMethodDamage($model->paymentMethodDamage);
                $order->setCancellation($model->cancellation);
            }

            $this->em->persist($order);
            $this->em->flush();
        }
        return $order;
    }

    /**
     * @param DTO\Order $order
     * @return Order[]
     * @throws RodaboatsException
     */
    public function isConflicts(DTO\Order $order)
    {
        try {
            $date = new \DateTime($order->date);
        } catch (\Exception $e) {
            throw new RodaboatsException('Order date is invalid');
        }

        try {
            $start = new \DateTime();
            $start->setTimestamp($order->start);
        } catch (\Exception $e) {
            throw new RodaboatsException('Order start has incorrect value');
        }

        try {
            $end = new \DateTime();
            $end->setTimestamp($order->end);
        } catch (\Exception $e) {
            throw new RodaboatsException('Order end has incorrect value');
        }


        $qb = $this->getOrderRepository()->createQueryBuilder('orders');

        $qb->andWhere($qb->expr()->eq('orders.date', ':date'));
        $qb->setParameter('date', $date);

        $qb->andWhere($qb->expr()->eq('orders.boat', ':boat'));
        $qb->setParameter('boat', $order->boatId);

        $qb->andWhere($qb->expr()->in('orders.status', [
            OrderDataProvider::STATUS_CONFIRMED,
            OrderDataProvider::STATUS_DELIVERED,
            OrderDataProvider::STATUS_CLOSED
        ]));

        if ($order->type === OrderDataProvider::TYPE_FISHING) {
            $qb->andWhere($qb->expr()->neq('orders.type', ':type'));
            $qb->setParameter('type', OrderDataProvider::TYPE_FISHING);
        }

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gt('orders.start', ':start'),
                    $qb->expr()->lt('orders.start', ':end')
                ),
                $qb->expr()->andX(
                    $qb->expr()->gt('orders.end', ':start'),
                    $qb->expr()->lt('orders.end', ':end')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('orders.start', ':start'),
                    $qb->expr()->gte('orders.end', ':end')
                )
            )
        );
        $qb->setParameter('start', $start);
        $qb->setParameter('end', $end);

        if ($order->id !== null) {
            $qb->andWhere($qb->expr()->neq('orders.id', ':id'));
            $qb->setParameter('id', $order->id);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Order $order
     * @param array $recipients
     * @return bool
     */
    public function notifyStaff(Order $order, $recipients = [])
    {
        $location = $order->getLocation();
        if ($location != null) {
            $recipient = $location->getNotificationEmail();
            if (!empty($recipient)) {
                $recipients[] = $recipient;
            }
        }

        if (count($recipients) === 0) {
            return false;
        }

        // Render the message body.
        $body = $this->twig->render('emails/newbooking.html.twig', array(
            'order' => $order
        ));
        $subject = sprintf("New booking of %s | %s %s - %s | %s", $order->getBoat()->getName(),
            $order->getDate()->format('l j M Y'),
            $order->getStart()->format('G:i'),
            $order->getEnd()->format('G:i'),
            $order->getCustomer()->getFirstName() . ' ' . $order->getCustomer()->getLastName());

        // Compose and send the message.
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom("info@rodaboats.eu")
            ->setTo($recipients)
            ->setBody($body, 'text/html');

        $result = $this->mailer->send($message);

        return ($result === count($recipients));
    }

    public function notifyCustomer(Order $order)
    {
        // Don't send the notification if the customer does not have an email
        $recipient = $order->getCustomer()->getEmail();
        if (empty($recipient)) {
            return false;
        }

        // Lookup the template
        $template = $this->getEmailTemplateRepository()->findOneBy(array(
            'name'      => 'TEMPLATE_CONFIRMATION',
            'language'  => strtoupper($order->getCustomer()->getLanguage())
        ));

        // Render the message body.
        $body = $this->twig->render('emails/bookingconfirmation.html.twig', array(
            'order'     => $order,
            'template'  => $template
        ));
        $subject = sprintf("Confirmation of booking | %s %s - %s | %s",
            $order->getDate()->format('l j M Y'),
            $order->getStart()->format('G:i'),
            $order->getEnd()->format('G:i'),
            $order->getCustomer()->getFirstName() . ' ' . $order->getCustomer()->getLastName()
        );

        // Compose and send the message.
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom("info@rodaboats.eu")
            ->setTo($recipient)
            ->setBody($body, 'text/html');

        $result = $this->mailer->send($message);

        return ($result === 1);
    }

    public function notifyOrderCancellation(Order $order, array $recipients = [])
    {
        // Do not send cancellation notices for bookings in the past.
        if ($order->getDate() < new \DateTime(date('Y-m-d'))) {
            return false;
        }


        // Don't send the notification if the location has no proper notification address.
        $location = $order->getLocation();
        if ($location != null) {
            $recipient = $location->getNotificationEmail();
            if (!empty($recipient)) {
                $recipients[] = $recipient;
            }
        }

        if (count($recipients) === 0) {
            return false;
        }

        // Render the message body.
        $body = $this->twig->render('emails/bookingcancelled.html.twig', array(
            'order' => $order
        ));
        $subject = sprintf("Booking cancellation of %s | %s %s - %s | %s", $order->getBoat()->getName(),
            $order->getDate()->format('l j M Y'),
            $order->getStart()->format('G:i'),
            $order->getEnd()->format('G:i'),
            $order->getCustomer()->getFirstName() . ' ' . $order->getCustomer()->getLastName());

        // Compose and send the message.
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom("info@rodaboats.eu")
            ->setTo($recipients)
            ->setBody($body, 'text/html');

        $result = $this->mailer->send($message);

        return ($result === count($recipients));
    }

    /**
     * @param Customer $customer
     */
    public function reNotifyCustomer(Customer $customer)
    {
        $qb = $this->getOrderRepository()->createQueryBuilder('o');
        $qb->where($qb->expr()->eq('o.customer', ':customer'));
        $qb->andWhere($qb->expr()->in('o.status', ':status'));
        $qb->andWhere($qb->expr()->gte('o.date', ':date'));

        $qb->setParameter('customer', $customer);
        $qb->setParameter('status', [OrderDataProvider::STATUS_CONFIRMED]);
        $qb->setParameter('date', new \DateTime('now'));

        $orders = $qb->getQuery()->getResult();
        foreach ($orders as $order) {
            $this->notifyCustomer($order);
        }
    }

    /**
     * @param $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function boatPaxDeclarationForm(array $options = [])
    {
        $data = [
            'boat'          => null,
            'start_date'    => new \DateTime(),
            'end_date'      => new \DateTime(),
            'print'         => false
        ];

        $fb = $this->formFactory->createBuilder('form', $data, $options);

        $fb->add('boat', 'entity', [
            'class'         => 'AppBundle:Boat',
            'choice_label'  => 'name',
            'required'     => false,
            'constraints' => array(
                new NotBlank()
            )
        ]);
        $fb->add('start_date', 'date');
        $fb->add('end_date', 'date');
        $fb->add('print', 'checkbox', [
            'required' => false
        ]);
        $fb->add('save', 'submit', array('label' => 'Execute'));

        return $fb->getForm();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 12.04.18
 * Time: 0:13
 */

namespace AppBundle\Menu;


use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function sideMenuCommon(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class' => 'nav',
                'id'    => 'side-menu'
            )
        ));

        $dashboard = $menu->addChild('Dashboard', array('route' => 'calendar'));
        $dashboard->setLabel('<i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;Dashboard');
        $dashboard->setExtra('safe_label', true);

        $bookings = $menu->addChild('Bookings', array('route' => 'booking'));
        $bookings->setLabel('<i class="fa fa-handshake-o" aria-hidden="true"></i>&nbsp;Bookings');
        $bookings->setExtra('safe_label', true);

        $orders = $menu->addChild('Orders', array('route' => 'app_order_index'));
        $orders->setLabel('<i class="fa fa-handshake-o" aria-hidden="true"></i>&nbsp;Orders');
        $orders->setExtra('safe_label', true);

        $customers = $menu->addChild('Customers', array('route' => 'app.customer.index'));
        $customers->setLabel('<i class="fa fa-users" aria-hidden="true"></i>&nbsp;Customers');
        $customers->setExtra('safe_label', true);

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function sideMenuBoat(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class' => 'nav',
                'id'    => 'side-menu',
                'style' => 'margin-top: 10px;'
            )
        ));

        $boats = $menu->addChild('Boats', array('route' => 'boat'));
        $boats->setLabel('<i class="fa fa-ship" aria-hidden="true"></i>&nbsp;Boats');
        $boats->setExtra('safe_label', true);

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function sideMenuReport(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class' => 'nav',
                'id'    => 'side-menu',
                'style' => 'margin-top: 10px;'
            )
        ));

        $reports = $menu->addChild('Reports', array('route' => 'report'));
        $reports->setLabel('<i class="fa fa-database" aria-hidden="true"></i>&nbsp;Reports');
        $reports->setExtra('safe_label', true);

        $daily = $menu->addChild('Daily report', array('route' => 'reportdaily'));
        $daily->setLabel('<i class="fa fa-calendar-check-o" aria-hidden="true"></i>&nbsp;Daily report');
        $daily->setExtra('safe_label', true);

        $month = $menu->addChild('Month report', array('route' => 'reportmonth'));
        $month->setLabel('<i class="fa fa-calendar-o" aria-hidden="true"></i>&nbsp;Month report');
        $month->setExtra('safe_label', true);

        $boatPaxDeclaration = $menu->addChild('Boat Pax Declaration', array('route' => 'app.report.boat_pax_declaration_form'));
        $boatPaxDeclaration->setLabel('<i class="fa fa-users" aria-hidden="true"></i>&nbsp;Pax declaration');
        $boatPaxDeclaration->setExtra('safe_label', true);

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function sideMenuAdmin(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class' => 'nav',
                'id'    => 'side-menu',
                'style' => 'margin-top: 10px;'
            )
        ));

        $admin = $menu->addChild('Daily report', array('route' => 'admin'));
        $admin->setLabel('<i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;Admin');
        $admin->setExtra('safe_label', true);

        return $menu;
    }
}
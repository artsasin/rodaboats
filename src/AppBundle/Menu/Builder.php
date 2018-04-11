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
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class' => 'nav',
                'id'    => 'side-menu'
            )
        ));

        $dashboard = $menu->addChild('Dashboard', array('route' => 'calendar'));
        $dashboard->setLabel('<i class="fa fa-dashboard fa-fw" aria-hidden="true"></i>&nbsp;Dashboard');
        $dashboard->setExtra('safe_label', true);

        $customers = $menu->addChild('Customers', array('route' => 'app.customer.index'));
        $customers->setLabel('<i class="fa fa-users" aria-hidden="true"></i>&nbsp;Customers');
        $customers->setExtra('safe_label', true);

        return $menu;
    }
}
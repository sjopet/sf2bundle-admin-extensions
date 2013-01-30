<?php
/**
 * (c) Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\Bundle\AdminExtensionsBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;


class GroupAdminListExtension extends AdminExtension
{
    /** @var AdminInterface $adminFacade */
    protected $adminFacade;

    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $adminFacade
     */
    public function __construct(AdminInterface $adminFacade)
    {
        $this->adminFacade = $adminFacade;
    }

    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        /** @var RouteCollection $routes */
        $routes = $this->adminFacade->getRoutes();

        $rc = new RouteCollection($collection->getBaseCodeRoute(), $routes->getBaseRouteName(), $routes->getBaseRoutePattern(), $routes->getBaseControllerName());
        $rc->add('list', '/list');

        /*
         * @todo it works but wtf @ the url's that are generated from this
         * @todo also the showAction override in controller is bs and should not be needed
         */

        $collection->remove('list');
        $collection->addCollection($rc);
    }
}

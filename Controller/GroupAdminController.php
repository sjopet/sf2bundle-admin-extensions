<?php
/*
* (c) Netvlies Internetdiensten
*
* Richard van den Brand <richard@netvlies.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Netvlies\Bundle\AdminExtensionsBundle\Controller;

use Netvlies\Bundle\OmsBundle\Controller\CRUDController;

class GroupAdminController extends CRUDController
{
    /**
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $types = $this->admin->getSubClasses();

        return $this->render('NetvliesOmsBundle:Sonata:Admin/Group/create_type_selection.html.twig', array(
            'types'         => $types,
            'base_template' => $this->getBaseTemplate(),
            'admin'         => $this->admin,
            'action'        => 'create'
        ));
    }

    public function showAction($id = null)
    {
        return parent::listAction();
    }
}

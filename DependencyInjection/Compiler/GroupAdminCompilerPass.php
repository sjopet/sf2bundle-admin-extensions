<?php
/**
* (c) Netvlies Internetdiensten
*
* @author Sjoerd Peters <speters@netvlies.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Netvlies\Bundle\AdminExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Netvlies\Bundle\AdminExtensionsBundle\DependencyInjection\Compiler\SonataAdminExtensionCompilerPass;

class GroupAdminCompilerPass extends SonataAdminExtensionCompilerPass
{
    public function process(ContainerBuilder $container)
    {

        $groups = array();
        $extensionId = 'netvlies.admin.group.extension';
        $extension = $container->getDefinition($extensionId);

        foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $tags) {
            foreach ($tags as $tag) {
                if(!isset($tag['list_owner']) || $tag['list_owner'] != 'true'){
                    continue(2);
                }
            }

            $subClasses = array();
            $owner = $container->getDefinition($id);
            $superClass = $this->getManagedClass($owner, $container);

            foreach ($container->findTaggedServiceIds('sonata.admin') as $subId => $subTags) {
                $admin = $container->getDefinition($subId);
                if($this->adminAddSubClassExtension($admin, $container, $superClass, $extensionId)){
                    $subClass = $this->getManagedClass($admin, $container);
                    $subClasses[$subClass] = $admin; // add to subclasses for group-admin
                    $groups[$subClass] = $owner; //add to groups for group-admin-extension

                    $admin->clearTag('sonata.admin');
                    $subTags[0]['show_in_dashboard'] = false;
                    $admin->addTag('sonata.admin', $subTags[0]);
                }
            }
            $owner->addMethodCall('setSubClasses', array($subClasses));
        }
        $extension->addMethodCall('setGroups', array($groups));
    }
}

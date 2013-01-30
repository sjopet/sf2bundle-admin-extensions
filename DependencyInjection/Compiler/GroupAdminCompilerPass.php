<?php

namespace Netvlies\Bundle\AdminExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class GroupAdminCompilerPass implements  CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $extensionServiceId = 'netvlies.admin.group.extension';
        $extension = $container->getDefinition($extensionServiceId);
        foreach ($container->findTaggedServiceIds('netvlies.group_admin') as $id => $tags) {
            $baseAdmin = $container->getDefinition($id);
            $extension->addArgument($baseAdmin); // @todo this way we can only support 1 group_admin
            $baseClass = $baseAdmin->getArgument(1);

            $subs = array();
            foreach ($container->findTaggedServiceIds('sonata.admin') as $subId => $subTags) {
                if(!$container->hasDefinition($subId)){
                    continue;
                }
                $admin = $container->getDefinition($subId); // @todo maybe do checks on showOnDashboard etc..

                $subClass = $admin->getArgument(1);
                if(!class_exists($subClass)){
                    continue;
                }
                $class = new \ReflectionClass($admin->getArgument(1));
                if($class->isSubclassOf($baseClass)){
                    $subs[$class->getName()] = new Reference($subId);
                    $admin->addMethodCall('addExtension', array(new Reference($extensionServiceId)));
                }
            }
            $baseAdmin->addMethodCall('setSubClasses', array($subs));
        }
    }
}

<?php

namespace Netvlies\Bundle\AdminExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class GroupAdminCompilerPass implements  CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $admins = array(
            'symfony_cmf_block.simple_admin' => 'Symfony\Cmf\Bundle\BlockBundle\Document\SimpleBlock',
            'symfony_cmf_block.container_admin' => 'Symfony\Cmf\Bundle\BlockBundle\Document\ContainerBlock',
            'symfony_cmf_block.reference_admin' => 'Symfony\Cmf\Bundle\BlockBundle\Document\ReferenceBlock',
            'symfony_cmf_block.action_admin' => 'Symfony\Cmf\Bundle\BlockBundle\Document\ActionBlock',
        );

        $extensionServiceId = 'netvlies.admin.group.extension';
        $extension = $container->getDefinition($extensionServiceId);
        foreach ($container->findTaggedServiceIds('netvlies.group_admin') as $id => $tags) {
            $baseAdmin = $container->getDefinition($id);
            $extension->addArgument($baseAdmin); // @todo this way we can only support 1 group_admin
            $baseClass = $baseAdmin->getArgument(1);

            $subs = array();
            foreach ($admins as $subId => $subClass) {
                $subAdmin = $container->getDefinition($subId);
                $subs[$subClass] = new Reference($subId);
                $subAdmin->addMethodCall('addExtension', array(new Reference($extensionServiceId)));
            }
            $baseAdmin->addMethodCall('setSubClasses', array($subs));
        }
    }

    public function process2(ContainerBuilder $container)
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

                // This fails when the class is set through a parameter
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

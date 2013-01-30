<?php

namespace Netvlies\Bundle\AdminExtensionsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Netvlies\Bundle\AdminExtensionsBundle\DependencyInjection\Compiler\GroupAdminCompilerPass;

class NetvliesAdminExtensionsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new GroupAdminCompilerPass());
    }
}

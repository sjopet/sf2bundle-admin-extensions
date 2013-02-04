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

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Abstract class for handling sonata admin extensions.
 * The extension must be added to the dependency container
 *
 * <code>
 * use Symfony\Component\DependencyInjection\ContainerBuilder;
 *
 * class SonataListOrderingActionsCompilerPass extends OmsSonataAdminExtensionCompilerPass
 * {
 *     public function process(ContainerBuilder $container)
 *     {
 *         foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $tags) {
 *             $this->adminAddInterfaceExtension(
 *                 $container->getDefinition($id),
 *                 $container,
 *                 '\Netvlies\Bundle\OmsBundle\Document\OrderingInterface',
 *                 'netvlies_oms.list_orderingactions.admin_extension'
 *             );
 *         }
 *     }
 * }
 * </code>
 */
abstract class SonataAdminExtensionCompilerPass implements CompilerPassInterface
{
    /**
     * Adds the extension based on the managed class implementing the given interface
     * Skipps addition if the admin's config has disabled extending explicitly
     *
     * @param \Symfony\Component\DependencyInjection\Definition $admin
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $interfaceName
     * @param $extensionId
     * @return bool
     */
    protected function adminAddInterfaceExtension(
        Definition $admin, ContainerBuilder $container, $interfaceName, $extensionId
    ) {
        if ($this->adminSkipExtending($admin)) {
            return false;
        }

        $class = $this->getManagedClass($admin, $container);

        if ($this->classHasInterface($class, $interfaceName)) {
            $this->addExtension($admin, $extensionId);
            return true;
        }
        return false;
    }

    /**
     * Adds the extension based on the managed class extending the given superclass
     * Skipps addition if the admin's config has disabled extending explicitly
     *
     * @param \Symfony\Component\DependencyInjection\Definition $admin
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $superClassName
     * @param string $extensionId
     * @return bool
     */
    protected function adminAddSubClassExtension(
        Definition $admin, ContainerBuilder $container, $superClassName, $extensionId
    ) {
        if ($this->adminSkipExtending($admin)) {
            return false;
        }

        $class = $this->getManagedClass($admin, $container);

        if ($this->classIsSubOf($class, $superClassName)) {
            $this->addExtension($admin, $extensionId);
            return true;
        }
        return false;
    }

    /**
     * Checks if it has a tag extensions and if extensions are forced disabled
     *
     * @param $admin
     * @return bool
     */
    protected final function adminSkipExtending(Definition $admin)
    {
        return $admin->hasTag('extensions') && ($admin->getTag('extensions') == false);
    }

    /**
     * Resolves the class argument of the admin to an actual class (in case of %parameter%)
     *
     * @param                                                         $admin
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return string
     */
    protected final function getManagedClass(Definition $admin, ContainerBuilder $container)
    {
        return $container->getParameterBag()->resolveValue($admin->getArgument(1));
    }

    /**
     * Checks if the class has a certain interface implemented
     *
     * @param $class
     * @param $interface
     *
     * @return bool
     */
    protected final function classHasInterface($class, $interface)
    {
        $reflection = new \ReflectionClass($class);
        return $reflection->implementsInterface($interface);
    }

    /**
     * Checks if the class extends a certain Super class
     *
     * @param $class
     * @param $superClass
     *
     * @return bool
     */
    protected final function classIsSubOf($class, $superClass)
    {
        $reflection = new \ReflectionClass($class);
        return $reflection->isSubclassOf($superClass);
    }

    /**
     * Adds the extension reference to the admin
     *
     * @param $admin
     * @param $extensionId
     */
    protected final function addExtension(Definition $admin, $extensionId)
    {
        $admin->addMethodCall('addExtension', array(new Reference($extensionId)));
    }
}

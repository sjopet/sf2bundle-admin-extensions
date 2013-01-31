<?php
/**
* (c) Netvlies Internetdiensten
*
* @author Sjoerd Peters <speters@netvlies.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Netvlies\Bundle\AdminExtensionsBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Sonata\DoctrinePHPCRAdminBundle\Datagrid\ProxyQuery;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use Netvlies\Bundle\AdminExtensionsBundle\Datagrid\TypeFieldDescription;

class GroupAdmin extends Admin
{
    /** @var $listFieldTemplate string */
    protected $listFieldTemplate = 'NetvliesAdminExtensionsBundle:Sonata:Admin/CRUD/base_list_field.html.twig';

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', 'doctrine_phpcr_string', array('label' => 'ID'))
            ->add('type', 'doctrine_phpcr_string', array('label' => 'Type'))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $typeField = new TypeFieldDescription();
        $typeField->setFieldName('type');

        $listMapper
            ->addIdentifier('id', 'text', array('label' => 'ID', 'template' => $this->listFieldTemplate))
            ->add($typeField, 'text', array('label' => 'Type', 'template' => $this->listFieldTemplate))
        ;
    }

    /**
     * @param string $context
     * @return ProxyQuery
     */
    public function createQuery($context = 'list')
    {
        $dm = $this->getModelManager()->getDocumentManager();
        /** @var \Doctrine\ODM\PHPCR\Query\QueryBuilder $qb */
        $qb = $dm->createQueryBuilder();
        $query = new ProxyQuery($qb);
        $query->setDocumentManager($dm);
        $qb->nodeType('nt:unstructured');

        foreach ($this->getSubClasses() as $class => $admin) {
            $exp = $qb->expr()->eq('phpcr:class', $class);
            $qb->orWhere($exp);
        }
        return $query;
    }

    /**
     * @param $name
     * @return string
     */
    public function getSubAdmin($name)
    {
        if(is_object($name)){
            $name = ($name instanceof \Doctrine\ODM\PHPCR\Proxy\Proxy) ? get_parent_class($name) : get_class($name);
        }
        return parent::getSubClass($name);
    }

    /**
     * Because of the structure of the subclasses array
     * We need to change this function slightly
     *
     * @param  string $name The name of the sub class
     * @return string the subclass
     */
    protected function getSubClass($name)
    {
        if ($this->hasSubClass($name)) {
            return $name;
        }

        return null;
    }

}

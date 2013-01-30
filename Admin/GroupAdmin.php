<?php
/*
* (c) Netvlies Internetdiensten
*
* Sven de Bie <sven@netvlies.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Netvlies\Bundle\AdminExtensionsBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\Query\QOM\QueryObjectModelConstantsInterface as Constants;

//use Netvlies\Bundle\AdminExtensionsBundle\Datagrid\ProxyQuery;
use Sonata\DoctrinePHPCRAdminBundle\Datagrid\ProxyQuery;
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
            ->add('title', 'doctrine_phpcr_string', array('label' => 'Titel'))
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
            ->addIdentifier('title', 'text', array('label' => 'Titel', 'template' => $this->listFieldTemplate))
            ->add($typeField, 'text', array('label' => 'Type', 'template' => $this->listFieldTemplate))
        ;
    }

    /**
     * @param string $context
     * @return \Netvlies\Bundle\AdminExtensionsBundle\Datagrid\ProxyQuery
     */
    public function createQuery($context = 'list')
    {
        $dm = $this->getModelManager()->getDocumentManager();
        $qb = $dm->createQueryBuilder();
//        $qmf = $qb->getQOMFactory();
        $query = new ProxyQuery($qb);
        $query->setDocumentManager($dm);

        $constraint = null;
        echo "<pre>";
        var_dump($this->getSubClasses());
        echo "</pre>";
        die;
        foreach ($this->getSubClasses() as $class => $admin) {
            $condition = $qb->comparison($qb->propertyValue('phpcr:class'), Constants::JCR_OPERATOR_EQUAL_TO, $qb->literal($class));
            if ($constraint) {
                $constraint = $qb->orConstraint($constraint, $condition);
            } else {
                $constraint = $condition;
            }
        }
//        $qb->from($qb->selector('nt:unstructured'));
        $qb->andWhere($constraint);

        return $query;
    }

    /**
     * @param $name
     * @return string
     */
    public function getSubAdmin($name)
    {
        if(is_object($name)){
            $name = get_class($name);
        }
        return parent::getSubClass($name);
    }

}

<?php
namespace CongNo\Form;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Form\Form;
use CongNo\Entity\CongNo;

class CongNoFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('cong-no');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new CongNo());

        $this->add(array(
             'name' => 'idCongNo',
             'type' => 'Hidden',
        ));

        $this->add(array(
             'name' => 'idDoiTac',
             'type' => 'Hidden',
        ));

        $this->add(array(
             'name' => 'ki',
             'type' => 'Date',
             'options' => array(                 
             ),
             'attributes'=>array(
             	'id'=>'ki'                
            ),
         ));        

        $this->add(array(
             'name' => 'noDauKi',
             'type' => 'Number',             
             'attributes'=>array(                
                'id'=>'noDauKi',
            ),
        ));

        $this->add(array(
             'name' => 'noPhatSinh',
             'type' => 'Number',             
             'attributes'=>array(                
                'id'=>'noPhatSinh',
            ),
        ));

        $this->add(array(
             'name' => 'duNo',
             'type' => 'Number',             
             'attributes'=>array(                
                'id'=>'duNo',
            ),
        ));
    }
}
?>
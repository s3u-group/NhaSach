<?php
namespace CongNo\Form;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Form\Form;
use CongNo\Entity\PhieuThu;
use CongNo\Form\CongNoFieldset;
use Application\Form\SystemUserFieldset;

class PhieuThuFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('phieu-thu');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new PhieuThu());

        $congNoFieldset = new CongNoFieldset($objectManager);
        $congNoFieldset->setUseAsBaseFieldset(true);
        $congNoFieldset->setName('idCongNo');
        $this->add($congNoFieldset);

        /*$systemUserFieldset = new SystemUserFieldset($objectManager);
        $systemUserFieldset->setUseAsBaseFieldset(true);
        $systemUserFieldset->setName('idUserNv');        
        $this->add($systemUserFieldset);*/

        $this->add(array(
             'name' => 'idUserNv',
             'type' => 'Hidden',
        ));

        $this->add(array(
             'name' => 'idPhieuThu',
             'type' => 'Hidden',             
        ));  

        $this->add(array(
             'name' => 'maPhieuThu',
             'type' => 'Hidden',             
        ));      

        $this->add(array(
             'name' => 'lyDo',
             'type' => 'Text',
             'options' => array(                 
             ),
             'attributes'=>array(
                'id'=>'lyDo',
                'class'=>'h5a-input form-control input-sm',               
            ),
         ));

        $this->add(array(
             'name' => 'soTien',
             'type' => 'Number',             
             'attributes'=>array(                
                'id'=>'soTien',
                'min'=>0,
                'class'=>'h5a-input form-control input-sm',
            ),
        ));
        
        $this->add(array(
             'name' => 'ngayThanhToan',
             'type' => 'Date',             
             'attributes'=>array(               
                'id'=>'ngayThanhToan',                
                'class'=>'h5a-input form-control input-sm',
            ),
        ));        
    }
     public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
?>
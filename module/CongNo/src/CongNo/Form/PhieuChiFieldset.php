<?php
namespace CongNo\Form;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Form\Form;
use CongNo\Entity\PhieuChi;
use CongNo\Form\CongNoFieldset;
use Zend\ServiceManager\ServiceManager;

class PhieuChiFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('phieu-chi');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new PhieuChi());

        $this->add(array(
             'name' => 'idPhieuChi',
             'type' => 'Hidden',
        ));
         $this->add(array(
             'name' => 'maPhieuChi',
             'type' => 'Hidden',
        ));

        $congNoFieldset = new CongNoFieldset($objectManager);
        $congNoFieldset->setUseAsBaseFieldset(true);
        $congNoFieldset->setName('idCongNo');
        $this->add($congNoFieldset);      
        
        $this->add(array(
             'name' => 'idUserNv',
             'type' => 'Hidden',
        ));        

        $this->add(array(
             'name' => 'lyDo',
             'type' => 'Text',
             'options' => array(                 
             ),
             'attributes'=>array(
                'id'=>'lyDo'                
            ),
         ));

        $this->add(array(
             'name' => 'soTien',
             'type' => 'Number',             
             'attributes'=>array(                
                'id'=>'soTien',
            ),
        ));

        $this->add(array(
             'name' => 'ngayThanhToan',
             'type' => 'Date',             
             'attributes'=>array(               
                'id'=>'ngayThanhToan',
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
<?php
namespace CongNo\Form;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Form\Form;
use CongNo\Entity\PhieuThu;

class PhieuThuFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('phieu-thu');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new PhieuChi());

        $this->add(array(
             'name' => 'idPhieuThu',
             'type' => 'Hidden',
        ));

        $this->add(array(
             'name' => 'idCongNo',
             'type' => 'Hidden',
        ));

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
}
?>
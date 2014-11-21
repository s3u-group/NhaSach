<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Form\Form;
use HangHoa\Entity\PhieuNhap;
use HangHoa\Form\ChiTietPhieuNhapFieldset;
use HangHoa\Form\DoiTacFieldset;
use HangHoa\Form\SystemUserFieldset;

class PhieuNhapFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('phieu-nhap');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new PhieuNhap());

        $this->add(array(
             'name' => 'idPhieuNhap',
             'type' => 'Hidden',
         ));

        $this->add(array(
             'name' => 'maPhieuNhap',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Mã phiếu nhập',
             ),
             'attributes'=>array(
                //'required'=>'required',
                //'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Mã phiếu nhập',
            ),
         ));

        $this->add(array(
             'name' => 'ngayNhap',
             'type' => 'Date',
             'options' => array(
                 'label' => 'Ngày nhập',
             ),
             'attributes'=>array(
                //'required'=>'required',
                //'class'   => 'h5a-input form-control input-sm',                
            ),
         ));        
        
        $doiTacFieldset = new DoiTacFieldset($objectManager);
        $doiTacFieldset->setUseAsBaseFieldset(true);
        $doiTacFieldset->setName('idDoiTac');
        $this->add($doiTacFieldset);

        $systemUserFieldset = new SystemUserFieldset($objectManager);
        $systemUserFieldset->setUseAsBaseFieldset(true);
        $systemUserFieldset->setName('idUserNv');
        $this->add($systemUserFieldset); 

        $chiTietNhapHangFieldset = new ChiTietPhieuNhapFieldset($objectManager);
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'ctPhieuNhap',
            'options' => array(
                'label' => '',
                'count' => 1,
                'should_create_template' => false,
                'allow_add' => false,
                'target_element' => $chiTietNhapHangFieldset,
            )
        ));       
    }
    public function getInputFilterSpecification()
    {
        return array(            
        );
    }
}
?>
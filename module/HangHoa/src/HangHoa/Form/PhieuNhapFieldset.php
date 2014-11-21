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

        $this->add(array(
             'name' => 'idDoiTac',
             'type' => 'Hidden',             
         ));

        $this->add(array(
             'name' => 'idUserNv',
             'type' => 'Hidden',             
         ));

        $chiTietNhapHangFieldset = new ChiTietPhieuNhapFieldset($objectManager);
        $chiTietNhapHangFieldset->setUseAsBaseFieldset(true);
        $this->add($chiTietNhapHangFieldset);
    }
    public function getInputFilterSpecification()
    {
        return array(            
        );
    }
}
?>
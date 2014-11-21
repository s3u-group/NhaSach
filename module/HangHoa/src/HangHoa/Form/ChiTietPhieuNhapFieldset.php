<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use HangHoa\Entity\CTPhieuNhap;

class ChiTietPhieuNhapFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('ct-phieu-nhap');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new CTPhieuNhap());

        $this->add(array(
             'name' => 'idCTPhieuNhap',
             'type' => 'Hidden',
         ));

        $this->add(array(
             'name' => 'idPhieuNhap',
             'type' => 'Hidden',
         ));

        $this->add(array(
             'name' => 'idSanPham',
             'type' => 'Hidden',
         ));

        $this->add(array(
             'name' => 'giaNhap',
             'type' => 'Number',
             'options' => array(
                 'label' => 'Giá nhập',
             ),
             'attributes'=>array(
                //'required'=>'required',
                //'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Giá nhập',
                'min'=>0,
                'step'=>500,
            ),
         ));

        $this->add(array(
             'name' => 'soLuong',
             'type' => 'Number',
             'options' => array(
                 'label' => 'Số lượng',
             ),
             'attributes'=>array(
                //'required'=>'required',
                //'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Số lượng',
                'min'=>0,
            ),
         ));

        /*$SanPhamFieldset = new SanPhamFieldset($objectManager);
        $SanPhamFieldset->setUseAsBaseFieldset(true);
        $this->add($SanPhamFieldset);*/
    }
    public function getInputFilterSpecification()
    {
        return array(            
        );
    }
}
?>
<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use HangHoa\Form\SanPhamFieldset;
use HangHoa\Form\HoaDonFieldset;

use Zend\Form\Element;
use Zend\Form\Form;

use HangHoa\Entity\SanPham;
use HangHoa\Entity\HoaDon;
use HangHoa\Entity\CTHoaDon;

class CTHoaDonFieldset  extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('ct-hoa-don');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new CTHoaDon());

         $this->add(array(
             'name' => 'idCTHoaDon',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'idHoaDon',
             'type' => 'Hidden',
         ));

        
         $sanPhamFieldset = new SanPhamFieldset($objectManager);
         $sanPhamFieldset->setUseAsBaseFieldset(true);
         $sanPhamFieldset->setName('idSanPham');
         $this->add($sanPhamFieldset); 

         $this->add(array(
             'name' => 'gia',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Giá xuất',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Nhập giá sản phẩm',
            ),
         )); 


         $this->add(array(
             'name' => 'soLuong',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Số lượng',
             ),
         ));         
             

    }

    public function getInputFilterSpecification()
    {
        return array(
        );
    }
}
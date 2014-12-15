<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use HangHoa\Entity\CTPhieuNhap;
use HangHoa\Form\SanPhamFieldset;

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
             'name' => 'giaNhap',
             'type' => 'Number',
             'options' => array(
                 'label' => 'Giá nhập',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                //'placeholder'=>'Giá nhập',
                //'min'=>0,
            ),
         ));

        $this->add(array(
             'name' => 'soLuong',
             'type' => 'Number',
             'options' => array( 
                'label' => 'Số lượng',                
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                //'placeholder'=>'Số lượng',
                'min'=>0,
            ),
         ));

        $this->add(array(
             'type' => 'Text',
             'name' => 'loaiGia',
             'options' => array(
                    'label' => 'A checkbox',                     
             )
         ));

          $this->add(array(
             'name' => 'giaBia',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

          $this->add(array(
             'name' => 'chiecKhau',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));


         $sanPhamFieldset = new SanPhamFieldset($objectManager);
         $sanPhamFieldset->setUseAsBaseFieldset(true);
         $sanPhamFieldset->setName('idSanPham');
         $sanPhamFieldset->remove('idLoai');
         $sanPhamFieldset->remove('idDonViTinh');
         $sanPhamFieldset->remove('nhan');
         $sanPhamFieldset->remove('hinhAnh');
         $sanPhamFieldset->remove('tonKho');
         //$sanPhamFieldset->remove('loaiGia');
         $this->add($sanPhamFieldset);


         
    }
    public function getInputFilterSpecification()
    {
        return array(            
        );
    }
}
?>
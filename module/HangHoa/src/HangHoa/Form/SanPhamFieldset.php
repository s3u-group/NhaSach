<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use HangHoa\Entity\SanPham;

class SanPhamFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('san-pham');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new SanPham());

         $this->add(array(
             'name' => 'idSanPham',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'tenSanPham',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Tên sản phẩm',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Tên sản phẩm',
            ),
         )); 

         $this->add(array(
             'name' => 'maSanPham',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Mã sản phẩm',
             ),
             'attributes'=>array('required'=>'required'),
         ));

         $this->add(array(
             'name' => 'moTa',
             'type' => 'Textarea',
             'options' => array(
                 'label' => 'Mô tả',
             ),
             //'attributes'=>array('required'=>'required'),
         ));         
                  

         $this->add(array(
             'name' => 'idLoai',
             'type' => '\Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Loại',
                 'empty_option'=>'----------Chọn Loại Sản Phẩm----------',
                 'disable_inarray_validator' => true,
             ),
         ));

        $this->add(array(
         'name' => 'nhan',
         'type' => 'Text',
         'options' => array(
             'label' => 'Chọn Nhãn',             
         ),
         'attributes'=>array('required'=>'required'),
        ));

       
         $this->add(array(
             'name' => 'idDonViTinh',
             'type' => '\Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Chọn Đơn Vị Tính',
                 'empty_option'=>'----------Chọn Đơn Vị Tính----------',
                 'disable_inarray_validator' => true,                 
             ),
         ));

         $this->add(array(
             'name' => 'hinhAnh',
             'type' => 'Zend\Form\Element\File',
             'options' => array( 
              )             
         ));

        $this->add(array(
             'name' => 'tonKho',
             'type' => 'Hidden',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'giaNhap',
             'type' => 'Hidden',
             'options' => array(                 
             ),             
         ));

        /*$this->add(array(
        'type' => 'Zend\Form\Element\Collection',
        'name' => 'nhan',
        'options' => array(

            'count' => 1,
            'should_create_template' => true,
            'allow_add' => true,
            'allow_remove' => true,
            'template_placeholder' => '__element_name__',
            'target_element' => new CustomFieldset('nhan', 'Nhãn')
            )
        )); */       
    }

    public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
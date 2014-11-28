<?php

namespace KenhPhanPhoi\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use HangHoa\Entity\DoiTac;

class KhachHangFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('khach-hang');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new DoiTac());

         $this->add(array(
             'name' => 'idDoiTac',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'hoTen',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Họ tên',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Họ tên',
            ),
         )); 

         $this->add(array(
             'name' => 'diaChi',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Địa chỉ',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Địa chỉ',
             ),
         ));

          $this->add(array(
             'name' => 'email',
             'type' => 'Email',             
             
         ));

         $this->add(array(
             'name' => 'moTa',
             'type' => 'Textarea',
             'options' => array(
                 'label' => 'Mô tả',
             ),
         ));         
                  

         $this->add(array(
         'name' => 'dienThoaiCoDinh',
         'type' => 'Text',
         'options' => array(
             'label' => 'Điện thoại cố định',             
         ),         
        ));

         $this->add(array(
         'name' => 'diDong',
         'type' => 'Text',
         'options' => array(
             'label' => 'Điện thoại di động',             
         ),         
        ));


          $this->add(array(
             'name' => 'hinhAnh',
             'type' => 'Zend\Form\Element\File',
             'options' => array( 
              )             
         ));

        $this->add(array(
         'name' => 'website',
         'type' => 'Text',
         'options' => array(
             'label' => 'Website',             
         ),         
        ));

        $this->add(array(
         'name' => 'twitter',
         'type' => 'Text',
         'options' => array(
             'label' => 'Twitter',             
         ),         
        ));


        // mặc định loại đổi tác =45 thuộc loại khách hàng
        $this->add(array(
             'name' => 'loaiDoiTac',
             'type' => 'Hidden',
             'options' => array( 
                
              )
         ));

          $this->add(array(
             'name' => 'idKenhPhanPhoi',
             'type' => '\Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Kênh phân phối',
                 'empty_option'=>'----------Chọn kênh phân phối----------',
                 'disable_inarray_validator' => true,
             ),
             'attributes'=>array(
                'required'=>'required',
             ),
         ));
    }

    public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use HangHoa\Entity\DoiTac;

class DoiTacFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('doi-tac');

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
             'attributes'=>array('required'=>'required'),
        ));

       
         $this->add(array(
             'name' => 'email',
             'type' => '\Zend\Form\Element\Email',
             'options' => array(
                 'label' => 'Email',
             ),
         ));

         $this->add(array(
             'name' => 'state',
             'type' => 'Hidden',
         ));

        $this->add(array(
             'name' => 'moTa',
             'type' => 'Textarea',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'dienThoaiCoDinh',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'diDong',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'hinhAnh',
             'type' => 'Zend\Form\Element\File',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'website',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'twitter',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

         
         $this->add(array(
             'name' => 'loaiDoiTac',
             'type' => '\Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Đối tác',
                 'empty_option'=>'----------Chọn Loại đối tác----------',
                 'disable_inarray_validator' => true,
             ),
         ));

    }

    public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
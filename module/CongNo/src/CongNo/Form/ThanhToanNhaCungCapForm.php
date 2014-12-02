<?php
namespace CongNo\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;

class ThanhToanNhaCungCapForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('thanh-toan-nha-cung-cap');
               
        $this->setHydrator(new DoctrineHydrator($objectManager));

        $this->add(array(
             'name' => 'idNhaCungCap',
             'type' => 'hidden',
             'attributes'=>array(  
                'id'=>'idNhaCungCap'            
            ),
         ));

        $this->add(array(
             'name' => 'thanhToan',
             'type' => 'Number',
             'attributes'=>array(  
                'id'=>'thanhToan',
                'min'=>0,            
            ),
         ));

        $this->add(array(
             'name' => 'tenNhaCungCap',
             'type' => 'Text',
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm', 
                'placeholder'=>'Nhập tên nhà cung cấp',   
                'id'=>'tenNhaCungCap',
                'autocomplete'=>'off',
            ),
         ));

        $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
    }
}

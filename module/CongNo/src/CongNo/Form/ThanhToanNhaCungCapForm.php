<?php
namespace CongNo\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use CongNo\Form\PhieuChiFieldset;


class ThanhToanNhaCungCapForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('thanh-toan-nha-cung-cap');
               
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        $phieuChiFieldset = new PhieuChiFieldset($objectManager);
        $phieuChiFieldset->setUseAsBaseFieldset(true);
        $this->add($phieuChiFieldset);


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
             'name' => 'submitbutton',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Lưu',
                 'id' => 'submitbutton',
             ),
         ));
    }
}

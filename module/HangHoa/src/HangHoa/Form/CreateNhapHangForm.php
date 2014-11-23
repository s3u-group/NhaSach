<?php
namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use HangHoa\Form\PhieuNhapFieldset;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;


class CreateNhapHangForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('create-nhap-hang');
        
        // The form will hydrate an object of type "BlogPost"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        $nhapHangFieldset = new PhieuNhapFieldset($objectManager);
        $nhapHangFieldset->setUseAsBaseFieldset(true);
        $this->add($nhapHangFieldset);

        $this->add(array(
             'name' => 'idSanPham',
             'type' => 'Hidden',             
         ));

        $this->add(array(
             'name' => 'maHang',
             'type' => 'Text',
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Mã hàng'
            ),
         ));

        $this->add(array(
             'name' => 'tenHang',
             'type' => 'Text',
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Tên hàng'
            ),
         ));

        $this->add(array(
             'name' => 'soLuong',
             'type' => 'Number',             
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',                
                'min'=>0,
            ),
         ));
        $this->add(array(
             'name' => 'giaNhap',
             'type' => 'Number',             
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',                
                'min'=>0,
                'step'=>500,
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
?>
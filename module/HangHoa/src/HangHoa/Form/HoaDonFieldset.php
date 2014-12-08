<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use HangHoa\Form\CTHoaDonFieldset;
use Application\Form\SystemUserFieldset;
use HangHoa\Form\DoiTacFieldset;

use Zend\Form\Element;
use Zend\Form\Form;

use HangHoa\Entity\HoaDon;

class HoaDonFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('hoa-don');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new HoaDon());

         $this->add(array(
             'name' => 'idHoaDon',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'status',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'maHoaDon',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Mã hóa đơn',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Nhập mã hóa đơn',
            ),
         )); 

         $this->add(array(
             'name' => 'ngayXuat',
             'type' => 'Date',
             'options' => array(
                 'label' => 'Ngày xuất',
             ),
             'attributes'=>array('required'=>'required'),
         ));

          

        $doiTacFieldset = new DoiTacFieldset($objectManager);
        $doiTacFieldset->setUseAsBaseFieldset(true);
        $doiTacFieldset->setName('idDoiTac');
        $doiTacFieldset->remove('email');
        $doiTacFieldset->remove('state');
        $doiTacFieldset->remove('moTa');
        $doiTacFieldset->remove('loaiDoiTac');

        $this->add($doiTacFieldset);      
             
        $systemUserFieldset = new SystemUserFieldset($objectManager);
        $systemUserFieldset->setUseAsBaseFieldset(true);
        $systemUserFieldset->setName('idUserNv');
        $doiTacFieldset->remove('email');
        $doiTacFieldset->remove('loaiTaiKhoan');
        $this->add($systemUserFieldset); 

        $ctHoaDonFieldset = new CTHoaDonFieldset($objectManager);
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'ctHoaDons',
            'options' => array(
                'label' => '',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => $ctHoaDonFieldset,
            )
        ));      
                  

             
    }

    public function getInputFilterSpecification()
    {
        return array(
            'ngayXuat' => array(
                'required' => false
            )
        );
    }
}
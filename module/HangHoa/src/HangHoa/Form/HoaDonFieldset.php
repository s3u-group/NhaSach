<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use HangHoa\Form\CTHoaDonFieldset;

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
        $this->add($doiTacFieldset);      
             
        $systemUserFieldset = new SystemUserFieldset($objectManager);
        $systemUserFieldset->setUseAsBaseFieldset(true);
        $systemUserFieldset->setName('idUserNv');
        $this->add($systemUserFieldset); 

       

         $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'ctHoaDon',
            'options' => array(
                'label' => '',
                'count' => 1,
                'should_create_template' => false,
                'allow_add' => false,
                'target_element' => array(
                    'type' => 'HangHoa\Form\CTHoaDonFieldset'
                )
            )
        ));      
                  

             
    }

    public function getInputFilterSpecification()
    {
        return array(
        );
    }
}
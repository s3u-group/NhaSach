<?php

namespace Kho\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use Kho\Entity\Kho;

class KhoFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('kho');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new Kho());

         $this->add(array(
             'name' => 'idKho',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'tenKho',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Tên kho',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Nhập tên kho',
            ),
         )); 

         $this->add(array(
             'name' => 'diaChiKho',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Địa chỉ kho',
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Địa chỉ kho',
             ),
         ));
     }
         

    public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
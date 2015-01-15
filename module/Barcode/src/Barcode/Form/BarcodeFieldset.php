<?php

namespace Barcode\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use Barcode\Entity\Barcode;

class BarcodeFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('barcode');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new Barcode());

         $this->add(array(
             'name' => 'idBarcode',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'tenBarcode',
             'type' => 'Text',
             'options' => array(                 
             ),
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Nhập tên loại mã vạch',
            ),
         )); 

         /*$this->add(array(
             'name' => 'length',
             'type' => 'Number',
             'options' => array(                
             ),
             'attributes'=>array(                
                'class'   => 'h5a-input form-control input-sm',
                'min'=>0,                
             ),
         ));*/

         $this->add(array(
             'name' => 'state',
             'type' => 'Hidden',
         ));
     }
         

    public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
<?php
namespace Barcode\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Barcode\Form\BarcodeFieldset;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;


class BarcodeForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('barcode');
        
        // The form will hydrate an object of type "BlogPost"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        $barcodeFieldset = new BarcodeFieldset($objectManager);
        $barcodeFieldset->setUseAsBaseFieldset(true);
        $this->add($barcodeFieldset);        

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
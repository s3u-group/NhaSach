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
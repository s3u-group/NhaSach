<?php
namespace KenhPhanPhoi\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use KenhPhanPhoi\Form\KhachHangFieldset;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;


class ThemKhachHangForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('them-khach-hang');
        
        // The form will hydrate an object of type "BlogPost"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        $khachHangFieldset = new KhachHangFieldset($objectManager);
        $khachHangFieldset->setUseAsBaseFieldset(true);
        $this->add($khachHangFieldset);        

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
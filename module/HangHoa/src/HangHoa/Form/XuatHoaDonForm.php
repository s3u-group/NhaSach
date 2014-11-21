<?php
namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use HangHoa\Form\HoaDonFieldset;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;


class XuatHoaDonForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('xuat-hoa-don');
        
        // The form will hydrate an object of type "BlogPost"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        $hoaDonFieldset = new HoaDonFieldset($objectManager);
        $hoaDonFieldset->setUseAsBaseFieldset(true);
        $this->add($hoaDonFieldset);        

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
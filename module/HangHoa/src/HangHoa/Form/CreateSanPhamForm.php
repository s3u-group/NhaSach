<?php
namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use HangHoa\Form\SanPhamFieldset;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;


class CreateSanPhamForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('create-san-pham');
        
        // The form will hydrate an object of type "BlogPost"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        $sanPhamFieldset = new SanPhamFieldset($objectManager);
        $sanPhamFieldset->setUseAsBaseFieldset(true);
        $this->add($sanPhamFieldset);

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
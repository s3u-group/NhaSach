<?php
namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;


class FileForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('file');
        
        // The form will hydrate an object of type "BlogPost"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        /*$sanPhamFieldset = new SanPhamFieldset($objectManager);
        $sanPhamFieldset->setUseAsBaseFieldset(true);
        $this->add($sanPhamFieldset);*/        

        $this->add(array(
             'name' => 'file',
             'type' => 'Zend\Form\Element\File',
             'optin'=>array(
             ),
             'attributes'=>array(
                'id'=>'file',                
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
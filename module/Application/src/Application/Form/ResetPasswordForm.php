<?php
namespace Application\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Application\Form\ResetPasswordFieldset;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;


class ResetPasswordForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('tao-tai-khoan');
        
        // The form will hydrate an object of type "BlogPost"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the user fieldset, and set it as the base fieldset
        $hoaDonFieldset = new ResetPasswordFieldset($objectManager);
        $hoaDonFieldset->setUseAsBaseFieldset(true);
        $this->add($hoaDonFieldset);  

        $this->add(array(
             'name' => 'passwordVerify',
             'type' => 'Password',
             'options' => array(
                 'label' => 'Nhập lại mật khẩu',
             ),
             'attributes'=>array('required'=>'required'),
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
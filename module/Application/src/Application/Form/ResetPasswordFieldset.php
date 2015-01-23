<?php

namespace Application\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use Application\Entity\SystemUser;

class ResetPasswordFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('reset-password');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new SystemUser());

         $this->add(array(
             'name' => 'userId',
             'type' => 'Hidden',
         ));

         
         $this->add(array(
             'name' => 'password',
             'type' => 'Password',
             'options' => array(
                 'label' => 'Mật khẩu',
             ),
            'attributes'=>array('required'=>'required'),
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'passwordVeryfy', // name of first password field
                    ),
                ),
            ),
         ));        

    }

    public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
<?php

namespace Application\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;
use Zend\Form\Form;
use Application\Entity\SystemUser;

class SystemUserFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('system-user');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new SystemUser());

         $this->add(array(
             'name' => 'userId',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'username',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Tên đăng nhập',
             ),
             'attributes'=>array(               
                'placeholder'=>'Tên đăng nhập',
            ),
         )); 

         $this->add(array(
             'name' => 'password',
             'type' => 'Password',
             'options' => array(
                 'label' => 'Mật khẩu',
             ),
             'attributes'=>array('required'=>'required'),
         ));

         $this->add(array(
             'name' => 'displayName',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Tên hiển thị',
             ),
             
         ));         
                  

         $this->add(array(
             'name' => 'hoTen',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Họ tên',
             ),
             'attributes'=>array('required'=>'required'),
         ));

        $this->add(array(
             'name' => 'diaChi',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Địa chỉ',             
             ),
             'attributes'=>array('required'=>'required'),
        ));

       
         $this->add(array(
             'name' => 'email',
             'type' => '\Zend\Form\Element\Email',
             'options' => array(
                 'label' => 'Email',
             ),
             'attributes'=>array('required'=>'required'),
         ));

         $this->add(array(
             'name' => 'state',
             'type' => 'Hidden',
         ));

        $this->add(array(
             'name' => 'moTa',
             'type' => 'Textarea',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'dienThoaiCoDinh',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

         $this->add(array(
             'name' => 'diDong',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

         
         $this->add(array(
             'name' => 'twitter',
             'type' => 'Text',
             'options' => array(                 
             ),             
         ));

        

    }

    public function getInputFilterSpecification()
    {
        return array(
          
        );
    }
}
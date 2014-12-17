<?php
namespace CongNo\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use CongNo\Form\XuatPhieuChiFieldset;


class PhieuChiForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('thanh-toan');
               
        $this->setHydrator(new DoctrineHydrator($objectManager));

        $phieuChiFieldset = new XuatPhieuChiFieldset($objectManager);
        $phieuChiFieldset->setUseAsBaseFieldset(true);
        $this->add($phieuChiFieldset);

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

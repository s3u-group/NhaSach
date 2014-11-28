<?php

namespace HangHoa\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Form\Form;
use HangHoa\Entity\PhieuNhap;
use HangHoa\Form\ChiTietPhieuNhapFieldset;
use HangHoa\Form\DoiTacFieldset;
use HangHoa\Form\SystemUserFieldset;
use DateTime;
use DateTimeZone;

class PhieuNhapFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('phieu-nhap');

        $this->setHydrator(new DoctrineHydrator($objectManager))
             ->setObject(new PhieuNhap());

        $this->add(array(
             'name' => 'idPhieuNhap',
             'type' => 'Hidden',
         ));

        $this->add(array(
             'name' => 'maPhieuNhap',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Mã phiếu nhập',
             ),
             'attributes'=>array(
                //'required'=>'required',
                //'class'   => 'h5a-input form-control input-sm',
                'placeholder'=>'Mã phiếu nhập',
            ),
         ));

        $currentDate = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh'));
        $ngayHienTai=new DateTime($currentDate->format('Y-m-d'));

        $this->add(array(
             'name' => 'ngayNhap',
             'type' => 'Date',
             'options' => array(
                 'label' => 'Ngày nhập',
                 'value'=>$ngayHienTai,
             ),
             'attributes'=>array(
                'required'=>false,
                //'class'   => 'h5a-input form-control input-sm',                
            ),
         ));        
        
        $doiTacFieldset = new DoiTacFieldset($objectManager);
        $doiTacFieldset->setUseAsBaseFieldset(true);
        $doiTacFieldset->setName('idDoiTac');
        $doiTacFieldset->remove('email');
        $doiTacFieldset->remove('state');
        $doiTacFieldset->remove('moTa');
        $doiTacFieldset->remove('loaiDoiTac');

        $this->add($doiTacFieldset);

        $systemUserFieldset = new SystemUserFieldset($objectManager);
        $systemUserFieldset->setUseAsBaseFieldset(true);
        $systemUserFieldset->setName('idUserNv');
        $doiTacFieldset->remove('email');
        $doiTacFieldset->remove('loaiTaiKhoan');
        $this->add($systemUserFieldset); 

        $chiTietNhapHangFieldset = new ChiTietPhieuNhapFieldset($objectManager);
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'ctPhieuNhaps',
            'options' => array(
                'label' => '',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => $chiTietNhapHangFieldset,
            )
        ));       
    }
    public function getInputFilterSpecification()
    {
        return array(            
        );
    }
}
?>
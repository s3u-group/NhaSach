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
             'name' => 'idKhachHang',
             'type' => 'hidden',
             'attributes'=>array(  
                'id'=>'idKhachhang'            
            ),
         )); 

        $this->add(array(
             'name' => 'idSanPham',
             'type' => 'hidden',
             'attributes'=>array(  
                'id'=>'idSanPham'            
            ),
         )); 


        $this->add(array(
             'name' => 'donViTinh',
             'type' => 'hidden',
             'attributes'=>array(  
                'id'=>'donViTinh'            
            ),
         ));

        $this->add(array(
             'name' => 'khachHang',
             'type' => 'Text',
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm', 
                'placeholder'=>'Nhập tên khách hàng',   
                'id'=>'tenKhachHang'            
            ),
         )); 

        $this->add(array(
             'name' => 'diaChi',
             'type' => 'Text',
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm', 
                'id' => 'diaChiKhachHang',
                'placeholder'=>'Nhập địa chỉ',              
            ),
         ));

        $this->add(array(
             'name' => 'maHang',
             'type' => 'Text',
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',  
                'placeholder'=>'Nhập mã hàng', 
                'id'=>'maHang',
            ),
         ));

        $this->add(array(
             'name' => 'tenHang',
             'type' => 'Text',
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',     
                'placeholder'=>'Nhập tên hàng',
                'id'=>'tenHang',
            ),
         ));

        $this->add(array(
             'name' => 'soLuong',
             'type' => 'Number',             
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',                
                'min'=>0,
                'placeholder'=>'Nhập số lượng',
                'id'=>'soLuong',
            ),
         ));
        $this->add(array(
             'name' => 'giaXuat',
             'type' => 'Number',             
             'attributes'=>array(
                'required'=>'required',
                'class'   => 'h5a-input form-control input-sm',                
                'min'=>0,
                'step'=>500,
                'placeholder'=>'Nhập giá',
                'id'=>'giaXuat',
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
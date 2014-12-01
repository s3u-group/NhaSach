<?php
namespace LoiNhuan\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
 
class DistinctPlugin extends AbstractPlugin{
   
    // chuyền vào một mảng tháng năm theo định dạng Y-m, vd: array(2014-11,2014-10,2014-11)
    // hàm này sẽ return về một mảng có giá trị array(2014-11,2014-10)
    // nó sẽ bỏ những giá trị trùng ra khỏi mảng
    public function DistinctFunction($yMs)
    {
        $namThangs=array();
        foreach ($yMs as $yM) {
            $co=0;
            foreach ($namThangs as $namThang) {
                if($yM==$namThang)
                {
                    $co=1;
                }
            }
            if($co==0)
            {
                $namThangs[]=$yM;
            }
        }

        return $namThangs;
    }

}
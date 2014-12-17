<?php
namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel5;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Shared_Date;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Style_Color;
use PHPExcel_RichText;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Font;
use DateTime;
use DateTimeZone;
 
class ExportExcel extends AbstractPlugin{
   
    // chuyền vào một mảng tháng năm theo định dạng Y-m, vd: array(2014-11,2014-10,2014-11)
    // hàm này sẽ return về một mảng có giá trị array(2014-11,2014-10)
    // nó sẽ bỏ những giá trị trùng ra khỏi mảng
    public function exportExcel($objPHPExcel, $fileNameOutput, $data)
    {
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE); 
        ini_set('display_startup_errors', TRUE); 
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        // Create new PHPExcel object
        
        //$objPHPExcel = new PHPExcel();

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=".$fileNameOutput.".xls"); 
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: text/html; charset=utf-8');

        
        // Set document properties
        
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                       ->setLastModifiedBy("Maarten Balliauw")
                       ->setTitle("Office 2007 XLSX Test Document")
                       ->setSubject("Office 2007 XLSX Test Document")
                       ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                       ->setKeywords("office 2007 openxml php")
                       ->setCategory("Test result file");

        // Set default font
        
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                                  ->setSize(10);

        // set data output
        $data; // value of data include from controler
        // end data output
        // Rename worksheet    
        $objPHPExcel->getActiveSheet()->setTitle($fileNameOutput);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Save Excel 2007 file
        $callStartTime = microtime(true);

        
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);    //  (I want the output for 2003)
        $objWriter->save('php://output'); 
        
    }

}
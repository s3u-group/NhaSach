<?php namespace KenhPhanPhoi\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 use HangHoa\Entity\DoiTac;
 use KenhPhanPhoi\Form\ThemKhachHangForm;
 use KenhPhanPhoi\Form\KhachHangFieldset;
 use HangHoa\Entity\CTHoaDon;
 use PHPExcel;
 use PHPExcel_IOFactory;
 use PHPExcel_Shared_Date;
 use PHPExcel_Style_NumberFormat;
 use PHPExcel_Style_Color;
 use PHPExcel_RichText;
 use PHPExcel_Style_Border;
 use PHPExcel_Style_Alignment;
 use PHPExcel_Style_Fill;

 
 
 class IndexController extends AbstractActionController
 {
 	private $entityManager;
  
  public function getEntityManager()
  {
     if(!$this->entityManager)
     {
      $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
     }
     return $this->entityManager;
  }
  
 	public function indexAction()
 	{
  	$this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();
    $doiTacs=$entityManager->getRepository('HangHoa\Entity\DoiTac')->findAll(); 

    $taxonomyFunction=$this->TaxonomyFunction();
    $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug


    return array(
      'kenhPhanPhois'=>$kenhPhanPhois,
      'doiTacs'=>$doiTacs,
    );

 	}

  // đã sửa tên biến
  public function nhaCungCapAction()
  {
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();
    $doiTacs=$entityManager->getRepository('HangHoa\Entity\DoiTac')->findAll(); 

    return array(
      'doiTacs'=>$doiTacs,
    );

  }



  public function chiTietDonHangAction()
  {
      $this->layout('layout/giaodien');
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
           return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }  
      $entityManager=$this->getEntityManager();
      $hoaDon=$entityManager->getRepository('HangHoa\Entity\HoaDon')->find($id);
      $query=$entityManager->createQuery('SELECT cthd FROM HangHoa\Entity\CTHoaDon cthd WHERE cthd.idHoaDon='.$hoaDon->getIdHoaDon());
      $chiTietHoaDons=$query->getResult();
      
      return array(
        'chiTietHoaDons'=>$chiTietHoaDons,
        'hoaDon'=>$hoaDon,
      );
  }

  // đã sửa tên biến
  public function chiTietPhieuNhapAction()
  {
      $this->layout('layout/giaodien');
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
           return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'nhaCungCap'));
      }  
      $entityManager=$this->getEntityManager();
      $phieuNhap=$entityManager->getRepository('HangHoa\Entity\PhieuNhap')->find($id);
      $query=$entityManager->createQuery('SELECT cthd FROM HangHoa\Entity\CTPhieuNhap cthd WHERE cthd.idPhieuNhap='.$phieuNhap->getIdPhieuNhap());
      $chiTietPhieuNhaps=$query->getResult();
      return array(
        'chiTietPhieuNhaps'=>$chiTietPhieuNhaps,
        'phieuNhap'=>$phieuNhap,
      );
  }

 	public function themKhachHangAction()
 	{
    	$this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

      $doiTac=new DoiTac();
      $form= new ThemKhachHangForm($entityManager);
      $form->bind($doiTac);

      $taxonomyFunction=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug


      $request = $this->getRequest();
      if($request->isPost())
      {
        $post = array_merge_recursive(
              $request->getPost()->toArray(),
              $request->getFiles()->toArray()
        );
        $form->setData($request->getPost());      
        if ($form->isValid())
        {
          $query = $entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.email=\''.$doiTac->getEmail().'\'');
          $ktDoiTac = $query->getResult(); // array of CmsArticle objects  
          if($ktDoiTac)
          {
            return array(
              'form' => $form, 
              'kenhPhanPhois'=>$kenhPhanPhois,
              'ktTonTaiKhachHang'=>1,
            ); 
          }
          else
          {
            if($post['khach-hang']['hinhAnh']['error']==0)
            {
              // tạo lại tên mới
              $uniqueToken=md5(uniqid(mt_rand(),true));
              $newName=$uniqueToken.'_'.$post['khach-hang']['hinhAnh']['name'];
              // lưu vào cơ sở dữ liệu với tên hình là tên vừa tạo ở trên
              $doiTac->setHinhAnh($newName);
              // di chuyển hình ảnh vào img            
              $filter = new \Zend\Filter\File\Rename("./public/img/".$newName);
              $filter->filter($post['khach-hang']['hinhAnh']);
            }
            if(!$doiTac->getHinhAnh())
            {
              $doiTac->setHinhAnh('photo_default.png');
            }
            $entityManager->persist($doiTac);
            $entityManager->flush();

            return $this->redirect()->toRoute('kenh_phan_phoi/crud');   
          }          
        }
      }
      return array(
        'form' => $form, 
        'kenhPhanPhois'=>$kenhPhanPhois,
        'ktTonTaiKhachHang'=>0,
      ); 

 	}

  //đã sửa tên biến
  public function themNhaCungCapAction()
  {
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

      $doiTac=new DoiTac();
      $form= new ThemKhachHangForm($entityManager);
      $form->bind($doiTac);

      $taxonomyFunction=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug


      $request = $this->getRequest();
      if($request->isPost())
      {
        $post = array_merge_recursive(
              $request->getPost()->toArray(),
              $request->getFiles()->toArray()
        );
        $form->setData($request->getPost());      
        if ($form->isValid())
        {
          $query = $entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.hoTen=\''.$doiTac->getHoTen().'\'');
          $ktDoiTac = $query->getResult(); // array of CmsArticle objects  
          if($ktDoiTac)
          {
            return array(
              'form' => $form, 
              'kenhPhanPhois'=>$kenhPhanPhois,
              'ktTonTaiNhaCungCap'=>1,
            ); 
          }
          else
          {
            if($post['khach-hang']['hinhAnh']['error']==0)
            {
              // tạo lại tên mới
              $uniqueToken=md5(uniqid(mt_rand(),true));
              $newName=$uniqueToken.'_'.$post['khach-hang']['hinhAnh']['name'];
              // lưu vào cơ sở dữ liệu với tên hình là tên vừa tạo ở trên
              $doiTac->setHinhAnh($newName);
              // di chuyển hình ảnh vào img            
              $filter = new \Zend\Filter\File\Rename("./public/img/".$newName);
              $filter->filter($post['khach-hang']['hinhAnh']);
            }
            if(!$doiTac->getHinhAnh())
            {
              $doiTac->setHinhAnh('photo_default.png');
            }
            
            $entityManager->persist($doiTac);
            $entityManager->flush();
            return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'nhaCungCap'));   
          }          
        }
      }
      return array(
        'form' => $form, 
        'kenhPhanPhois'=>$kenhPhanPhois,
        'ktTonTaiNhaCungCap'=>0,
      ); 

  }


  public function chiTietKhachHangAction()
  {

      $this->layout('layout/giaodien');
      $id=(int)$this->params()->fromRoute('id',0);
      if(!$id)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }
      $entityManager=$this->getEntityManager();      
      $query=$entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.idDoiTac='.$id.' and kh.loaiDoiTac=45');
      $khachHangs=$query->getResult();
      // nếu id khách hàng này không tồn tại
      if(!$khachHangs)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }
      $taxonomyFunction=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug


      $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.idDoiTac='.$id);
      $lichSuGiaoDichs=$query->getResult();

      $khachHang=$khachHangs[0];
      $form= new ThemKhachHangForm($entityManager);
      $form->bind($khachHang);


      $request=$this->getRequest();
      if($request->isPost())
      {
        $post = array_merge_recursive(
              $request->getPost()->toArray(),
              $request->getFiles()->toArray()
          ); 
        $form->setData($request->getPost());
        if($form->isValid())
        {
          $query=$entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.loaiDoiTac=45 and kh.email=\''.$khachHang->getEmail().'\'');
          $ktKhachHang=$query->getResult();
          if(!$ktKhachHang||($ktKhachHang&&$ktKhachHang[0]->getIdDoiTac()==$khachHang->getIdDoiTac()))
          {
            if ($post['khach-hang']['hinhAnh']['error']==0) {
              $uniqueToken=md5(uniqid(mt_rand(),true));          
              $newName=$uniqueToken.'_'.$post['khach-hang']['hinhAnh']['name'];
              $filter = new \Zend\Filter\File\Rename("./public/img/".$newName);
              $filter->filter($post['khach-hang']['hinhAnh']);
              $khachHang->setHinhAnh($newName);
            }   
            $entityManager->flush();
          }        
          else
          {
            $khachHang->setHoTen($request->getPost()['hoTenTruoc']);
            return array(
              'form'=>$form,
              'khachHang'=>$khachHang,
              'kenhPhanPhois'=>$kenhPhanPhois,
              'lichSuGiaoDichs'=>$lichSuGiaoDichs,
              'coKiemTraTrung'=>1,
            );
          }
        }       
        
      }

      return array(
        'form'=>$form,
        'khachHang'=>$khachHang,
        'kenhPhanPhois'=>$kenhPhanPhois,
        'lichSuGiaoDichs'=>$lichSuGiaoDichs,
        'coKiemTraTrung'=>0,
      );
  }


  // đã sửa tên biến
  public function chiTietNhaCungCapAction()
  {

      $this->layout('layout/giaodien');
      $id=(int)$this->params()->fromRoute('id',0);
      if(!$id)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nhaCungCap'));
      }
      $entityManager=$this->getEntityManager();      
      $query=$entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.idDoiTac='.$id.' and kh.loaiDoiTac=46');
      $khachHangs=$query->getResult();
      // nếu id khách hàng này không tồn tại
      if(!$khachHangs)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nhaCungCap'));
      }
      $taxonomyFunction=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug


      $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.idDoiTac='.$id);
      $lichSuGiaoDichs=$query->getResult();

      $nhaCungCap=$khachHangs[0];
      $form= new ThemKhachHangForm($entityManager);
      $form->bind($nhaCungCap);


      $request=$this->getRequest();
      if($request->isPost())
      {
        //die(var_dump($request->getPost()['hoTenTruoc']));
        $post = array_merge_recursive(
              $request->getPost()->toArray(),
              $request->getFiles()->toArray()
          ); 
        $form->setData($request->getPost());
        if($form->isValid())
        {
          $query=$entityManager->createQuery('SELECT ncc FROM HangHoa\Entity\DoiTac ncc WHERE ncc.loaiDoiTac=46 and ncc.hoTen=\''.$nhaCungCap->getHoTen().'\'');
          $ktNhaCungCap=$query->getResult();
          if(!$ktNhaCungCap||($ktNhaCungCap&&$ktNhaCungCap[0]->getIdDoiTac()==$nhaCungCap->getIdDoiTac()))
          {

            if ($post['khach-hang']['hinhAnh']['error']==0) 
            {
              $uniqueToken=md5(uniqid(mt_rand(),true));          
              $newName=$uniqueToken.'_'.$post['khach-hang']['hinhAnh']['name'];
              $filter = new \Zend\Filter\File\Rename("./public/img/".$newName);
              $filter->filter($post['khach-hang']['hinhAnh']);
              $nhaCungCap->setHinhAnh($newName);
            }   
            $entityManager->flush();
          }  
          else
          {
            $nhaCungCap->setHoTen($request->getPost()['hoTenTruoc']);
            return array(
              'form'=>$form,
              'nhaCungCap'=>$nhaCungCap,
              'kenhPhanPhois'=>$kenhPhanPhois,
              'lichSuGiaoDichs'=>$lichSuGiaoDichs,
              'coKiemTraTrung'=>1,
            );

          }

        }       
        
      }
      
      return array(
        'form'=>$form,
        'nhaCungCap'=>$nhaCungCap,
        'kenhPhanPhois'=>$kenhPhanPhois,
        'lichSuGiaoDichs'=>$lichSuGiaoDichs,
        'coKiemTraTrung'=>0,
      );
  }

  public function xoaKhachHangAction()
  {
      $this->layout('layout/giaodien');
      $id=(int)$this->params()->fromRoute('id',0);
      if(!$id)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }
      $entityManager=$this->getEntityManager();
      $doiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if(!$doiTac)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }

      $loaiDoiTac=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(0);
      $doiTac->setLoaiDoiTac($loaiDoiTac);
      $entityManager->flush();
      return $this->redirect()->toRoute('kenh_phan_phoi/crud');

  }


  // sửa tên biến
  public function xoaNhaCungCapAction()
  {
      $this->layout('layout/giaodien');
      $id=(int)$this->params()->fromRoute('id',0);
      if(!$id)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }
      $entityManager=$this->getEntityManager();
      $doiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if(!$doiTac)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nhaCungCap'));
      }

      $loaiDoiTac=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(0);
      $doiTac->setLoaiDoiTac($loaiDoiTac);
      $entityManager->flush();
      return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nhaCungCap'));

  }


  public function exportExcelAction()
  {

    $entityManager=$this->getEntityManager();

    $filename='PhanVanThanh.xlsx';


    /** Error reporting */
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE); 
    ini_set('display_startup_errors', TRUE); 
    date_default_timezone_set('Europe/London');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    // Create new PHPExcel object
    echo date('H:i:s') , " Create new PHPExcel object" , EOL;
    $objPHPExcel = new PHPExcel();

    // Set document properties
    echo date('H:i:s') , " Set document properties" , EOL;
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                   ->setLastModifiedBy("Maarten Balliauw")
                   ->setTitle("Office 2007 XLSX Test Document")
                   ->setSubject("Office 2007 XLSX Test Document")
                   ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                   ->setKeywords("office 2007 openxml php")
                   ->setCategory("Test result file");

    // Set default font
    echo date('H:i:s') , " Set default font" , EOL;
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                              ->setSize(10);

// canh chỉnh trong phpexcel: chỉnh mà cho border
    $styleArray = array(
       'borders' => array(
             'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array('argb' => 'FFFF0000'),
             ),
       ),
    );
    //->getStyle('B3')->getFill()->getStartColor()->setARGB('B7B7B7');
    // Add some data, resembling some different data types
    echo date('H:i:s') , " Add some data" , EOL;
    $objPHPExcel->getActiveSheet()->setCellValue('A15', 'phan văn thanh đẹp trai')
                                  ->setCellValue('B15', '22-3-1992')
                                  ->setCellValue('C15', 'PHPExcel');
    // set background color
    $this->cellColor('A15:C15','FFFF0000', $objPHPExcel);

    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'phan văn thanh đẹp trai')
                                  ->setCellValue('B1', '22-3-1992')
                                  ->setCellValue('C1', 'PHPExcel')
                                  // sử dụng applyFormArray(); để chỉnh
                                  ->getStyle('A1:C14')->applyFromArray($styleArray);

    $objPHPExcel->getActiveSheet()->setCellValue('A2', 'String')
                                  ->setCellValue('B2', 'Symbols')
                                  ->setCellValue('C2', '!+&=()~§±æþ');

    $objPHPExcel->getActiveSheet()->setCellValue('A3', 'String')
                                  ->setCellValue('B3', 'UTF-8')
                                  ->setCellValue('C3', 'Создать MS Excel Книги из PHP скриптов')                                  
                                  ->getStyle('A2:L2')->getBorders()->getTop()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLUE));
    
    $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Number')
                                  ->setCellValue('B4', 'Integer')
                                  ->setCellValue('C4', 12)
                                  ->getStyle('A1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('A5', 'Number')
                                  ->setCellValue('B5', 'Float')
                                  ->setCellValue('C5', 34.56);

    $objPHPExcel->getActiveSheet()->setCellValue('A6', 'Number')
                                  ->setCellValue('B6', 'Negative')
                                  ->setCellValue('C6', -7.89);
                                  

    $objPHPExcel->getActiveSheet()->setCellValue('A7', 'Boolean')
                                  ->setCellValue('B7', 'True')
                                  ->setCellValue('C7', true);

    $objPHPExcel->getActiveSheet()->setCellValue('A8', 'Boolean')
                                  ->setCellValue('B8', 'False')
                                  ->setCellValue('C8', false);

    $dateTimeNow = time();
    $objPHPExcel->getActiveSheet()->setCellValue('A9', 'Date/Time')
                                  ->setCellValue('B9', 'Date')
                                  ->setCellValue('C9', PHPExcel_Shared_Date::PHPToExcel( $dateTimeNow ));
    $objPHPExcel->getActiveSheet()->getStyle('C9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);

    
    $objPHPExcel->getActiveSheet()->setCellValue('A10', 'Date/Time')
                                  ->setCellValue('B10', 'Time')
                                  ->setCellValue('C10', PHPExcel_Shared_Date::PHPToExcel( $dateTimeNow ));
    $objPHPExcel->getActiveSheet()->getStyle('C10')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4);

    $objPHPExcel->getActiveSheet()->setCellValue('A11', 'Date/Time')
                                  ->setCellValue('B11', 'Date and Time')
                                  ->setCellValue('C11', PHPExcel_Shared_Date::PHPToExcel( $dateTimeNow ));
    $objPHPExcel->getActiveSheet()->getStyle('C11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);

    $objPHPExcel->getActiveSheet()->setCellValue('A12', 'NULL')
                                  ->setCellValue('C12', NULL);

    $objRichText = new PHPExcel_RichText();
    $objRichText->createText('你好 ');

    $objPayable = $objRichText->createTextRun('你 好 吗？');
    $objPayable->getFont()->setBold(true);
    $objPayable->getFont()->setItalic(true);
    $objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );

    $objRichText->createText(', unless specified otherwise on the invoice.');

    $objPHPExcel->getActiveSheet()->setCellValue('A13', 'Rich Text')
                                  ->setCellValue('C13', $objRichText);


    $objRichText2 = new PHPExcel_RichText();
    $objRichText2->createText("black text\n");

    $objRed = $objRichText2->createTextRun("red text");
    $objRed->getFont()->setColor( new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED  ) );

    $objPHPExcel->getActiveSheet()->getCell("C14")->setValue($objRichText2);
    $objPHPExcel->getActiveSheet()->getStyle("C14")->getAlignment()->setWrapText(true);


    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

    // Rename worksheet
    echo date('H:i:s') , " Rename worksheet" , EOL;
    $objPHPExcel->getActiveSheet()->setTitle('Datatypes');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Save Excel 2007 file
    echo date('H:i:s') , " Write to Excel2007 format" , EOL;
    $callStartTime = microtime(true);

    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save(str_replace('.php', '.xlsx', $filename));
    //__FILE__
    $callEndTime = microtime(true);
    $callTime = $callEndTime - $callStartTime;

    

    echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
    echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
    // Echo memory usage
    echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


    echo date('H:i:s') , " Reload workbook from saved file" , EOL;
    $callStartTime = microtime(true);

    $objPHPExcel = PHPExcel_IOFactory::load(str_replace('.php', '.xlsx', $filename));

    $callEndTime = microtime(true);
    $callTime = $callEndTime - $callStartTime;
    echo 'Call time to reload Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
    // Echo memory usage
    echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
    

    /*header('Cache-Control: max-age=0');
    // We'll be outputting an excel file
    header('Content-type: application/vnd.ms-excel; charset=utf-8');

    // It will be called file.xls
    header('Content-Disposition: attachment; filename="PhanVanThanh.xls"');
    // Write file to the browser
    $objWriter->save('php://output');*/
    var_dump($objPHPExcel->getActiveSheet()->toArray());


    // Echo memory peak usage
    echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

    // Echo done
    echo date('H:i:s') , " Done testing file" , EOL;
    echo 'File has been created in ' , getcwd() , EOL;
    }

    public function cellColor($cells,$color, $objPHPExcel){
      
      $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
      ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
      'startcolor' => array('rgb' => $color),
      'endcolor'   => array('rgb' => $color),
      ));
    }



  public function donHangAction()
  {
      $this->layout('layout/giaodien');
      

  }

  public function doanhThuAction()
  {
      $this->layout('layout/giaodien');
  }

  }

 

    
?>
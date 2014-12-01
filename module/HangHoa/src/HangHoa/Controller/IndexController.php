<?php namespace HangHoa\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 use HangHoa\Entity\SanPham;
 use HangHoa\Entity\PhieuNhap;
 use HangHoa\Entity\CTPhieuNhap;
 use HangHoa\Entity\HoaDon;
 use HangHoa\Entity\CTHoaDon;
 use HangHoa\Entity\GiaXuat;
 use HangHoa\Form\CreateSanPhamForm;

 use HangHoa\Form\XuatHoaDonForm;

 use HangHoa\Form\CreateNhapHangForm;
 use HangHoa\Form\FileForm;
 use Zend\Validator\File\Size;

 use Zend\Stdlib\AbstractOptions;
 
 use S3UTaxonomy\Form\CreateTermTaxonomyForm;
 
 use PHPExcel;
 use PHPExcel_IOFactory;

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
 	}
  

  public function hangHoaAction()
  {
    $this->layout('layout/giaodien'); 
    $entityManager=$this->getEntityManager();    
    $form= new FileForm($entityManager);
    $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll(); 
    return array(
      'sanPhams'=>$sanPhams,
      'form'=>$form,
    );
  }

  public function locHangHoaAction()
  {
    $this->layout('layout/giaodien'); 
    $entityManager=$this->getEntityManager();      
    $request=$this->getRequest();
    if($request->isPost())
    {
      $tam=array();
      if($request->getPost()['dieuKienLoc'])    
      {
        if($request->getPost()['locHangHoa']=='locTheoLoaiHang')
        {
          //die(var_dump('Lọc theo loại hàng'));
          $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll(); 
          foreach ($sanPhams as $sanPham) 
          {
            if($sanPham->getIdLoai()->getTermId()->getName()==$request->getPost()['dieuKienLoc'])
            {
              $tam[]=$sanPham;
            }
            
          }
          $sanPhams=$tam;
          //die(var_dump($sanPhams));          
        }
        elseif($request->getPost()['locHangHoa']=='locTheoNhanHang')
        {
           //die(var_dump('Lọc theo nhãn hàng'));
          $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.nhan=\''.$request->getPost()['dieuKienLoc'].'\'');
          $sanPhams = $query->getResult(); // array of CmsArticle objects    
          
        }
      }
      else
      {
        $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll();         
      }      
    }
    else
    {
      $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll();         
    } 
    return array('sanPhams'=>$sanPhams);
  }

  // xem chi tiết sản phẩm
  public function sanPhamAction()
  {
     $id = (int) $this->params()->fromRoute('id', 0);
     if (!$id) {
         return $this->redirect()->toRoute('hang_hoa/crud', array(
             'action' => 'hangHoa',
         ));
     }  
     $this->layout('layout/giaodien');  
     $entityManager=$this->getEntityManager();     
     $form= new CreateSanPhamForm($entityManager);     
     $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($id); 
     $form->bind($sanPhams);

     $taxonomyLoai=$this->TaxonomyFunction();
     $loais=$taxonomyLoai->getListChildTaxonomy('danh-muc-hang-hoa');// đưa vào taxonomy dạng slug
    
     $taxonomyDonViTinh=$this->TaxonomyFunction();
     $donViTinhs=$taxonomyDonViTinh->getListChildTaxonomy('don-vi-tinh');// đưa vào taxonomy dạng slug

     $taxonomyKenhPhanPhoi=$this->TaxonomyFunction();
     $kenhPhanPhois=$taxonomyKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug


     $request = $this->getRequest();

     if($request->isPost())
     {
        $post = array_merge_recursive(
              $request->getPost()->toArray(),
              $request->getFiles()->toArray()
        );
        $form->setData($request->getPost());
        if($form->isValid())
        {
          //die(var_dump($request->getPost()));
          if($post['san-pham']['hinhAnh']['error']==0)
          {
            // xóa bỏ hình củ trong img
            $mask =__ROOT_PATH__.'/public/img/'.$sanPhams->getHinhAnh();
            array_map( "unlink", glob( $mask ) );
            // tạo lại tên mới
            $uniqueToken=md5(uniqid(mt_rand(),true));
            $newName=$uniqueToken.'_'.$post['san-pham']['hinhAnh']['name'];
            // lưu vào cơ sở dữ liệu với tên hình là tên vừa tạo ở trên
            $sanPhams->setHinhAnh($newName);
            // di chuyển hình ảnh vào img            
            $filter = new \Zend\Filter\File\Rename("./public/img/".$newName);
            $filter->filter($post['san-pham']['hinhAnh']);
          }
          $entityManager->flush();

          // sửa giá xuất từng kênh phân phối của sản phẩm
          foreach ($kenhPhanPhois as $kenhPhanPhoi) 
          {
            if($kenhPhanPhoi['cap']>0)
            {
              $query=$entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham='.$id.' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);
              $giaXuats=$query->getResult();
              foreach ($giaXuats as $giaXuat)
              {
                $giaXuat->setGiaXuat($request->getPost()[$kenhPhanPhoi['termTaxonomyId']]);
                $entityManager->flush();
              }                          
            }
          }
        }
        else
        {
          die(var_dump($form->getMessages()));
        }
     } 
     return array(
       'sanPhams'=>$sanPhams,
       'form' =>$form,
       'donViTinhs'=>$donViTinhs,
       'loais'=>$loais,
       'kenhPhanPhois'=>$kenhPhanPhois,
     );

  }

  public function bangGiaAction()
  {
    $this->layout('layout/giaodien');  
    $entityManager=$this->getEntityManager();
    $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll(); 

    $taxonomyLoai=$this->TaxonomyFunction();
    $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug


    return array(
       'sanPhams'=>$sanPhams,
       'kenhPhanPhois'=>$kenhPhanPhois,
     );

  }

  public function nhapHangAction()
  {
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();     
    $form= new CreateNhapHangForm($entityManager);
    //$sanPham= new SanPham();
    $phieuNhap= new PhieuNhap();
    //$chiTietPhieuNhap= new CTPhieuNhap();
    $form->bind($phieuNhap);

    $request = $this->getRequest();
    if($request->isPost())
    {
      $form->setData($request->getPost());
      if($form->isValid())
      {
        $user=$entityManager->getRepository('Application\Entity\SystemUser')->find(1);
        $phieuNhap->setIdUserNv($user);
        $entityManager->persist($phieuNhap);

        $entityManager->flush();        
        
      }
      
    }
    return array(       
       'form' =>$form,       
     );
  }   

  // set lại id user nhân viên
  public function xuatHangAction()
  {
    $this->layout('layout/giaodien');  
    $entityManager=$this->getEntityManager();     
    $form= new XuatHoaDonForm($entityManager);
    $hoaDon = new HoaDon();
    $form->bind($hoaDon);

    $request = $this->getRequest();
    if($request->isPost()){

      $form->setData($request->getPost());
      if($form->isValid()){        
        foreach ($hoaDon->getCtHoaDons() as $chiTietHoaDon) {
          $soLuongXuat=$chiTietHoaDon->getSoLuong();
          $soLuongTon=$chiTietHoaDon->getIdSanPham()->getTonKho();
          $soLuongConLai=$soLuongTon-$soLuongXuat;          
          $chiTietHoaDon->getIdSanPham()->setTonKho($soLuongConLai);
        }
        
        $entityManager->persist($hoaDon);
        $entityManager->flush();


        $mHD=$hoaDon->getMaHoaDon();

        
        $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.maHoaDon=\''.$mHD.'\'');
        $hoaDons=$query->getResult();

        $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh')); 
        $y=$datetime->format('Y');$m=$datetime->format('m');
        $mY=$m.$y[2].$y[3];

        $idHD=$hoaDons[0]->getIdHoaDon();
        if($idHD<10)
        {
          $newMaHoaDon=$mY.'-'.'000'.$idHD;

        }
        if ($idHD>=10&&$idHD<100) {
          $newMaHoaDon=$mY.'-'.'00'.$idHD;          
        }
        if ($idHD>=100&&$idHD<1000) {
          $newMaHoaDon=$mY.'-'.'0'.$idHD;    
        }
        if($idHD>=1000)
        {
          $newMaHoaDon=$mY.'-'.$idHD;  
        }
        $hoaDon=$hoaDons[0];        

        $hoaDon->setMaHoaDon($newMaHoaDon);

        $entityManager->flush();
        return $this->redirect()->toRoute('hang_hoa/crud', array(
             'action' => 'xuatHang',
         ));
      }      
    }
    return array('form'=>$form);
  }

  public function themSanPhamAction()
  {

    $this->layout('layout/giaodien'); 

    $entityManager=$this->getEntityManager();
    $sanPham=new SanPham();
    $form= new CreateSanPhamForm($entityManager);
    $form->bind($sanPham);

    $taxonomyLoai=$this->TaxonomyFunction();
    $loais=$taxonomyLoai->getListChildTaxonomy('danh-muc-hang-hoa');// đưa vào taxonomy dạng slug
    
    $taxonomyDonViTinh=$this->TaxonomyFunction();
    $donViTinhs=$taxonomyDonViTinh->getListChildTaxonomy('don-vi-tinh');// đưa vào taxonomy dạng slug

    $request = $this->getRequest();

    if($request->isPost())
    {
      $post = array_merge_recursive(
              $request->getPost()->toArray(),
              $request->getFiles()->toArray()
          ); 
      //var_dump($sanPham);
      $form->setData($request->getPost()); 
      //var_dump($form);
      if ($form->isValid()){
        //die(var_dump($sanPham));
        $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
        $queryBuilder = $repository->createQueryBuilder('sp');
        $queryBuilder->add('where','sp.maSanPham=\''.$sanPham->getMaSanPham().'\'');
        $query = $queryBuilder->getQuery(); 
        $maSanPham = $query->execute();
        if(!$maSanPham)
        {
          if ($post['san-pham']['hinhAnh']['error']==0) {
            $uniqueToken=md5(uniqid(mt_rand(),true));          
            $newName=$uniqueToken.'_'.$post['san-pham']['hinhAnh']['name'];
            $filter = new \Zend\Filter\File\Rename("./public/img/".$newName);
            $filter->filter($post['san-pham']['hinhAnh']);
            $sanPham->setHinhAnh($newName);
          }
          else
          {
            $sanPham->setHinhAnh('photo_default.png');
          }     
          
          $sanPham->setTonKho(0);
          $sanPham->setGiaNhap(0);
          $entityManager->persist($sanPham);
          $entityManager->flush(); 

          $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
          $queryBuilder = $repository->createQueryBuilder('sp');
          $queryBuilder->add('where','sp.maSanPham=\''.$sanPham->getMaSanPham().'\'');
          $query = $queryBuilder->getQuery(); 
          $sanPhams = $query->execute();
          

          $taxonomyLoai=$this->TaxonomyFunction();
          $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug
          
          foreach ($kenhPhanPhois as $kenhPhanPhoi) {
            if($kenhPhanPhoi['cap']>0)
            {
              $giaXuat=new GiaXuat();
              $giaXuat->setIdGiaXuat('');
              $giaXuat->setIdSanPham($sanPhams[0]->getIdSanPham());
              $gx=(int)$sanPhams[0]->getGiaNhap()+(((int)$sanPhams[0]->getGiaNhap()*(int)$kenhPhanPhoi['description'])/100);
              $giaXuat->setGiaXuat($gx);
              $giaXuat->setIdKenhPhanPhoi($kenhPhanPhoi['termTaxonomyId']);
              
              $entityManager->persist($giaXuat);
              $entityManager->flush(); 
            }
            
          }
          
          return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
        }
        else
        {
          return array(
            'form' => $form, 
            'loais'=>$loais,
            'donViTinhs'=>$donViTinhs,
            'kiemTraTonTai'=>1,
          );          
        }
      }      
    }

    return array(
      'form' => $form, 
      'loais'=>$loais,
      'donViTinhs'=>$donViTinhs,
      'kiemTraTonTai'=>0,
    ); 

  }

 	public function addAction()
 	{
 	}

 	public function editAction()
 	{   
 	}

 	public function deleteAction()
 	{        
  }

  public function searchKhachHangAction()
  {
    $response=array();

    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $tenKhachHang=$data['tenKhachHang'];
      if($tenKhachHang)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.hoTen LIKE :ten');
        $query->setParameter('ten','%'.$tenKhachHang.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $khachHangs = $query->getResult(); // array of CmsArticle objects 
        foreach ($khachHangs as $khachHang) {
          $response[]=array(
            'idKhachHang'=>$khachHang->getIdDoiTac(),
            'tenKhachHang'=>$khachHang->getHoTen(),
            'diaChiKhachHang'=>$khachHang->getDiaChi(),
            'kenhPhanPhoi'=>$khachHang->getIdKenhPhanPhoi()->getTermTaxonomyId(),
            //'chietKhau'=>$khachHang->getIdKenhPhanPhoi()->getDescription(),
          );
        }
      }
    }

    $json = new JsonModel($response);
    return $json;
  }

  public function searchSanPhamAction()
  {
    $response=array();
    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $maHang=$data['maHang'];
      if($maHang)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.maSanPham LIKE :maHang');
        $query->setParameter('maHang','%'.$maHang.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $sanPhams = $query->getResult(); // array of CmsArticle objects         

        $pluginKenhPhanPhoi=$this->TaxonomyFunction();
        $kenhPhanPhois=$pluginKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug
          

        foreach ($sanPhams as $sanPham) {

          foreach ($kenhPhanPhois as $kenhPhanPhoi) {
            if($kenhPhanPhoi['cap']>0)
            {
              $query=$entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham='.$sanPham->getIdSanPham().' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);
              $giaXuats=$query->getResult();
              foreach ($giaXuats as $gx) {
                $giaXuat[$kenhPhanPhoi['termTaxonomyId']]=$gx->getGiaXuat();
              }
              
            }            
          }
          $soKenhPhanPhoi=count($giaXuat);
          $response[]=array(
            'idSanPham'=>$sanPham->getIdSanPham(),
            'maHang'=>$sanPham->getMaSanPham(),
            'tenSanPham'=>$sanPham->getTenSanPham(),
            'donViTinh'=>$sanPham->getDonViTinh(),
            'tonKho'=>$sanPham->getTonKho(),
            'giaXuat'=>$giaXuat,
          );
        }
      }
    }
    $json = new JsonModel($response);
    return $json;
  }

  public function searchNhaCungCapAction()
  {
    $response=array();
    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $nhaCungCap=$data['nhaCungCap'];
      if($nhaCungCap)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.loaiDoiTac=46 and dt.hoTen LIKE :hoTen');
        $query->setParameter('hoTen','%'.$nhaCungCap.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $nhaCungCaps = $query->getResult(); // array of CmsArticle objects           
        foreach ($nhaCungCaps as $ncc) {
          $response[]=array(
            'idDoiTac'=>$ncc->getIdDoiTac(),
            'hoTen'=>$ncc->getHoTen(),
          );
        }
      }
    }
    $json = new JsonModel($response);
    return $json;
  }  

  public function importHangHoaAction()
  {
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();
    $sanPham=new SanPham();
    $form= new CreateSanPhamForm($entityManager);
    $form->bind($sanPham);

    $request = $this->getRequest();        
    if($request->isPost())
    {
      $post = array_merge_recursive(
        $request->getPost()->toArray(),
        $request->getFiles()->toArray()
      );

      $fileType=$post['file']['type'];     
      if($fileType=='application/vnd.ms-excel')
      {
        $objPHPExcel = new PHPExcel();
        $tmpName=$post['file']['tmp_name'];
        $objLoad = PHPExcel_IOFactory::load($tmpName);        

        $listMaSanPham=array();
        $taxonomyLoai=$this->TaxonomyFunction();
        $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {
            //$worksheetTitle     = $worksheet->getTitle();
            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'            
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            for ($row = 2; $row <= $highestRow; ++ $row) {
                //echo '<tr>';
                for ($col = 2; $col < $highestColumnIndex; ++ $col) {
                    
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    if($col==2)
                    {
                      $maSanPham = $cell->getValue();
                    }
                    if($col==3)
                    {
                      $soLuong = $cell->getValue();
                    }
                    if($col==4)
                    {
                      $giaNhap = $cell->getValue();
                    }                    
                }
                $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.maSanPham =\''.trim($maSanPham).'\'');
                $SanPhams = $query->getResult();                    
                //die(var_dump($maSanPham));
                if($SanPhams)
                {
                  foreach ($SanPhams as $SanPham)
                  {
                  //Cập nhật bảng SẢN PHẨM
                    $tonKho=(int)($SanPham->getTonKho())+$soLuong;
                    $SanPham->setTonKho($tonKho);
                    $SanPham->setGiaNhap($giaNhap);
                    $entityManager->flush();

                  //Cập nhật bảng CHI TIẾT PHIẾU NHẬP
                    $idSanPham=$SanPham->getIdSanPham();
                    $query = $entityManager->createQuery('SELECT pn FROM HangHoa\Entity\CTPhieuNhap pn WHERE pn.idSanPham ='.$idSanPham);
                    $PhieuNhaps = $query->getResult();
                    foreach ($PhieuNhaps as $PhieuNhap)
                    {
                      $PhieuNhap->setSoLuong($soLuong);
                      $PhieuNhap->setGiaNhap($giaNhap);
                      $entityManager->flush();                      
                    }
                  //Cập nhật bảng GIÁ XUẤT
                    // đưa vào taxonomy dạng slug
                    
                    foreach ($kenhPhanPhois as $kenhPhanPhoi) 
                    {
                      if($kenhPhanPhoi['cap']>0)
                      {

                        $query = $entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham ='.$idSanPham.' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);   
                        $giaXuats = $query->getResult();
                        foreach ($giaXuats as $giaXuat) {  
                          $gx=(int)$giaNhap+(((int)$giaNhap*(int)$kenhPhanPhoi['description'])/100);
                          $giaXuat->setGiaXuat($gx);
                          $entityManager->flush();
                        }
                      }
                    }

                  }                  
                }
                else
                {
                  //lưu lại các mã sản phẩm chưa có trong CSDL->xuất thông báo     
                  $listMaSanPham[]=$maSanPham;                  
                }                
            }
        }
        return array(
          'listMaSanPham' => $listMaSanPham,
          'import'=>1,
        ); 
      }
      else
      {
        return array(
          'listMaSanPham' => array(),
          'import'=>0,         
        );
      }
    }
    else
    {
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
    }
  }

  public function exportHangHoaAction()
  {
    $entityManager=$this->getEntityManager();

    $filename='data_hang_hoa.xlsx';

    
    /** Error reporting */
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE); 
    ini_set('display_startup_errors', TRUE); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    // Create new PHPExcel object
    
    $objPHPExcel = new PHPExcel();

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

    $objPHPExcel->getActiveSheet()->setCellValue('A2', 'HÀNG HÓA');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
    $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                  
   

    $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Sản Phẩm')
                                  ->setCellValue('B4', 'Mã sản phẩm')
                                  ->setCellValue('C4', 'Tồn kho')
                                  ->setCellValue('D4', 'Loại')
                                  ->setCellValue('E4', 'Nhãn hàng')                                  
                                  ->getStyle('A4:E4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll();
    foreach ($sanPhams as $key=>$sanPham) {
      //die(var_dump($sanPham));
      $index=$key+5;
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$index, $sanPham->getTenSanPham())
                                    ->setCellValue('B'.$index, $sanPham->getMaSanPham())
                                    ->setCellValue('C'.$index, $sanPham->getTonKho())
                                    ->setCellValue('D'.$index, $sanPham->getIdLoai()->getTermId()->getName())
                                    ->setCellValue('E'.$index, $sanPham->getNhan());
      $objPHPExcel->getActiveSheet()->getStyle('C'.$index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }

    // Rename worksheet    
    $objPHPExcel->getActiveSheet()->setTitle('data_hang_hoa');
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    // Save Excel 2007 file
    $callStartTime = microtime(true);

    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save(str_replace('.php', '.xlsx', $filename));   

    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));                                  

  }

  public function exportBangGiaAction()
  {
    $entityManager=$this->getEntityManager();

    $filename='data_bang_gia.xlsx';

    
    /** Error reporting */
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE); 
    ini_set('display_startup_errors', TRUE); 
    date_default_timezone_set('Europe/London');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    // Create new PHPExcel object
    
    $objPHPExcel = new PHPExcel();

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

    $taxonomyLoai=$this->TaxonomyFunction();
    $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug
    $soCot=count($kenhPhanPhois)+3;// tổng số cột dạng int
    $soCotAscii=ord($soCot);// tổng cộng số cột dạng ascii
    $cotCuoiCungAscii=$soCotAscii+16;// cột cuối cùng dạng ascii
    $cotCuoiCung=chr($cotCuoiCungAscii);// cuột cuối cùng dạng string

    
    $objPHPExcel->getActiveSheet()->setCellValue('A2', 'BẢNG GIÁ');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:'.$cotCuoiCung.'2');
    $objPHPExcel->getActiveSheet()->getStyle('A2:'.$cotCuoiCung.'2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A2:'.$cotCuoiCung.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                  
    // xuất giá trị dòng title ra excel tại dòng thứ 4
    $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Sản phẩm');
    $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Mã sản phẩm');
    $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Giá nhập');
    $objPHPExcel->getActiveSheet()->setCellValue('D4', 'Đơn vị tính');
    foreach ($kenhPhanPhois as $key=>$kenhPhanPhoi) {
      if($kenhPhanPhoi['cap']>0)
      {
        $sttCot=$key+4;
        $cotHienTaiAscii=ord($sttCot)+16;
        $cotHienTai=chr($cotHienTaiAscii);// cột hiện tại dạng string        
        $objPHPExcel->getActiveSheet()->setCellValue($cotHienTai.'4', $kenhPhanPhoi['termId']['name']);
      }
    }
    $objPHPExcel->getActiveSheet()->getStyle('A4:'.$cotCuoiCung.'4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A4:'.$cotCuoiCung.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // xuất dữ liệu trong csdl ra excel từ dùng số 5 trở đi
    $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll();
    foreach ($sanPhams as $key=>$sanPham) {
      $sttDong=$key+5;
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttDong, $sanPham->getTenSanPham());
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttDong, $sanPham->getMaSanPham());
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttDong, $sanPham->getGiaNhap());
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttDong, $sanPham->getDonViTinh());
      foreach ($kenhPhanPhois as $key=>$kenhPhanPhoi) 
      {
        if($kenhPhanPhoi['cap']>0)
        {
          $sttCot=$key+4;
          $cotHienTaiAscii=ord($sttCot)+16;
          $cotHienTai=chr($cotHienTaiAscii);// cột hiện tại dạng string 

          $query=$entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham='.$sanPham->getIdSanPham().' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']); 
          $giaXuats=$query->getResult();
          if($giaXuats)
          {
            $objPHPExcel->getActiveSheet()->setCellValue($cotHienTai.$sttDong, $giaXuats[0]->getGiaXuat());
          }
        }
      }
    }

    // Rename worksheet    
    $objPHPExcel->getActiveSheet()->setTitle('data_bang_gia');
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    // Save Excel 2007 file
    $callStartTime = microtime(true);

    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save(str_replace('.php', '.xlsx', $filename));   

    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'bangGia'));                                  

  }
  
 }
?>
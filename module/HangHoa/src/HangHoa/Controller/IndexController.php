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
 use Barcode\Entity\Barcode;

 use HangHoa\Form\XuatHoaDonForm;

 use HangHoa\Form\CreateNhapHangForm;
 use HangHoa\Form\FileForm;
 use Zend\Validator\File\Size;

 use Zend\Stdlib\AbstractOptions;
 
 use S3UTaxonomy\Form\CreateTermTaxonomyForm;
 
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
 
 class IndexController extends AbstractActionController
 {
 	private $entityManager;
  
  public function getEntityManager()
  {
    // kiểm tra đăng nhập
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     if(!$this->entityManager)
     {
      $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
     }
     return $this->entityManager;
  }
  
 	public function indexAction()
 	{
    // kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $this->layout('layout/giaodien');
 	}
  

  public function hangHoaAction() 
  {
    // kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      $form= new FileForm($entityManager);
      $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho);
      $sanPhams=$query->getResult();
      return array(
        'sanPhams'=>$sanPhams,
        'form'=>$form,
      );
  }

  public function locHangHoaAction()
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $request=$this->getRequest();
    if($request->isPost())
    {
      $tam=array();
      $post=$request->getPost();
      $dieuKienLoc=$post['dieuKienLoc'];
      $locHangHoa=$post['locHangHoa'];

      if($dieuKienLoc)  // nếu có nhập điều kiện lọc  
      {
        if($locHangHoa=='locTheoLoaiHang')
        {
          $query=$entityManager->createQuery('SELECT zft FROM S3UTaxonomy\Entity\ZfTerm zft WHERE zft.name LIKE :dieuKienLoc');
          $query->setParameter('dieuKienLoc','%'.$dieuKienLoc.'%');
          $zfTerms=$query->getResult();

          $termIds=' ';            
          if($zfTerms)
          {
            foreach ($zfTerms as $zfTerm) {
              $termIds.='zfttx.termId='.$zfTerm->getTermId().' and ';
            }
          }
          if($termIds==' ')
          {
            $termIds=' ';
          }
          

          $query=$entityManager->createQuery('SELECT zfttx FROM S3UTaxonomy\Entity\ZfTermTaxonomy zfttx WHERE '.$termIds.' zfttx.taxonomy=\''.'danh-muc-hang-hoa'.'\'');
          $zfTermTaxonomys=$query->getResult();

          $idLoais=' ';
          $soId=count($zfTermTaxonomys);
          $i=0;
          if($zfTermTaxonomys)
          {
            foreach ($zfTermTaxonomys as $key=>$zfTermTaxonomy) {
              $idLoais.='sp.idLoai='.$zfTermTaxonomy->getTermTaxonomyId();
              $i++;
              if($i>0&&$i<$soId&&$soId>1)
              {
                $idLoais.=' or ';
              }

            }

          }
          else
          {
            $idLoais.=' 1=1 ';
          }
          //die(var_dump($idLoais));
          $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE '.$idLoais);
          $sanPhams=$query->getResult();
        }
        elseif($locHangHoa=='locTheoNhanHang')
        {
           //die(var_dump('Lọc theo nhãn hàng'));
          $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.nhan LIKE :dieuKienLoc');
          $query->setParameter('dieuKienLoc','%'.$dieuKienLoc.'%');
          $sanPhams = $query->getResult(); // array of CmsArticle objects    
          
        }
      }
      else // nếu không có nhập điều kiện lọc thì lấy ra hết
      {        
        $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho);
        $sanPhams=$query->getResult();
      }      
    }
    else
    {        
      $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho);
      $sanPhams=$query->getResult();
    } 
    return array('sanPhams'=>$sanPhams);
  }

  // xem chi tiết sản phẩm
  public function sanPhamAction()
  {
    
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    // id đối tác
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) {
        return $this->redirect()->toRoute('hang_hoa/crud');
    }  
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

     $form= new CreateSanPhamForm($entityManager);     
     $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($id); 

     // kiểm tra có sản phẩm này có thuộc kho của user hiện tại không
     if($sanPhams->getKho()!=$this->zfcUserAuthentication()->getIdentity()->getKho())
     {
       return $this->redirect()->toRoute('hang_hoa/crud', array(
             'action' => 'hangHoa',
         ));
     }
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
                $post=$request->getPost();
                $giaXuat->setGiaXuat($post[$kenhPhanPhoi['termTaxonomyId']]);
                $entityManager->flush();
              }                          
            }
          }
        }
        else{
          //die(var_dump($form->getMessages()));
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
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();


    $form= new FileForm($entityManager);
    $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho);
    $sanPhams=$query->getResult();

    $taxonomyLoai=$this->TaxonomyFunction();
    $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');


    return array(
       'sanPhams'=>$sanPhams,
       'kenhPhanPhois'=>$kenhPhanPhois,
       'form'=>$form,
     );

  }

  public function nhapHangAction()
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

   
    $form= new CreateNhapHangForm($entityManager);    
    $phieuNhap= new PhieuNhap();    
    $form->bind($phieuNhap);
    $request = $this->getRequest();
    if($request->isPost())
    {      
      $form->setData($request->getPost());      
      if($form->isValid())
      {
        $taxonomyLoai=$this->TaxonomyFunction();
        $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');     

        foreach ($phieuNhap->getCtPhieuNhaps() as $cTPhieuNhap) 
        { 
          $giaNhap=$cTPhieuNhap->getGiaNhap();
          $soLuong=$cTPhieuNhap->getSoLuong();
          $idUserNv=$this->zfcUserAuthentication()->getIdentity();
          //Cập nhật Phiếu Nhập
          $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($idUserNv);
          $phieuNhap->setIdUserNv($user);
          $entityManager->persist($phieuNhap);
          $entityManager->flush();

          $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh')); 
          $y=$datetime->format('Y');          
          $m=$datetime->format('m');
          $mY=$m.$y[2].$y[3];

          $idPhieuNhap=$phieuNhap->getIdPhieuNhap();
          if($idPhieuNhap<10)
          {
            $maPhieuNhap=$mY.'-'.'000'.$idPhieuNhap;
          }
          if($idPhieuNhap>=10&&$idPhieuNhap<100)
          {
            $maPhieuNhap=$mY.'-'.'00'.$idPhieuNhap;
          }
          if($idPhieuNhap>100&&$idPhieuNhap<1000)
          {
            $maPhieuNhap=$mY.'-'.'0'.$idPhieuNhap;
          }
          if($idPhieuNhap>1000)
          {
            $maPhieuNhap=$mY.'-'.$idPhieuNhap;
          }
          $phieuNhap->setMaPhieuNhap($maPhieuNhap);
          $phieuNhap->setKho($idKho);
          $entityManager->flush();          
          
          //Cập nhật Sản Phẩm
          $idSanPham=$cTPhieuNhap->getIdSanPham()->getIdSanPham();  

          $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.idSanPham =\''.$idSanPham.'\''); // SUAKHO
          $sanPhams = $query->getResult();
          if($sanPhams)
          {
            foreach ($sanPhams as $sanPham)
            {
              $tonKho=(float)($sanPham->getTonKho())+$soLuong;
              $sanPham->setTonKho($tonKho);
              $sanPham->setGiaNhap($giaNhap);
              $entityManager->flush(); 

               $loaiGia=$sanPham->getLoaiGia();  
               $giaBia=$sanPham->getGiaBia(); 
               $chietKhau=$sanPham->getChiecKhau(); 

              foreach ($kenhPhanPhois as $kenhPhanPhoi) 
              {
                if($kenhPhanPhoi['cap']>0)
                {
                  $query = $entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham ='.$idSanPham.' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);   
                  $giaXuats = $query->getResult();
                  $ck=$this->getChietKhau($idKho,$kenhPhanPhoi['termTaxonomyId']);
                  foreach ($giaXuats as $giaXuat) { 
                    if((float)$loaiGia==1)
                    {
                      $loiNhuan=(float)(((float)$giaBia*(float)$ck)/100);
                      $gx=(float)$giaBia-(float)$loiNhuan;
                    } 
                    else
                    {
                      
                      $gx=(float)$giaNhap+(((float)$giaNhap*(float)$ck)/100);
                    }                    
                    $giaXuat->setGiaXuat($gx);
                    $entityManager->flush();
                  }
                }
              }           
            }
          }
          else
          {
            //Có lỗi trong quá trình cập nhật Sản Phẩm
          }
        }
        $this->flashMessenger()->addSuccessMessage('Nhập hàng thành công!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'nhapHang'));        
      }
      else
      {
        //die(var_dump($form->getMessages()['phieu-nhap']['ctPhieuNhaps']));
        $this->flashMessenger()->addErrorMessage('Nhập hàng thất bại!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'nhapHang'));
      }
    }    
    return array(       
       'form' =>$form,       
     );
  }   

  // set lại id user nhân viên
  public function xuatHangAction()
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $form= new XuatHoaDonForm($entityManager);
    $hoaDon = new HoaDon();
    $form->bind($hoaDon);

    $request = $this->getRequest();
    if($request->isPost()){
      $form->setData($request->getPost());      
      if($form->isValid()){
        //die(var_dump($hoaDon));
        $idUserNv=$this->zfcUserAuthentication()->getIdentity();
        $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($idUserNv);
        $hoaDon->setIdUserNv($user);
        foreach ($hoaDon->getCtHoaDons() as $chiTietHoaDon) {          
          $soLuongXuat=$chiTietHoaDon->getSoLuong();
          $soLuongTon=$chiTietHoaDon->getIdSanPham()->getTonKho();
          $soLuongConLai=$soLuongTon-$soLuongXuat;          
          $chiTietHoaDon->getIdSanPham()->setTonKho($soLuongConLai);
          $giaNhap=$chiTietHoaDon->getIdSanPham()->getGiaNhap();
          $chiTietHoaDon->setGiaNhap($giaNhap);
        }
        //die(var_dump($hoaDon->getCtHoaDons()));
        $hoaDon->setKho($idKho); // SUAKHO
        
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
        $this->flashMessenger()->addSuccessMessage('Xuất hàng thành công!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'xuatHang'));        
      }      
    }
    return array('form'=>$form);
  }

  public function themSanPhamAction()
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }     
    $idKho=1;
    if($this->zfcUserAuthentication()->hasIdentity())
    { 
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    }

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
      $form->setData($request->getPost());      
      if ($form->isValid())
      {
        //Kiểm tra mã sản phẩm đã tồn tại chưa
        $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
        $queryBuilder = $repository->createQueryBuilder('sp');
        $queryBuilder->add('where','sp.kho='.$idKho.' and sp.maSanPham=\''.$post['san-pham']['maSanPham'].'\'');
        $query = $queryBuilder->getQuery(); 
        $maSanPham = $query->execute();
        if(!$maSanPham)
        {
          //-------Kiểm tra loại mã vạch đã thiết lập
            $repository = $entityManager->getRepository('Barcode\Entity\Barcode');
            $queryBuilder = $repository->createQueryBuilder('b');
            $queryBuilder->add('where','b.state=1');
            $query = $queryBuilder->getQuery(); 
            $loaiMaVachs = $query->execute();
            foreach ($loaiMaVachs as $loaiMaVach) {
              $loaiMV=$loaiMaVach->getTenBarcode();
              $length=$loaiMaVach->getLength();          
            }
          //-----------------------------------------

          //-------Kiểm tra loại mã vạch
            if((int)($post['san-pham']['checkMaVach'])==1)
            {
              $mang=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
              $a='';
              if($loaiMV=='Code128')
              {            
                do
                {
                  for ($i = 0; $i<15; $i++) 
                  {
                      $a .= mt_rand(0,9);
                  }
                  $maVach=$a;

                  $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
                  $queryBuilder = $repository->createQueryBuilder('sp');
                  $queryBuilder->add('where','sp.kho='.$idKho.' and sp.maVach=\''.$maVach.'\'');
                  $query = $queryBuilder->getQuery(); 
                  $maVachSanPham = $query->execute();
                }
                while($maVachSanPham);
              }

              if($loaiMV=='Codabar')
              {            
                do
                {
                  $rand1=$mang[rand(0,25)];
                  $rand2=$mang[rand(0,25)];            
                  for ($i = 0; $i<13; $i++) 
                  {
                      $a .= mt_rand(0,9);
                  }        
                  $maVach=$rand1.$a.$rand2;

                  $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
                  $queryBuilder = $repository->createQueryBuilder('sp');
                  $queryBuilder->add('where','sp.kho='.$idKho.' and sp.maVach=\''.$maVach.'\'');
                  $query = $queryBuilder->getQuery(); 
                  $maVachSanPham = $query->execute();
                }
                while($maVachSanPham);
              }
              if($loaiMV=='Code25')
              {
                do
                {
                  for ($i = 0; $i<15; $i++) 
                  {
                      $a .= mt_rand(0,9);
                  }        
                  $maVach=$a;

                  $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
                  $queryBuilder = $repository->createQueryBuilder('sp');
                  $queryBuilder->add('where','sp.kho='.$idKho.' and sp.maVach=\''.$maVach.'\'');
                  $query = $queryBuilder->getQuery(); 
                  $maVachSanPham = $query->execute();
                }
                while($maVachSanPham);
              }
              if($loaiMV=='Ean13')
              {
                do
                {
                  for ($i = 0; $i<12; $i++) 
                  {
                      $a .= mt_rand(0,9);
                  }        
                  $maVach=$a;

                  $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
                  $queryBuilder = $repository->createQueryBuilder('sp');
                  $queryBuilder->add('where','sp.kho='.$idKho.' and sp.maVach=\''.$maVach.'\'');
                  $query = $queryBuilder->getQuery(); 
                  $maVachSanPham = $query->execute();
                }
                while($maVachSanPham);
              }
              if($loaiMV=='Code39')
              {
                do
                {
                  for ($i = 0; $i<15; $i++) 
                  {
                      $a .= mt_rand(0,9);
                  }        
                  $maVach=$a;

                  $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
                  $queryBuilder = $repository->createQueryBuilder('sp');
                  $queryBuilder->add('where','sp.kho='.$idKho.' and sp.maVach=\''.$maVach.'\'');
                  $query = $queryBuilder->getQuery(); 
                  $maVachSanPham = $query->execute();
                }
                while($maVachSanPham);
              }

              $sanPham->setMaVach($maVach);
              $query=$entityManager->createQuery('SELECT b FROM Barcode\Entity\Barcode b WHERE b.tenBarcode=\''.$loaiMV.'\'');
              $idBarcodes=$query->getResult();
              foreach ($idBarcodes as $idBarcode) {
                $sanPham->setIdBarcode($idBarcode);
              }
            }
            else
            {
              $query=$entityManager->createQuery('SELECT b FROM Barcode\Entity\Barcode b WHERE b.tenBarcode=0');
              $idBarcodes=$query->getResult();
              foreach ($idBarcodes as $idBarcode) {
                $sanPham->setIdBarcode($idBarcode);
              }
            }
          //----------------------------

          //-------Lưu các thông tin khác
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

            if($sanPham->getLoaiGia()==1)
            {
              $giaNhap=0; 
              if(!$sanPham->getChiecKhau()||$sanPham->getChiecKhau()==null||$sanPham->getChiecKhau()==''||
                 !$sanPham->getGiaBia()||$sanPham->getGiaBia()==null||$sanPham->getGiaBia()=='')
              {
                $giaNhap=0;
              }
              else
              {
                $loiNhuan=((float)$sanPham->getChiecKhau()*(float)$sanPham->getGiaBia())/100;
                $giaNhap=(float)$sanPham->getGiaBia()-(float)$loiNhuan;
              }
              $sanPham->setGiaNhap($giaNhap);
            }
            else
            {
              $sanPham->setLoaiGia(0);
              $sanPham->setChiecKhau(0);
              $sanPham->setGiaBia(0);
            }
                    
            $sanPham->setKho($idKho);          
            $entityManager->persist($sanPham);
            $entityManager->flush();
          //-----------------------------

          //-------Cập nhật mã sản phẩm            
            if((int)($post['san-pham']['loaiMaSanPham'])==1)
            {
              $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.maVach=\''.$sanPham->getMaVach().'\'');
              $sanPhams=$query->getResult();
              foreach ($sanPhams as $sanPham) {
                $id=$sanPham->getIdSanPham();
                if($id<10)
                {
                  $maSP='sp_000'.$id;
                }
                if($id>=10&&$id<100)
                {
                  $maSP='sp_00'.$id;
                }
                if($id>=100&&$id<1000)
                {
                  $maSP='sp_0'.$id;
                }
                if($id>=1000)
                {
                  $maSP='sp_'.$id;
                }
                $sanPham->setMaSanPham($maSP);
                $entityManager->flush();
              }
            }          
          //---------------------------
          $this->flashMessenger()->addSuccessMessage('Thêm sản phẩm thành công!');
          return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
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

  public function getChietKhau($idKho,$idKenhPhanPhoi)
  {
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery('SELECT ck FROM Kho\Entity\ChietKhau ck WHERE ck.idKho='.$idKho.' and ck.idKenhPhanPhoi='.$idKenhPhanPhoi.' and ck.status=0');
    $chietKhaus=$query->getResult();
    $chietKhau=$chietKhaus[0]->getChietKhau();
    return $chietKhau;
  }

  public function searchKhachHangAction()
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();


    $response=array();

    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $tenKhachHang=$data['tenKhachHang'];
      if($tenKhachHang)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.kho='.$idKho.' and kh.loaiDoiTac=45 AND kh.hoTen LIKE :ten');
        $query->setParameter('ten','%'.$tenKhachHang.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $khachHangs = $query->getResult(); // array of CmsArticle objects
        //die(var_dump($khachHangs[0])); 
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
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho(); 
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $response=array();
    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $maHang=$data['maHang'];
      if($maHang)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham LIKE :maHang');
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
            'maVach'=>$sanPham->getMaVach(),
            'tenSanPham'=>$sanPham->getTenSanPham(),
            'donViTinh'=>$sanPham->getDonViTinh(),
            'tonKho'=>$sanPham->getTonKho(),
            'giaNhap'=>$sanPham->getGiaNhap(),
            'loaiGia'=>$sanPham->getLoaiGia(),
            'giaBia'=>$sanPham->getGiaBia(),
            'chietKhau'=>$sanPham->getChiecKhau(),
            'giaXuat'=>$giaXuat,
          );
        }
      }
    }
    $json = new JsonModel($response);
    return $json;
  }

   public function searchSanPhamTheoMaVachAction()
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho(); 
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $response=array();
    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $maVach=$data['maVach'];
      if($maVach)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maVach LIKE :maVach');
        $query->setParameter('maVach','%'.$maVach.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
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
            'maVach'=>$sanPham->getMaVach(),
            'tenSanPham'=>$sanPham->getTenSanPham(),
            'donViTinh'=>$sanPham->getDonViTinh(),
            'tonKho'=>$sanPham->getTonKho(),
            'giaNhap'=>$sanPham->getGiaNhap(),
            'loaiGia'=>$sanPham->getLoaiGia(),
            'giaBia'=>$sanPham->getGiaBia(),
            'chietKhau'=>$sanPham->getChiecKhau(),
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
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $response=array();
    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $nhaCungCap=$data['nhaCungCap'];
      if($nhaCungCap)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=46 and dt.hoTen LIKE :hoTen');
        $query->setParameter('hoTen','%'.$nhaCungCap.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $nhaCungCaps = $query->getResult(); // array of CmsArticle objects           
        foreach ($nhaCungCaps as $ncc) {
          $response[]=array(
            'idDoiTac'=>$ncc->getIdDoiTac(),
            'hoTen'=>$ncc->getHoTen(),
            'diaChi'=>$ncc->getDiaChi(),
          );
        }
      }
    }
    $json = new JsonModel($response);
    return $json;
  }  

  public function importHangHoaAction()
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    

    $request = $this->getRequest();        
    if($request->isPost())
    {
      $post = array_merge_recursive(
        $request->getPost()->toArray(),
        $request->getFiles()->toArray()
      );

      $fileType=$post['file']['type'];
      $fileName=explode('.',$post['file']['name']);      
      $type=$fileName[count($fileName)-1];      
      if($fileType=='application/vnd.ms-excel'||$type=='xls'||$type=='xlsx')
      {
        $objPHPExcel = new PHPExcel();
        $tmpName=$post['file']['tmp_name'];
        $objLoad = PHPExcel_IOFactory::load($tmpName);        

        $listMaSanPham=array();
        $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh'));        
        $taxonomyLoai=$this->TaxonomyFunction();
        $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');        
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {
          $highestRow         = $worksheet->getHighestRow();
          $highestColumn      = $worksheet->getHighestColumn();
          $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);          
          for ($row = 2; $row <= $highestRow; ++ $row) {
              for ($col = 1; $col < $highestColumnIndex; ++ $col) 
              {
                  $cell = $worksheet->getCellByColumnAndRow($col, $row);                  
                  if($col==1)
                  {
                    $maSanPham = $cell->getValue();
                  }
                  if($col==2)
                  {
                    $tenSanPham = $cell->getValue();
                  }
                  if($col==3)
                  {
                    $donViTinh = $cell->getValue();
                  }
                  if($col==5)
                  {
                    $gia = $cell->getValue();
                  }
                  if($col==6)
                  {
                    $nhaCungCap = $cell->getValue();
                  }                  
              }
              $sanPham=new SanPham();
              if(trim($maSanPham)!=''&&$maSanPham!=null&&trim($tenSanPham)!=''&&$tenSanPham!=null&&trim($donViTinh)!=''&&$donViTinh!=null&&is_numeric(trim($gia)))
              {
                $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham =\''.trim($maSanPham).'\'');
                $sanPhams = $query->getResult();
                if(!$sanPhams)
                {
                  $sanPham->setIdSanPham('');
                  $sanPham->setMaSanPham($maSanPham);
                  $sanPham->setTenSanPham($tenSanPham);
                  $sanPham->setMoTa('');                  
                  $sanPham->setNhan('');                  
                  $sanPham->setTonKho('');
                  $sanPham->setGiaNhap($gia);
                  $sanPham->setLoaiGia('0');
                  $sanPham->setGiaBia('');
                  $sanPham->setChiecKhau('');
                  //Thêm mã vạch tự động
                  $sanPham->setKho($idKho);
                  $sanPham->setHinhAnh('photo_default.png');                  
                  $entityManager->persist($sanPham);
                  $entityManager->flush();                  
                  $idSanPham=$sanPham->getIdSanPham();                  

                  foreach ($kenhPhanPhois as $kenhPhanPhoi) {
                    if($kenhPhanPhoi['cap']>0)
                    {
                      $giaXuat=new GiaXuat();
                      $giaXuat->setIdGiaXuat('');
                      $giaXuat->setIdSanPham($sanPham->getIdSanPham());
                      
                      $chietKhau=$this->getChietKhau($idKho,$kenhPhanPhoi['termTaxonomyId']);
                      $gx=(float)$gia+(((float)$gia*(float)$chietKhau)/100);

                      $giaXuat->setGiaXuat(round($gx, 0));
                      $giaXuat->setIdKenhPhanPhoi($kenhPhanPhoi['termTaxonomyId']);
                      $giaXuat->setKho($idKho);
                      
                      $entityManager->persist($giaXuat);
                      $entityManager->flush();                      
                    }        
                  }                  
                }
                else
                {                  
                  //Cập nhật thông tin cho sản phẩm đã có                  
                  foreach ($kenhPhanPhois as $kenhPhanPhoi) 
                  {
                    if($kenhPhanPhoi['cap']>0)
                    {
                      $query = $entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham ='.$sanPhams[0]->getIdSanPham().' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);   
                      $giaXuats = $query->getResult();
                      $chietKhau=$this->getChietKhau($idKho,$kenhPhanPhoi['termTaxonomyId']);
                      foreach ($giaXuats as $giaXuat) {
                        $gx=(float)$gia+(((float)$gia*(float)$chietKhau)/100);                    
                        $giaXuat->setGiaXuat(round($gx, 0));
                        $entityManager->flush();
                      }
                    }
                  }                  
                }
              }
              else
              {
                //Đánh dấu các sản phẩm không thể import
                if($maSanPham!=''||$maSanPham!==null)
                  $listMaSanPham[]=$maSanPham;
                else
                  $listMaSanPham[]='Sản phẩm không có mã sản phẩm';
              }
              //die(var_dump('Stop'));//Mở khi muốn test import 1 hàng trong file excel
          }
        }
                
        $this->flashMessenger()->addSuccessMessage('Import tập tin sản phẩm thành công.');
        if(count($listMaSanPham)==0)
        {
          return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
        }
        else
        {
          return array(
            'listMaSanPham' => $listMaSanPham,          
          );
        }
      }
      else
      {
        $this->flashMessenger()->addErrorMessage('Import hàng hóa không thành công! Tập tin không hợp lệ');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
      }
    }
    else
    {
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
    }
  }

  public function importBangGiaAction()
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $sanPham=new SanPham();
    $request = $this->getRequest();        
    if($request->isPost())
    {
      $post = array_merge_recursive(
        $request->getPost()->toArray(),
        $request->getFiles()->toArray()
      );

      $fileType=$post['file']['type'];
      $fileName=explode('.',$post['file']['name']);      
      $type=$fileName[count($fileName)-1];      
      if($fileType=='application/vnd.ms-excel'||$type=='xls'||$type=='xlsx')
      {
        $objPHPExcel = new PHPExcel();
        $tmpName=$post['file']['tmp_name'];
        $objLoad = PHPExcel_IOFactory::load($tmpName);        

        $listMaSanPham=array();
        $taxonomyLoai=$this->TaxonomyFunction();
        $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {            
          $highestRow         = $worksheet->getHighestRow();
          $highestColumn      = $worksheet->getHighestColumn();
          $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);            
          for ($row = 3; $row <= $highestRow; ++ $row) {
            $listGiaXuat=array();
            for ($col = 2; $col < $highestColumnIndex; ++ $col) {                    
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                if($col==2)
                {
                  $maSanPham = $cell->getValue();
                }                    
                if($col==3)
                {
                  $giaNhap = $cell->getValue();
                }
                if($col>3)
                {
                  $listGiaXuat[] = $cell->getValue();
                }                  
            }
            /*$t='Sách';
            var_dump($t);
            die(var_dump(utf8_decode($t)));*/
            if(trim($giaNhap)==''||$giaNhap==null)
            {
              $listMaSanPham[]=$maSanPham;              
            }
            else
            {
              $soGiaXuat=count($listGiaXuat);              
              $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham =\''.trim($maSanPham).'\'');
              $SanPhams = $query->getResult();              
              if($SanPhams)
              {
                foreach ($SanPhams as $SanPham)
                {                  
                  $idSanPham=$SanPham->getIdSanPham();               
                  $SanPham->setGiaNhap($giaNhap);
                  $entityManager->flush();
                  $i=0;

                  foreach ($kenhPhanPhois as $kenhPhanPhoi) {
                    if($kenhPhanPhoi['cap']>0)
                    {
                      $query = $entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham ='.$idSanPham.' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);   
                      $giaXuats = $query->getResult();
                      foreach ($giaXuats as $giaXuat) 
                      {  
                        if($i<$soGiaXuat)
                        {
                          if($listGiaXuat[$i]!=null||$listGiaXuat[$i]!='')
                          {
                            $giaXuat->setGiaXuat($listGiaXuat[$i]);
                          }
                          else
                          {
                            if($giaXuat->getGiaXuat()==0)
                            {
                              $gx=(int)$giaNhap+(((int)$giaNhap*(int)$kenhPhanPhoi['description'])/100);
                              $giaXuat->setGiaXuat($gx);
                            }                            
                          }
                        }
                        else
                        {
                          if($giaXuat->getGiaXuat()==0)
                            {
                              $gx=(int)$giaNhap+(((int)$giaNhap*(int)$kenhPhanPhoi['description'])/100);
                              $giaXuat->setGiaXuat($gx);
                            }
                        }                          
                        $entityManager->flush();
                        $i++;                        
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
        }
        $this->flashMessenger()->addSuccessMessage('Import bảng giá thành công!');
       return array(
          'listMaSanPham' => $listMaSanPham,
          'import'=>1,
        );      
      }
      else
      {
        $this->flashMessenger()->addSuccessMessage('Import bảng giá không thành công! Tập tin không hợp lệ');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'bangGia'));
      }
    }
    else
    {
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'bangGia'));
    }
  }  

  public function exportHangHoaAction()
  {
    // kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();
 
    /** Error reporting */
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE); 
    ini_set('display_startup_errors', TRUE); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    // Create new PHPExcel object
    
    $objPHPExcel = new PHPExcel();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");;
    header("Content-Disposition: attachment;filename=data_hang_hoa.xls"); 
    header("Content-Transfer-Encoding: binary ");

    
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

    $post= $this->getRequest()->getPost();
    if($post['coLocSanPham'])
    {
      if($post['idSanPham'])
      {
        foreach ($post['idSanPham'] as $key => $idSanPham) {
          $index=$key+5;
          $sanPham=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($idSanPham);
          $objPHPExcel->getActiveSheet()->setCellValue('A'.$index, $sanPham->getTenSanPham())
                                        ->setCellValue('B'.$index, $sanPham->getMaSanPham())
                                        ->setCellValue('C'.$index, $sanPham->getTonKho())
                                        ->setCellValue('D'.$index, $sanPham->getIdLoai()->getTermId()->getName())
                                        ->setCellValue('E'.$index, $sanPham->getNhan());
          $objPHPExcel->getActiveSheet()->getStyle('C'.$index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        }
      }
    }
    else
    {    
      //$sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll();

      $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho);
      $sanPhams=$query->getResult();
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
    }
    // Rename worksheet    
    $objPHPExcel->getActiveSheet()->setTitle('data_hang_hoa');
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    // Save Excel 2007 file
    $callStartTime = microtime(true);

    
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);    //  (I want the output for 2003)
    $objWriter->save('php://output'); 

    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));                                  

  }

  public function exportBangGiaAction()
  {

    // kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

    
    /** Error reporting */
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE); 
    ini_set('display_startup_errors', TRUE); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    // Create new PHPExcel object
    
    $objPHPExcel = new PHPExcel();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");;
    header("Content-Disposition: attachment;filename=data_bang_gia.xls"); 
    header("Content-Transfer-Encoding: binary ");


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
    //$sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll();
    $query=$entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho);
    $sanPhams=$query->getResult();
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

    
    
     $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);    //  (I want the output for 2003)
     $objWriter->save('php://output');

    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'bangGia'));                                  

  }

  public function xoaSanPhamAction()
  {

    // kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('hang_hoa/crud');
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      $sanPham=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($id);
      if($sanPham->getKho()!=$idKho)
      {
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
      }
      if($sanPham->getHinhAnh()!='photo_default.png')
      {        
        /*$mask =__ROOT_PATH__.'/public/img/'.$sanPham->getHinhAnh();
        array_map( "unlink", glob( $mask ));*/
      }      
      if(!$sanPham)
      {
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
      }
      //$entityManager->remove($sanPham);      
      //$entityManager->flush();      
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hangHoa'));
  }
  
 }
?>
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
     if(!$sanPhams||$sanPhams->getKho()!=$this->zfcUserAuthentication()->getIdentity()->getKho())
     {
       return $this->redirect()->toRoute('hang_hoa/crud', array(
             'action' => 'hang-hoa',
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
  public function nhapHangAction(){
    return $this->nhapHang('nhap-hang');
  }

  public function doiTraHangAction(){
    return $this->nhapHang('doi-tra-hang');
  }

  public function nhapHang($loaiAction)
  {
    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $formFile= new FileForm($entityManager);
    $form= new CreateNhapHangForm($entityManager);    
    $phieuNhap= new PhieuNhap();
    $form->bind($phieuNhap);
    $request = $this->getRequest();
    if($request->isPost())
    {
      //die(var_dump($request->getPost()));
      $form->setData($request->getPost());      
      if($form->isValid())
      {
        $taxonomyLoai=$this->TaxonomyFunction();
        $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');
        $idUserNv=$this->zfcUserAuthentication()->getIdentity();
        //Cập nhật Phiếu Nhập
        $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($idUserNv);
        $phieuNhap->setIdUserNv($user);
        $entityManager->persist($phieuNhap);
        $entityManager->flush();
        $idPhieuNhap=$phieuNhap->getIdPhieuNhap();
        $maPhieuNhap=$this->createMaPhieuNhap($idPhieuNhap);
        $phieuNhap->setMaPhieuNhap($maPhieuNhap);
        $phieuNhap->setKho($idKho);
        if($loaiAction=='doi-tra-hang'){
          $phieuNhap->setStatus(1);
        }
        $entityManager->flush();       


        foreach ($phieuNhap->getCtPhieuNhaps() as $cTPhieuNhap) 
        { 
          $giaNhap=$cTPhieuNhap->getGiaNhap();
          $soLuong=$cTPhieuNhap->getSoLuong();
          //Cập nhật Sản Phẩm
          $idSanPham=$cTPhieuNhap->getIdSanPham()->getIdSanPham();  

          $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.idSanPham =\''.$idSanPham.'\''); // SUAKHO
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
                  $query = $entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.kho='.$idKho.' and gx.idSanPham ='.$idSanPham.' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);   
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
        if($loaiAction=='doi-tra-hang'){
          $this->flashMessenger()->addSuccessMessage('Trả hàng thành công, Vui lòng lập phiếu chi cho khách hàng!');
          return $this->redirect()->toRoute('cong_no/crud',array('action'=>'lap-phieu-chi-khach-hang'));   
        }
        $this->flashMessenger()->addSuccessMessage('Nhập hàng thành công!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>$loaiAction));        
      }
      else
      {
        $this->flashMessenger()->addErrorMessage('Nhập hàng thất bại!');
        if($loaiAction=='doi-tra-hang'){
          $this->flashMessenger()->addErrorMessage('Trả hàng thất bại vui lòng kiểm tra lại!');
        }
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>$loaiAction));
      }
    }    

    // lấy id phiếu thu lớn nhất +1 làm mã phiếu thu mới
    $query=$entityManager->createQuery('SELECT max(pn.idPhieuNhap) FROM HangHoa\Entity\PhieuNhap pn');
    $phieuNhap=$query->getSingleResult();
    $idPhieuNhap=(float)$phieuNhap['1']+1;
    $maPhieuNhap=$this->createMaPhieuNhap($idPhieuNhap);

    return array(       
       'form' =>$form,
       'formFile'=>$formFile,
       'maPhieuNhap'=>$maPhieuNhap,
     );
  }   

  public function createMaPhieuNhap($idPhieuNhap){
    $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh')); 
    $y=$datetime->format('Y');          
    $m=$datetime->format('m');
    $mY=$m.$y[2].$y[3];

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
    return $maPhieuNhap;
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

    $formFile=new FileForm($entityManager);
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
        $idHD=$hoaDons[0]->getIdHoaDon();
        $newMaHoaDon=$this->createMaHoaDon($idHD);// sử dụng hàm createMaHoaDon()      
        $hoaDon=$hoaDons[0];        
        $hoaDon->setMaHoaDon($newMaHoaDon);

        $entityManager->flush();
        $this->flashMessenger()->addSuccessMessage('Xuất hàng thành công!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'xuat-hang'));        
      }      
    }

    $kho=$entityManager->getRepository('Kho\Entity\Kho')->find($idKho);
    $query=$entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.hoTen= :hoTen and dt.kho= :idKho and dt.email= :email');
    $query->setParameter('hoTen','Bán lẻ');
    $query->setParameter('idKho',$idKho);
    $email='banLe_'.$idKho.'@gmail.com';
    $query->setParameter('email',$email);
    $banLe=$query->getSingleResult();
    // lấy id phiếu thu lớn nhất +1 làm mã phiếu thu mới
    $query=$entityManager->createQuery('SELECT max(hd.idHoaDon) FROM HangHoa\Entity\HoaDon hd');
    $hoaDon=$query->getSingleResult();
    $idHoaDon=(float)$hoaDon['1']+1;
    $maHoaDon=$this->createMaHoaDon($idHoaDon);

    return array(
      'form'=>$form,
      'formFile'=>$formFile,
      'maHoaDon'=>$maHoaDon,
      'banLe'=>$banLe,

    );
  }

  public function createMaHoaDon($idHD){
    $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh')); 
    $y=$datetime->format('Y');$m=$datetime->format('m');
    $mY=$m.$y[2].$y[3];
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
    return $newMaHoaDon;
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
            if(trim($post['san-pham']['maVach'])=='')
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
            if(trim($post['san-pham']['maSanPham'])=='')
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
          $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
          $queryBuilder = $repository->createQueryBuilder('sp');
          $queryBuilder->add('where','sp.kho='.$idKho.' and sp.maSanPham=\''.$sanPham->getMaSanPham().'\'');
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
              //  lấy chiết khấu
              $chietKhau=$this->getChietKhau($idKho,$kenhPhanPhoi['termTaxonomyId']);
              $gx=0;
              if($sanPhams[0]->getLoaiGia()==1)
              {

                $loiNhuan=(((float)$sanPhams[0]->getGiaBia()*(float)$chietKhau)/100);
                $gx=(float)$sanPhams[0]->getGiaBia()-(float)$loiNhuan;
              }
              else
              {
                $gx=(float)$sanPhams[0]->getGiaNhap()+(((float)$sanPhams[0]->getGiaNhap()*(float)$chietKhau)/100);  
              }
              $giaXuat->setGiaXuat(round($gx, 0));
              $giaXuat->setIdKenhPhanPhoi($kenhPhanPhoi['termTaxonomyId']);
              $giaXuat->setKho($idKho);
              
              $entityManager->persist($giaXuat);
              $entityManager->flush(); 
            }
          }
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
            'soDienThoaiKhachHang'=>$khachHang->getDiDong(),
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
    return $this->searchNhaCungCap(46);
  }  

  public function searchKhachHangDoiTraAction(){
    return $this->searchNhaCungCap(45);
  }

  public function searchNhaCungCap($loaiDoiTac)
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
        $query = $entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=:loaiDoiTac and dt.hoTen LIKE :hoTen');
        $query->setParameter('hoTen','%'.$nhaCungCap.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $query->setParameter('loaiDoiTac',$loaiDoiTac);
        $nhaCungCaps = $query->getResult(); // array of CmsArticle objects           
        foreach ($nhaCungCaps as $ncc) {
          $response[]=array(
            'idDoiTac'=>$ncc->getIdDoiTac(),
            'hoTen'=>$ncc->getHoTen(),
            'diaChi'=>$ncc->getDiaChi(),
            'diDong'=>$ncc->getDiDong(),
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

        $listMaSanPham=array(); $loi=0;
        $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh'));        
        $taxonomyLoai=$this->TaxonomyFunction();
        $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');
        $currentSheet=0;
        
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {
          $currentSheet++;          
          $worksheetTitle     = $worksheet->getTitle();
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
                  if($col==4)
                  {
                    $gia = $cell->getValue();
                  }                  
              }

              $sanPham=new SanPham();                  
              if(trim($maSanPham)!=''&&$maSanPham!=null&&trim($tenSanPham)!=''&&$tenSanPham!=null&&trim($donViTinh)!=''&&trim($gia)!=null&&trim($gia)!=''&&$donViTinh!=null&&is_numeric(trim($gia)))
              { 
                // kiểm tra đơn vị tính và loại sản phẩm
                $kiemTraDonViTinh=$this->getDonViTinh($donViTinh);
                $idLoai=$this->getLoaiBangSheetName($worksheetTitle);
                /**
                 * @var
                 * nếu chưa có đơn vị tính trong cơ sở dữ liệu
                 * hoặc nếu termtaxonomyid==52 là chưa xác định được đơn vị tính
                 * thì là lỗi
                 */
                
                if(!$kiemTraDonViTinh||$idLoai->getTermTaxonomyId()==52){
                  if(!$kiemTraDonViTinh){
                    $listMaSanPham[]=array(
                      'maSanPham'=>$maSanPham,
                      'tenSanPham'=>$tenSanPham,
                      'donViTinh'=>'<i style="color:red">Đơn vị tính không tồn tại</i>',
                      'gia'=>$gia,
                      'loaiSanPham'=>'',
                    );
                  }
                  else{
                    $listMaSanPham[]=array(
                      'maSanPham'=>$maSanPham,
                      'tenSanPham'=>$tenSanPham,
                      'donViTinh'=>$donViTinh,
                      'gia'=>$gia,
                      'loaiSanPham'=>'<i style="color:red">Loại sản phẩm không tồn tại: vui lòng liên hệ quản trị</i>',
                    );
                  }
                }
                else{


                  $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham =\''.trim($maSanPham).'\'');
                  $sanPhams = $query->getResult();
                  if(!$sanPhams)
                  {
                    //Kiểm tra loại mã vạch đã thiết lập
                    $repository = $entityManager->getRepository('Barcode\Entity\Barcode');
                    $queryBuilder = $repository->createQueryBuilder('b');
                    $queryBuilder->add('where','b.state=1');
                    $query = $queryBuilder->getQuery(); 
                    $loaiMaVachs = $query->execute();
                    foreach ($loaiMaVachs as $loaiMaVach) {
                      $loaiMV=$loaiMaVach->getTenBarcode();
                      $length=$loaiMaVach->getLength();          
                    }                

                  //Thêm mã vạch
                    
                    $mang=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
                    $a='';
                    if($loaiMV=='Code128')
                    {
                      do
                      {
                        $a='';
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
                        $a='';
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
                        $a='';
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
                        $a='';
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
                        $a='';
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

                  //Set các thông tin khác
                    $sanPham->setIdSanPham('');
                    $sanPham->setMaSanPham($maSanPham);
                    $sanPham->setTenSanPham($tenSanPham);
                    
                    //$idLoai=$this->getLoai($entityManager, $currentSheet);
                    $sanPham->setIdLoai($idLoai);

                    $dVT=$this->getDonViTinh($donViTinh);
                    $sanPham->setIdDonViTinh($dVT);
                    
                    $sanPham->setMoTa('');                  
                    $sanPham->setNhan('');                  
                    $sanPham->setTonKho('');
                    $sanPham->setGiaNhap($gia);
                    $sanPham->setLoaiGia(0);
                    $sanPham->setGiaBia('');
                    $sanPham->setChiecKhau('');                  
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
                      //1. Cập nhật Tên, Đơn vị tính của Sản phẩm
                    $sanPham->setTenSanPham($tenSanPham);
                    $query=$entityManager->createQuery('SELECT ztt FROM S3UTaxonomy\Entity\ZfTermTaxonomy ztt JOIN ztt.termId zt WHERE zt.name LIKE \'%'.trim($donViTinh).'%\'');
                    $dvt=$query->getResult();
                    if($dvt)
                    {
                      $sanPham->setIdDonViTinh($dvt[0]);
                    }
                    else
                    {
                      $dVT=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(51);
                      $sanPham->setIdDonViTinh($dVT);
                    }
                    $entityManager->flush();
                      //2. Cập nhật bảng Giá xuất
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
              }
              else
              {
                if($maSanPham==''||$maSanPham==null){
                  $maSanPham='<i style="color:red;">Mã sản phẩm: rỗng</i>';
                }
                if($tenSanPham==''||$tenSanPham==null){
                  $tenSanPham='<i style="color:red;">Tên sản phẩm: rỗng</i>';
                }
                if($donViTinh==''||$donViTinh==null){
                  $donViTinh='<i style="color:red;">Đơn vị tính: rỗng</i>';
                }
                if(!is_numeric(trim($gia))){
                  $gia='<i style="color:red;">Giá: phải là kiểu số</i>';
                }
                    $listMaSanPham[]=array(
                      'maSanPham'=>$maSanPham,
                      'tenSanPham'=>$tenSanPham,
                      'donViTinh'=>$donViTinh,
                      'gia'=>$gia,
                      'loaiSanPham'=>'',
                    );
              }
              //die(var_dump('Stop'));//Mở khi muốn test import 1 hàng trong file excel
         
          }          
        }     
        if(count($listMaSanPham)==0)
        {
          $this->flashMessenger()->addSuccessMessage('Import tập tin sản phẩm thành công.');
          return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
        }
        else
        {
          return array('lois'=>$listMaSanPham);
        }
      }
      else
      {
        $this->flashMessenger()->addErrorMessage('Import hàng hóa không thành công! Tập tin không hợp lệ');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
      }
    }
    else
    {
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
    }
  }

  public function getDonViTinh($donViTinh){
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery('SELECT ztt FROM S3UTaxonomy\Entity\ZfTermTaxonomy ztt JOIN ztt.termId zt WHERE ztt.taxonomy=\''.'don-vi-tinh'.'\' and zt.name LIKE \'%'.trim($donViTinh).'%\'');
    $dvt=$query->getResult();
    if($dvt)
    {
      return $dvt[0];
    }
    else
    {
      return false;
    }
  }

  public function getLoaiBangSheetName($sheetName){
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery('SELECT zft FROM S3UTaxonomy\Entity\ZfTermTaxonomy zft JOIN zft.termId t WHERE t.name LIKE :termName and zft.taxonomy= :danhMucHangHoa');
    $query->setParameter('termName','%'.$sheetName.'%');
    $query->setParameter('danhMucHangHoa','danh-muc-hang-hoa');
    $loaiHang=$query->getResult();
    if($loaiHang){
      $loaiHang=$loaiHang[0];
    } 
    else{
      // loại này không tồn tại
      $loaiHang=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(52);
    }
    return $loaiHang;
  }

  public function getLoai($entityManager, $currentSheet){

    if($currentSheet==1)
    {
      $idLoai=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(9);
    }
    if($currentSheet==2)
    {
      $idLoai=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(14);
    }
    if($currentSheet==3)
    {
      $idLoai=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(19);
    }
    if($currentSheet==4)
    {
      $idLoai=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(24);
    }
    if($currentSheet==5)
    {
      $idLoai=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(37);
    }
    if($currentSheet>5)
    {
      // loại này không tồn tại
      $idLoai=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(52);
    }
    return $idLoai;
  }

  public function xuatHangBangFileAction(){
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();
    $formFile=new FileForm($entityManager);
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
        // kiểm tra lỗi
        $danhSachLoi=array(); 
        $tenKhachHang=''; 
        $loi=0;      
        $idUserNv=$this->zfcUserAuthentication()->getIdentity();        
        $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($idUserNv);
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {
          $worksheetTitle     = $worksheet->getTitle();
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
              if($col==4)
              {
                $soLuong = $cell->getValue();
              }
              if($col==5)
              {
                $gia = $cell->getValue();
              }
              if($col==6)
              {
                $khachHang = $cell->getValue();
              }
            }
            if($row==2){
              $tenKhachHang=$khachHang;
              $query = $entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=45 and dt.hoTen= :ten');
              $query->setParameter('ten',trim($tenKhachHang));
              $doiTacs = $query->getResult();              
              if(count($doiTacs)>1){
                if(!$post['idNhaCungCap']||$post['fileName']!=$post['file']['name']){
                  return array(
                    'doiTacs'=>$doiTacs,
                    'danhSachLoi'=>$danhSachLoi,
                    'formFile'=>$formFile,
                    'fileName'=>$post['file']['name'],
                  );
                }
                else{
                  $doiTacs=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($post['idNhaCungCap']);
                }                  
              }   
              else{
                if($doiTacs){
                  $doiTacs=$doiTacs[0];
                }
                else{
                  $loi++;
                  $danhSachLoi[]=array(
                    'maSanPham'=>$maSanPham,
                    'khachHang'=>'<i style="color:red;">Lỗi: không tìm thấy khách hàng</i>',
                    'soLuong'=>$soLuong,
                    'gia'=>$gia,
                  );
                }
                
              }           
            }
            $loiKep=0;$soLoi=$loi;$tonKho=0;
            if($maSanPham){
              $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham= :maSanPham');
              $query->setParameter('maSanPham',trim($maSanPham));
              $sanPhams = $query->getResult();   
              
              if(!$sanPhams){
                $loiKep++;
                $loi++;
                $maSanPham='<i style="color:red;">Lỗi: Không tìm thấy mã sản phẩm này trong csdl</i>';
              }
              else{
                $tonKho=$sanPhams[0]->getTonKho();
              }
            }
            if($maSanPham==''||$maSanPham==null||!is_numeric(trim($soLuong))||!is_numeric(trim($gia))||$khachHang==''||$khachHang==null||$khachHang!=$tenKhachHang||(float)$tonKho<(float)$soLuong){
              if($loiKep==0){
                $loi++;
              }              
              if($maSanPham==''||$maSanPham==null){
                $maSanPham='<i style="color:red;">Lỗi: Rỗng</i>';
              }
              if($khachHang==''||$khachHang==null){
                $khachHang='<i style="color:red;">Lỗi: Rỗng</i>';
              }
              if($khachHang){
                if($khachHang!=$tenKhachHang){
                  $khachHang='<i style="color:red;">Lỗi: "Có quá nhiều tên nhà cung cấp"</i>';
                }
              }
              if(!is_numeric(trim($soLuong))){
                $soLuong='<i style="color:red;">Lỗi: số lượng phải là số</i>';
              }
              elseif((float)$tonKho<(float)$soLuong){
                $soLuong='<i style="color:red;">Lỗi: số lượng tồn trong kho không đủ đáp ứng</i>';
              }
              if(!is_numeric(trim($gia))){
                $gia='<i style="color:red;">Lỗi: giá phải là số</i>';
              }              
            }
            if($loi>$soLoi){
              $danhSachLoi[]=array(
                'maSanPham'=>$maSanPham,
                'khachHang'=>$khachHang,
                'soLuong'=>$soLuong,
                'gia'=>$gia,
              );
            }            
          } 
        }
        if($loi>0){
          return array(
            'doiTacs'=>null,
            'danhSachLoi'=>$danhSachLoi,
            'formFile'=>$formFile,
          );
        }


        //bước 2: file chuẩn
        // tiến hành xuất kho theo file
        $idKenhPhanPhoi=$doiTacs->getIdKenhPhanPhoi()->getTermTaxonomyId();
        $co=0;
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {
          $worksheetTitle     = $worksheet->getTitle();
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
              if($col==4)
              {
                $soLuong = $cell->getValue();
              }
              if($col==5)
              {
                $gia = $cell->getValue();
              }
              if($col==6)
              {
                $khachHang = $cell->getValue();
              }
            }
            $co++;
            if($co==1){
              $hoaDon= new HoaDon();
              $hoaDon->setMaHoaDon('');
              $hoaDon->setNgayXuat(Date('Y-m-d'));
              $hoaDon->setStatus(0);

              $hoaDon->setIdDoiTac($doiTacs);
              $hoaDon->setKho($idKho);
              $hoaDon->setIdUserNv($user);
              $entityManager->persist($hoaDon);
              $entityManager->flush();

              $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh')); 
              $y=$datetime->format('Y');          
              $m=$datetime->format('m');
              $mY=$m.$y[2].$y[3];

              $idHoaDon=$hoaDon->getIdHoaDon();
              $maHoaDon=$this->createMaHoaDon($idHoaDon);
              $hoaDon->setMaHoaDon($maHoaDon);        
              $entityManager->flush();
            }
            // thêm vào chi tiết hóa đơn
            $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham= :maSanPham');
            $query->setParameter('maSanPham',trim($maSanPham));
            $sanPhams = $query->getResult(); 
            if($sanPhams){
              $ctHoaDon=new CTHoaDon();
              $ctHoaDon->setIdCTHoaDon('');
              $ctHoaDon->setIdSanPham($sanPhams[0]);
              $ctHoaDon->setIdHoaDon($hoaDon);
              $ctHoaDon->setGiaNhap($sanPhams[0]->getGiaNhap());
              // nếu không nhập giá thì giá bán sẽ bằng giá bán mặt định, nếu nhập giá thì lấy giá đã nhập
              if($gia){
                $ctHoaDon->setGia($gia); 
              }
              else{
                $query = $entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.kho='.$idKho.' and gx.idSanPham= :idSanPham and gx.idKenhPhanPhoi= :idKenhPhanPhoi');
                $query->setParameter('idSanPham',trim($idSanPham));
                $query->setParameter('idKenhPhanPhoi',trim($idKenhPhanPhoi));
                $giaXuat = $query->getResult();
                $ctHoaDon->setGia($giaXuat);
              }     
              if($sanPhams[0]->getTonKho()>=$soLuong){
                $ctHoaDon->setSoLuong($soLuong);
                $tonKho=$sanPhams[0]->getTonKho()-$soLuong;
                $sanPhams[0]->setTonKho($tonKho);
              }
              else{
                $ctHoaDon->setSoLuong(0);
              }

              

              $entityManager->persist($ctHoaDon);
              $entityManager->flush();
            }
            // trừ tồn kho của sản phẩm
          }
        }
        $this->flashMessenger()->addSuccessMessage('Xuất hàng thành công');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'in-phieu-xuat-bang-file','id'=>$idHoaDon));
      }
      else //nếu tập tin không hợp lệ
      {
        $this->flashMessenger()->addErrorMessage('Tập tin không hợp lệ');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'xuat-hang'));
      }
    }
    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'xuat-hang'));
  }

  public function inPhieuXuatBangFileAction(){
    // kiểm tra đăng nhập
    // id đối tác
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) {
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'xuat-hang'));
    } 
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $hoaDon=$entityManager->getRepository('HangHoa\Entity\HoaDon')->find($id);
    if($hoaDon){
      if($hoaDon->getKho()!=$idKho){
        $this->flashMessenger()->addErrorMessage('Xin lỗi bạn không có quyền truy cập, vui lòng kiểm tra lại');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'xuat-hang'));
      }
    }
    $this->flashMessenger()->addSuccessMessage('Import tập tin nhập hàng thành công.');
    return array(
      'hoaDon'=>$hoaDon,
    );
  }

  public function nhapHangBangFileAction()
  {    
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

        $danhSachLoi=array();        
        $idUserNv=$this->zfcUserAuthentication()->getIdentity();        
        $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($idUserNv);
        
        // kiểm tra file hợp lệ
        $loi=0;
        $tenNhaCungCap='';
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {
          $worksheetTitle     = $worksheet->getTitle();
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
              if($col==4)
              {
                $soLuong = $cell->getValue();
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
            // lấy dòng đầu tiên làm tên chuẩn
            if($row==2){
              $tenNhaCungCap=$nhaCungCap;
            }

            // nếu masanpham hoặc tên nhà cung cấp hoặc số lượng khác kiểu number hoặc giá khác kiểu number  
            if($maSanPham==''||$maSanPham==null||$nhaCungCap==''||$nhaCungCap==null||!is_numeric(trim($soLuong))||!is_numeric(trim($gia))||$nhaCungCap!=$tenNhaCungCap){
              $loi++;
              if($maSanPham==''||$maSanPham==null){
                $maSanPham='<i style="color:red;">Lỗi: Rỗng</i>';
              }
              if($nhaCungCap==''||$nhaCungCap==null){
                $nhaCungCap='<i style="color:red;">Lỗi: Rỗng</i>';
              }
              if($nhaCungCap){
                if($nhaCungCap!=$tenNhaCungCap){
                  $nhaCungCap='<i style="color:red;">Lỗi: "Có quá nhiều tên nhà cung cấp"</i>';
                }
              }
              if(!is_numeric(trim($soLuong))){
                $soLuong='<i style="color:red;">Lỗi: số lượng không phải kiểu số</i>';
              }
              if(!is_numeric(trim($gia))){
                $gia='<i style="color:red;">Lỗi: giá không phải kiểu số</i>';
              }
              $danhSachLoi[]=array(
                'maSanPham'=>$maSanPham,
                'nhaCungCap'=>$nhaCungCap,
                'soLuong'=>$soLuong,
                'gia'=>$gia,
              );
            }
            elseif ($maSanPham) {
              //Kiểm tra mã sản phẩm
              $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham =\''.trim($maSanPham).'\'');
              $sanPhams = $query->getResult(); 
              if(!$sanPhams){
                $loi++;
                $maSanPham='<i style="color:red;">Lỗi: sản phẩm không tồn tại</i>';
              }
              $danhSachLoi[]=array(
                'maSanPham'=>$maSanPham,
                'nhaCungCap'=>$nhaCungCap,
                'soLuong'=>$soLuong,
                'gia'=>$gia,
              );
            }
          }
        }
        //Kiểm tra nhà cung cấp
        $query = $entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.'  and dt.loaiDoiTac=46 and dt.hoTen= :ten');
        $query->setParameter('ten',trim($tenNhaCungCap));
        $doiTacs = $query->getResult();
        if(!$doiTacs){ //nếu chưa có đôi tác thì thêm mới đối tác này luôn
          $loi++;
          $loiKHongTimDuocNhaCungCap='<i style="color:red;">Lỗi: Không tìm thấy tên nhà cung cấp trên</i>';
          $danhSachLoi[]=array(
                'maSanPham'=>$loiKHongTimDuocNhaCungCap,
                'nhaCungCap'=>$loiKHongTimDuocNhaCungCap,
                'soLuong'=>$loiKHongTimDuocNhaCungCap,
                'gia'=>$loiKHongTimDuocNhaCungCap,
              );

        }
        if($loi>0){
          return array(
            'danhSachLoi'=>$danhSachLoi,
          );
        }// kết thúc phần kiểm tra lỗi
        // nếu không có lỗi
        $doiTacs=$doiTacs[0];
        $co=0;// nếu cờ này ==1 tức là đã tạo phiếu nhập xong thì sử dụng lại phiếu nhập này để nhập hàng, không nên tạo phiếu nhập khác
         $currentSheet=0;
        foreach ($objLoad->getWorksheetIterator() as $worksheet) {
          $currentSheet++;  
          $worksheetTitle     = $worksheet->getTitle();
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
              if($col==4)
              {
                $soLuong = $cell->getValue();
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
            if(trim($maSanPham)!=''&&$maSanPham!=null&&trim($nhaCungCap)!=''&&$nhaCungCap!=null&&is_numeric(trim($soLuong))&&is_numeric(trim($gia)))
            {
              //Kiểm tra mã sản phẩm
              $query = $entityManager->createQuery('SELECT sp FROM HangHoa\Entity\SanPham sp WHERE sp.kho='.$idKho.' and sp.maSanPham =\''.trim($maSanPham).'\'');
              $sanPhams = $query->getResult();              
              /*if(!$sanPhams){ //nếu sản phẩm này chưa có trong csdl thì mình thêm vào luôn
                $sanPhams=$this->createSanPham($entityManager,$maSanPham,$tenSanPham,$donViTinh,$currentSheet,$gia);
              }*/
              if($sanPhams)
              {
                $co++;
                if($co==1){
                  //Cập nhật phiếu nhập
                  $phieuNhap= new PhieuNhap();
                  $phieuNhap->setIdPhieuNhap('');
                  $phieuNhap->setMaPhieuNhap('');
                  $phieuNhap->setNgayNhap(Date('Y-m-d'));
                  $phieuNhap->setStatus(0);

                  $phieuNhap->setIdDoiTac($doiTacs);
                  $phieuNhap->setKho($idKho);
                  $phieuNhap->setIdUserNv($user);
                  $entityManager->persist($phieuNhap);
                  $entityManager->flush();

                  $idPhieuNhap=$phieuNhap->getIdPhieuNhap();
                  $maPhieuNhap=$this->createMaPhieuNhap($idPhieuNhap);// hàm createMaPhieuNhap()
                  $phieuNhap->setMaPhieuNhap($maPhieuNhap);        
                  $entityManager->flush();
                }
                //Cập nhật sản phẩm
                $tonKho=(int)($sanPhams[0]->getTonKho());
                $tonKho=$tonKho+(int)($soLuong);
                $sanPhams[0]->setTonKho($tonKho);
                $sanPhams[0]->setGiaNhap($gia);
                $entityManager->flush();
                $cTPhieuNhap=new cTPhieuNhap();
                $cTPhieuNhap->setIdCTPhieuNhap('');
                $cTPhieuNhap->setIdPhieuNhap($phieuNhap);
                $cTPhieuNhap->setIdSanPham($sanPhams[0]);
                $cTPhieuNhap->setGiaNhap($gia);
                $cTPhieuNhap->setSoLuong($soLuong);
                $entityManager->persist($cTPhieuNhap);
                $entityManager->flush();

                //Cập nhật giá xuất
                $taxonomyLoai=$this->TaxonomyFunction();
                $kenhPhanPhois=$taxonomyLoai->getListChildTaxonomy('kenh-phan-phoi');
                $idSanPham=$sanPhams[0]->getIdSanPham();
                $loaiGia=$sanPhams[0]->getLoaiGia();
                $giaBia=$sanPhams[0]->getGiaBia();
                $chiecKhau=$sanPhams[0]->getChiecKhau();

                foreach ($kenhPhanPhois as $kenhPhanPhoi) {
                  if($kenhPhanPhoi['cap']>0)
                  {                    
                    $query = $entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.kho='.$idKho.' and gx.idSanPham ='.$idSanPham.' and gx.idKenhPhanPhoi='.$kenhPhanPhoi['termTaxonomyId']);   
                    $giaXuats = $query->getResult();                    
                    $chietKhau=$this->getChietKhau($idKho,$kenhPhanPhoi['termTaxonomyId']);

                    foreach ($giaXuats as $giaXuat) { 
                      if((float)$loaiGia==1)
                      {
                        $loiNhuan=(float)(((float)$giaBia*(float)$chietKhau)/100);
                        $gx=(float)$giaBia-(float)$loiNhuan;
                      } 
                      else
                      {
                        $gx=(float)$gia+(((float)$gia*(float)$chietKhau)/100);
                      }              
                      $giaXuat->setGiaXuat(round($gx, 0));
                      $entityManager->flush();
                    }
                  }        
                }
              }
            }                      
          }
        }
        $this->flashMessenger()->addSuccessMessage('Import tập tin nhập hàng thành công.');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'in-phieu-nhap-bang-file','id'=>$phieuNhap->getIdPhieuNhap()));
      }
      else //nếu tập tin không hợp lệ
      {
        $this->flashMessenger()->addErrorMessage('Tập tin không hợp lệ');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'nhap-hang'));
      }
    } // nếu isPost
    // không isPost thì return về nhập hàng
    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'nhap-hang'));
      
  }  

  public function inPhieuNhapBangFileAction(){
    // kiểm tra đăng nhập
    // id đối tác
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) {
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'nhap-hang'));
    } 
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $phieuNhap=$entityManager->getRepository('HangHoa\Entity\PhieuNhap')->find($id);
    if($phieuNhap){
      if($phieuNhap->getKho()!=$idKho){
        $this->flashMessenger()->addErrorMessage('Xin lỗi bạn không có quyền truy cập, vui lòng kiểm tra lại');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'nhap-hang'));
      }
    }
    $this->flashMessenger()->addSuccessMessage('Import tập tin nhập hàng thành công.');
    return array(
      'phieuNhap'=>$phieuNhap,
    );
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
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'bang-gia'));
      }
    }
    else
    {
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'bang-gia'));
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
    /*header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");*/
    header("Content-Type: application/vnd.ms-excel");    
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
    foreach(range('A','E') as $columnID) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }
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

    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));                                  

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
      $fileName='bang_gia';
      $objPHPExcel = new PHPExcel();
      $PI_ExportExcel=$this->ExportExcel();
      $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->dataExportBangGia($entityManager, $objPHPExcel));

      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'bang-gia'));                                  

  }
  public function dataExportBangGia($entityManager, $objPHPExcel){
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
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
    //$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);    

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
        $this->flashMessenger()->addErrorMessage('Xin lỗi, hệ thống không tìm thấy sản phẩm cần xóa!');
        return $this->redirect()->toRoute('hang_hoa/crud');
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();
      $sanPham=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($id);
      if($sanPham->getKho()!=$idKho)
      {
        $this->flashMessenger()->addErrorMessage('Xin lỗi, hệ thống không tìm thấy sản phẩm cần xóa!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
      }

      $query=$entityManager->createQuery('SELECT ctpn FROM HangHoa\Entity\CTPhieuNhap ctpn WHERE ctpn.idSanPham=:idSanPham');
      $query->setParameter('idSanPham',$id);
      $phieuNhaps=$query->getResult();
      if(!$phieuNhaps){ // nếu chưa tồn tại thì xóa
        // nếu có hình ảnh sản phẩm thì xóa hình ảnh
        if($sanPham->getHinhAnh()!='photo_default.png'&&$sanPham->getHinhAnh()!=''&&$sanPham->getHinhAnh()!=null)
        {        
          $mask =__ROOT_PATH__.'/public/img/'.$sanPham->getHinhAnh();
          array_map( "unlink", glob( $mask ));
        }   
        // xóa giá xuất của sản phẩm
        $query=$entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.idSanPham=:idSanPham and gx.kho=:idKho');
        $query->setParameter('idSanPham',$id);
        $query->setParameter('idKho',$idKho);
        $giaXuats=$query->getResult();
        foreach ($giaXuats as $giaXuat) {
          $entityManager->remove($giaXuat);
          $entityManager->flush();
        }
        // xóa sản phẩm
        $entityManager->remove($sanPham);  
        $entityManager->flush();
        $this->flashMessenger()->addSuccessMessage('Sản phảm đã được xóa thành công!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
      }
      else{ //ngược lại đã tồn tại không được xóa
        $this->flashMessenger()->addErrorMessage('Không thể xóa sản phẩm này vì sản phẩm '.$sanPham->getTenSanPham().' đã được sử dụng!');
        return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
      }  
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
  }
  
 }
?>
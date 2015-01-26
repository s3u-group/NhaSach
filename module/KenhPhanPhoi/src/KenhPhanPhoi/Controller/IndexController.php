<?php namespace KenhPhanPhoi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Zend\ServiceManager\ServiceManager;
use HangHoa\Entity\DoiTac;
use KenhPhanPhoi\Form\ThemKhachHangForm;
use KenhPhanPhoi\Form\KhachHangFieldset;
use HangHoa\Form\FileForm;
use HangHoa\Entity\CTHoaDon;

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
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $form= new FileForm($entityManager);

    $query=$entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=45');
    $doiTacs=$query->getResult();

    $taxonomyFunction=$this->TaxonomyFunction();
    $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');
    return array(
      'kenhPhanPhois'=>$kenhPhanPhois,
      'doiTacs'=>$doiTacs,
      'form'=>$form,
    );

 	}

  // đã sửa tên biến
  public function nhaCungCapAction()
  {

    // kiểm tra đăng nhập
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();


    $query=$entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho);
    $doiTacs=$query->getResult();


    return array(
      'doiTacs'=>$doiTacs,
    );

  }

  public function chiTietDonHangAction()
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
          return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

      $hoaDon=$entityManager->getRepository('HangHoa\Entity\HoaDon')->find($id);
      // kiểm tra nếu hóa đơn đó thuộc kho mà user này quản lý thì mới cho xem chi tiết hóa đơn
      if($hoaDon->getKho()!=$idKho)
      {
        return $this->redirect()->toRoute('loi_nhuan/crud',array('action'=>'don-hang'));
      }
        
        return array(
          'hoaDon'=>$hoaDon,
        );
  }

  // đã sửa tên biến
  public function chiTietPhieuNhapAction()
  {// kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'nha-cung-cap'));
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();
  
      
      $phieuNhap=$entityManager->getRepository('HangHoa\Entity\PhieuNhap')->find($id);
      if($phieuNhap->getKho()!=$idKho)
      {
        return $this->redirect()->toRoute('loi_nhuan/crud',array('action'=>'phieu-nhap'));
      }
      return array(
        'phieuNhap'=>$phieuNhap,
      );
  }

 	public function themKhachHangAction()
 	{

    if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho(); 
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

          $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh')); 
     
          $doiTac->setNgayDangKy($datetime);
          $query = $entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.loaiDoiTac=45 and kh.kho='.$idKho.' and kh.diDong=\''.$doiTac->getDiDong().'\'');
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
            $doiTac->setKho($idKho);
            $entityManager->persist($doiTac);
            $entityManager->flush();
            
            $this->flashMessenger()->addSuccessMessage('Thêm khách hàng thành công!');
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

    if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho(); 
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
          $datetime = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh')); 
     
          $query = $entityManager->createQuery('SELECT ncc FROM HangHoa\Entity\DoiTac ncc WHERE ncc.loaiDoiTac=46 and ncc.kho='.$idKho.' and ncc.hoTen=\''.$doiTac->getHoTen().'\'');
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
            
            $doiTac->setNgayDangKy($datetime);
            $doiTac->setKho($idKho);
            $entityManager->persist($doiTac);
            $entityManager->flush();
            $this->flashMessenger()->addSuccessMessage('Thêm nhà cung cấp thành công!');
            return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'nha-cung-cap'));   
          }          
        }
        else
        {
          //die(var_dump($form->getMessages()));
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

      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      $query=$entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.kho='.$idKho.' and kh.idDoiTac='.$id.' and kh.loaiDoiTac=45');
      $khachHangs=$query->getResult();
      // nếu id khách hàng này không tồn tại
      if(!$khachHangs)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }
      $taxonomyFunction=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

      //var_dump('Hóa đơn---------------------');
      $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.idDoiTac='.$id.' ORDER BY hd.ngayXuat DESC');
      $hoaDons=$query->getResult();     
      //var_dump($hoaDons);

      //var_dump('Phiếu thu---------------------');
      $query=$entityManager->createQuery('SELECT pt FROM CongNo\Entity\PhieuThu pt, CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt  WHERE pt.kho='.$idKho.' and dt.kho='.$idKho.' and pt.idCongNo=cn.idCongNo and cn.idDoiTac=dt.idDoiTac and dt.idDoiTac='.$id.' ORDER BY pt.ngayThanhToan DESC');
      $phieuThus=$query->getResult();
      //var_dump($phieuThus);
      //die(var_dump('stop'));


      /*$query=$entityManager->createQuery('SELECT hd, pt FROM HangHoa\Entity\HoaDon hd, CongNo\Entity\PhieuThu pt, CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt  WHERE pt.idCongNo=cn.idCongNo and cn.idDoiTac=dt.idDoiTac and hd.idDoiTac=dt.idDoiTac and dt.idDoiTac='.$id.' ORDER BY hd.ngayXuat DESC, pt.ngayThanhToan DESC');
      $lichSuGiaoDichs=$query->getResult();
      die(var_dump($lichSuGiaoDichs));*/

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
          $query=$entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.kho='.$idKho.' and kh.loaiDoiTac=45 and kh.email=\''.$khachHang->getEmail().'\'');
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
            $this->flashMessenger()->addSuccessMessage('Cập nhật thông tin khách hàng thành công!');
            return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'chi-tiet-khach-hang','id'=>$id));
          }        
          else
          {
            $this->flashMessenger()->addErrorMessage('Cập nhật thông tin khách hàng thất bại!');
            return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'chi-tiet-khach-hang','id'=>$id));
            
          }
        } 
      }

      $kho=$entityManager->getRepository('Kho\Entity\Kho')->find($idKho);

      return array(
        'form'=>$form,
        'khachHang'=>$khachHang,
        'kenhPhanPhois'=>$kenhPhanPhois,
        //'lichSuGiaoDichs'=>$lichSuGiaoDichs,
        'hoaDons'=>$hoaDons,
        'phieuThus'=>$phieuThus,
        'coKiemTraTrung'=>0,
        'kho'=>$kho,
      );
  }

  // đã sửa tên biến
  public function chiTietNhaCungCapAction()
  {
     if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'nha-cung-cap'));
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      $query=$entityManager->createQuery('SELECT ncc FROM HangHoa\Entity\DoiTac ncc WHERE ncc.kho='.$idKho.' and ncc.idDoiTac='.$id.' and ncc.loaiDoiTac=46');
      $khachHangs=$query->getResult();
      // nếu id khách hàng này không tồn tại
      if(!$khachHangs)
      {
        return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nha-cung-cap'));
      }
      $taxonomyFunction=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug



      //var_dump('Phiếu chi---------------------');
      $query=$entityManager->createQuery('SELECT pc FROM CongNo\Entity\PhieuChi pc, CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt  WHERE pc.kho='.$idKho.' and dt.kho='.$idKho.' and pc.idCongNo=cn.idCongNo and cn.idDoiTac=dt.idDoiTac and dt.idDoiTac='.$id.' ORDER BY pc.ngayThanhToan DESC');
      $phieuChis=$query->getResult();
      //var_dump($phieuThus);
      //die(var_dump($phieuChis));

      $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.idDoiTac='.$id);
      $phieuNhaps=$query->getResult();

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
          $query=$entityManager->createQuery('SELECT ncc FROM HangHoa\Entity\DoiTac ncc WHERE ncc.kho='.$idKho.' and ncc.loaiDoiTac=46 and ncc.hoTen=\''.$nhaCungCap->getHoTen().'\'');
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
            $this->flashMessenger()->addSuccessMessage('Cập nhật thông tin khách hàng thành công!');
            return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'chi-tiet-nha-cung-cap','id'=>$id));
          }  
          else
          {            
            $this->flashMessenger()->addErrorMessage('Cập nhật thông tin khách hàng thất bại!');
            return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'chi-tiet-nha-cung-cap','id'=>$id));
          }
        }       
      }
      
      return array(
        'form'=>$form,
        'nhaCungCap'=>$nhaCungCap,
        'kenhPhanPhois'=>$kenhPhanPhois,
        //'lichSuGiaoDichs'=>$lichSuGiaoDichs,
        'phieuChis'=>$phieuChis,
        'phieuNhaps'=>$phieuNhaps,
        'coKiemTraTrung'=>0,
      );
  }

  public function xoaKhachHangAction()
  {

    if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

      $doiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if(!$doiTac)
      {
        $this->flashMessenger()->addErrorMessage('Xóa khách hàng thất bại! Không tìm thấy khách hàng cần xóa.');
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }

      if($doiTac->getKho()!=$idKho)
      {
        $this->flashMessenger()->addErrorMessage('Xóa khách hàng thất bại! Không tìm thấy khách hàng cần xóa.');
        return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }

      $loaiDoiTac=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(0);
      $doiTac->setLoaiDoiTac($loaiDoiTac);
      $entityManager->flush();

      $this->flashMessenger()->addSuccessMessage('Xóa khách hàng thành công!');
      return $this->redirect()->toRoute('kenh_phan_phoi/crud');

  }

  // sửa tên biến
  public function xoaNhaCungCapAction()
  {

    if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
      $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'nha-cung-cap'));
      }  
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      $doiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if(!$doiTac)
      {
        $this->flashMessenger()->addErrorMessage('Xóa nhà cung cấp thất bại! Không tìm thấy nhà cung cấp cần xóa.');
        return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nha-cung-cap'));
      }
      if($doiTac->getKho()!=$idKho)
      {
        $this->flashMessenger()->addErrorMessage('Xóa nhà cung cấp thất bại! Không tìm thấy nhà cung cấp cần xóa.');
        return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nha-cung-cap'));
      }

      $loaiDoiTac=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(0);
      $doiTac->setLoaiDoiTac($loaiDoiTac);
      $entityManager->flush();

      $this->flashMessenger()->addSuccessMessage('Xóa nhà cung cấp thành công!');
      return $this->redirect()->toRoute('kenh_phan_phoi/crud', array('action'=>'nha-cung-cap'));

  }

  public function importKhachHangAction()
  {
   if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();


    $khachHang= new DoiTac();
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
        /*code*/
      //Test-------------        
        $this->flashMessenger()->addSuccessMessage('Import khách hàng thành công! Test, Chưa hoàn thiện');
        return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'index'));
      //Test-------------
      }
      else
      {
        $this->flashMessenger()->addErrorMessage('Tập tin không hợp lệ');
        return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'index'));
      }      
    }
    else
    {
      return $this->redirect()->toRoute('kenh_phan_phoi/crud',array('action'=>'index'));
    }
  }

  public function exportKhachHangAction()
  {

    // kiểm tra đăng nhập
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }


    $entityManager=$this->getEntityManager();
    // tham số thức nhất cho hàm exportExcel
    $objPHPExcel = new PHPExcel();
    // tham số thức 2 cho hàm exportExcel
    $fileName='danh_sach_khach_hang';
    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)

    $loaiDoiTac=45;
    $tieuDe='DANH SÁCH KHÁCH HÀNG';
    $fieldName=array(0=>'Tên khách hàng',1=>'Địa chỉ',2=>'Số điện thoại',3=>'Email');

    $PI_ExportExcel=$this->ExportExcel();
    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->data($objPHPExcel, $loaiDoiTac, $tieuDe, $fieldName));

    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'index'));   
  }

  public function exportNhaCungCapAction()
  {

    // kiểm tra đăng nhập
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
    

    $entityManager=$this->getEntityManager();
    // tham số thức nhất cho hàm exportExcel
    $objPHPExcel = new PHPExcel();
    // tham số thức 2 cho hàm exportExcel
    $fileName='danh_sach_nha_cung_cap';
    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)

    $loaiDoiTac=46;
    $tieuDe='DANH SÁCH NHÀ CUNG CẤP';
    $fieldName=array(0=>'Tên nhà cung cấp',1=>'Địa chỉ',2=>'Số điện thoại',3=>'Email');

    $PI_ExportExcel=$this->ExportExcel();
    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->data($objPHPExcel, $loaiDoiTac, $tieuDe, $fieldName));

    return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'nha-cung-cap'));   
  }

  // fieldName this is array, it have 5 column
  public function data($objPHPExcel, $loaiDoiTac, $tieuDe, $fieldName)
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();

    $query=$entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac='.$loaiDoiTac);
    $doiTacs=$query->getResult();

    $objPHPExcel->getActiveSheet()->setCellValue('A2', $tieuDe);
    $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
    $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                  
   

    $objPHPExcel->getActiveSheet()->setCellValue('A4', $fieldName[0])
                                  ->setCellValue('B4', $fieldName[1])
                                  ->setCellValue('C4', $fieldName[2])
                                  ->setCellValue('D4', $fieldName[3])
                                  ->getStyle('A4:D4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


    
    foreach ($doiTacs as $index => $doiTac) {
      $dong=$index+5;

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$dong, $doiTac->getHoTen());
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$dong, $doiTac->getDiaChi());
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$dong, $doiTac->getDiDong());
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$dong, $doiTac->getEmail());

    }

  }
}
?>
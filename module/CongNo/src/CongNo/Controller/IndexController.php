<?php 
namespace CongNo\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 use CongNo\Form\ThanhToanForm;
 use CongNo\Form\PhieuChiForm;
 use CongNo\Entity\PhieuThu;
 use CongNo\Form\ThanhToanNhaCungCapForm;
 use CongNo\Form\PhieuChiFieldset;
 use CongNo\Entity\PhieuChi;


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

    // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }
     
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();
      

      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      
      /*$query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=45');
      $doiTacs=$query->getResult();*/
      $query=$entityManager->createQuery('SELECT distinct dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=45');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      foreach ($doiTacs as $doiTac) 
      {   
        //$idDoiTac=$doiTac['idDoiTac'];  
        $idDoiTac=$doiTac->getIdDoiTac();
        if($idDoiTac)
        {
          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          
          $entityManager=$this->getEntityManager();

          $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.kho='.$idKho.' and kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and pt.kho='.$idKho.' and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');        

          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $congNos = $query->getResult(); // array of CmsArticle objects 

          // nếu đã có công nợ trước với hệ thống
          if($congNos)
          {
            $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
            $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();
            
          }
          else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
          {
            // nợ đầu kỳ
            $noDauKi=0;
            $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

            // kiểm tra đối tác này có từng mua hàng ngày nào chưa
            $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.idDoiTac='.$idDoiTac.' and hd.status=0 ORDER BY hd.idHoaDon');
            $hoaDonDauTienCuaDoiTac=$query->getResult();
            if($hoaDonDauTienCuaDoiTac)
            {
                // lấy ngày mua hàng đầu tiên
                $ngayDauKi=$hoaDonDauTienCuaDoiTac[0]->getNgayXuat()->format('Y-m-d');
            }
            else
            {
                // lấy ngày đăng ký làm ngày đầu kỳ
                $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
            }
          }

          // lấy nợ phát sinh hoaDon
          $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.status=0 and hd.idDoiTac= :idDoiTac');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $hoaDons=$query->getResult();

          
          $noPhatSinh=0;
          foreach ($hoaDons as $hoaDon) {
            foreach ($hoaDon->getCtHoaDons() as $ctHoaDon) {
              $noPhatSinh+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
            }
          }

          // tính nợ cuối kỳ
          $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
          $response[]=array(
            'idKenhPhanPhoi'=>$thongTinDoiTac->getIdKenhPhanPhoi()->getTermTaxonomyId(),
            'idDoiTac'=>$idDoiTac,
            'hoTenDoiTac'=>$thongTinDoiTac->getHoTen(),
            'ngayDauKi'=>$ngayDauKi,
            'noDauKi'=>$noDauKi,
            'noPhatSinh'=>$noPhatSinh,
            'noCuoiKi'=>$noCuoiKi,
          );
        }
      }

      $taxonomyKenhPhanPhoi=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug
      
      return array('response'=>$response, 'kenhPhanPhois'=>$kenhPhanPhois);
  }

  public function thanhToanAction()
  {

    // kiểm tra đăng nhập
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }

     
     
    // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }

      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();     
      $form= new ThanhToanForm($entityManager);
      $phieuThu= new PhieuThu();
      $form->bind($phieuThu);

      $request=$this->getRequest();      
      if($request->isPost())
      {        
        $form->setData($request->getPost());
        if($form->isValid())
        {
          $idUserNv=$this->zfcUserAuthentication()->getIdentity();
          $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($idUserNv);
          $phieuThu->setIdUserNv($user);
          $idDoiTac=$phieuThu->getIdCongNo()->getIdDoiTac()->getIdDoiTac();

          $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.status=0 and hd.idDoiTac='.$idDoiTac);
          $hoaDons=$query->getResult();

          foreach ($hoaDons as $hoaDon) {
            $hoaDon->setStatus(1);
            $entityManager->flush();
          }

          $phieuThu->setKho($idKho);
          $entityManager->persist($phieuThu);
          $entityManager->flush();
          $this->flashMessenger()->addSuccessMessage('Thanh toán thành công!');
          return $this->redirect()->toRoute('cong_no/crud',array('action'=>'chiTietCongNoKhachHang','id'=>$id));
        }
        else{
          die(var_dump($form->getMessages()));
            $this->flashMessenger()->addErrorMessage('Thanh toán thất bại!');
            return $this->redirect()->toRoute('cong_no/crud',array('action'=>'index'));
        }        
      }    

      

      $response=$this->searchCongNoKhachHang($id);
      

      $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if($thongTinDoiTac->getKho()!=$idKho)
      {
        return $this->redirect()->toRoute('cong_no/crud',array('action','index'));
      }
      // die(var_dump($response));
      return array(
        'form'=>$form,
        'thongTinDoiTac'=>$thongTinDoiTac,
        'response'=>$response,
      );
  }

  public function searchCongNoKhachHang($idDoiTac)
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('zfcuser');
    }
    // kiểm tra thuộc kho nào và lấy sản phẩm thuộc kho đó theo thuộc tín: "kho"
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }

      $response=array();
   
      if($idDoiTac)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.kho='.$idKho.' and kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and pt.kho='.$idKho.' and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');
        $query->setParameter('idDoiTac',$idDoiTac);
        
        $congNos = $query->getResult(); // array of CmsArticle objects 

        // nếu đã có công nợ trước với hệ thống
        if($congNos)
        {
          $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
          $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();
          
        }
        else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
        {
          // nợ đầu kỳ
          $noDauKi=0;
          $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

          // lấy ngày đăng ký làm ngày đầu kỳ
          $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
        }

        // lấy nợ phát sinh hoaDon
        $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.status=0 and hd.idDoiTac= :idDoiTac');
        $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $hoaDons=$query->getResult();

        
        $noPhatSinh=0;
        foreach ($hoaDons as $hoaDon) {
          foreach ($hoaDon->getCtHoaDons() as $ctHoaDon) {
            $noPhatSinh+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
          }
        }

        // tính nợ cuối kỳ
        $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
        $response=array(
          'ngayDauKi'=>$ngayDauKi,
          'noDauKi'=>$noDauKi,
          'noPhatSinh'=>$noPhatSinh,
          'noCuoiKi'=>$noCuoiKi,
        );
      }
    return $response;
  }

//-----------------------------------------------------------------  	
  public function congNoNhaCungCapAction()
  {

    // kiểm tra đăng nhập
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
   
     // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }

      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      /*$query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=46');
      $doiTacs=$query->getResult();*/

      $query=$entityManager->createQuery('SELECT distinct dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=46');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      foreach ($doiTacs as $doiTac) 
      {   
        $idDoiTac=$doiTac->getIdDoiTac();  
        if($idDoiTac)
        {

          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          

          $entityManager=$this->getEntityManager();
          $query = $entityManager->createQuery('SELECT pc FROM HangHoa\Entity\DoiTac ncc, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc  WHERE ncc.kho='.$idKho.' and ncc.idDoiTac=cn.idDoiTac and cn.idCongNo=pc.idCongNo and pc.kho='.$idKho.' and ncc.idDoiTac= :idDoiTac ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $congNos = $query->getResult(); // array of CmsArticle objects 

          // nếu đã có công nợ trước với hệ thống
          if($congNos)
          {
            $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
            $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();
            
          }
          else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
          {
            // nợ đầu kỳ
            $noDauKi=0;
            $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

           /* // lấy ngày đăng ký làm ngày đầu kỳ
            $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');*/

            // kiểm tra đối tác này có từng mua hàng ngày nào chưa
            $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.idDoiTac='.$idDoiTac.' and pn.status=0 ORDER BY pn.idPhieuNhap');
            $phieuNhapDauTienCuaDoiTac=$query->getResult();
            if($phieuNhapDauTienCuaDoiTac)
            {
                // lấy ngày đăng ký làm ngày đầu kỳ
                $ngayDauKi=$phieuNhapDauTienCuaDoiTac[0]->getNgayNhap()->format('Y-m-d');
            }
            else
            {
                // lấy ngày đăng ký làm ngày đầu kỳ
                $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
            }
          }

          // lấy nợ phát sinh hoaDon
          $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.status=0 and pn.idDoiTac= :idDoiTac');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $phieuNhaps=$query->getResult();

          
          $noPhatSinh=0;
          foreach ($phieuNhaps as $phieuNhap) {
            foreach ($phieuNhap->getCtPhieuNhaps() as $ctPhieuNhap) {
              $noPhatSinh+=(float)$ctPhieuNhap->getGiaNhap()*(float)$ctPhieuNhap->getSoLuong();
            }
          }

          // tính nợ cuối kỳ
          $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
          $response[]=array(
            'idDoiTac'=>$idDoiTac,
            'hoTenDoiTac'=>$thongTinDoiTac->getHoTen(),
            'ngayDauKi'=>$ngayDauKi,
            'noDauKi'=>$noDauKi,
            'noPhatSinh'=>$noPhatSinh,
            'noCuoiKi'=>$noCuoiKi,
          );
        }
      }
      return array('response'=>$response);
  }

  public function thanhToanNhaCungCapAction()
  {

    // kiểm tra đăng nhập
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }

     $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('cong_no/crud', array(
              'action' => 'congNoNhaCungCap',
          ));
      }  
     // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }

      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();
      $form= new ThanhToanNhaCungCapForm($entityManager); 
      $phieuChi=new PhieuChi();
      $form->bind($phieuChi);

      $request=$this->getRequest();
      if($request->isPost())
      {
        $form->setData($request->getPost());
        if($form->isValid())
        {
          $idUserNv=$this->zfcUserAuthentication()->getIdentity();
          $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($idUserNv);

          $phieuChi->setIdUserNv($user);
          $idDoiTac=$phieuChi->getIdCongNo()->getIdDoiTac()->getIdDoiTac();
          

          $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.status=0 and pn.idDoiTac='.$idDoiTac);
          $phieuNhaps=$query->getResult();

          foreach ($phieuNhaps as $phieuNhap) {
            $phieuNhap->setStatus(1);
            $entityManager->flush();
          }
          $phieuChi->setKho($idKho);
          $entityManager->persist($phieuChi);

          $entityManager->flush();
          $this->flashMessenger()->addSuccessMessage('Thanh toán thành công!');
          return $this->redirect()->toRoute('cong_no/crud', array(
             'action' => 'chiTietCongNoNhaCungCap','id'=>$id,
          ));          
        }
        else
        {
          $this->flashMessenger()->addErrorMessage('Thanh toán thất bại!');
          return $this->redirect()->toRoute('cong_no/crud', array(
              'action' => 'congNoNhaCungCap',
          ));
        }
       
      }

      

      $response=$this->searchCongNoNhaCungCap($id);      

      $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if($thongTinDoiTac->getKho()!=$idKho)
      {
        return $this->redirect()->toRoute('cong_no/crud', array(
              'action' => 'congNoNhaCungCap',
          ));
      }

      return array(
        'form'=>$form,
        'thongTinDoiTac'=>$thongTinDoiTac,
        'response'=>$response,
      );
  }

  public function searchCongNoNhaCungCap($idDoiTac)
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('zfcuser');
    }
    // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }

      if($idDoiTac)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT pc FROM HangHoa\Entity\DoiTac ncc, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc  WHERE ncc.kho='.$idKho.' and ncc.idDoiTac=cn.idDoiTac and cn.idCongNo=pc.idCongNo and pc.kho='.$idKho.' and ncc.idDoiTac= :idDoiTac ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC');
        $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $congNos = $query->getResult(); // array of CmsArticle objects 

        // nếu đã có công nợ trước với hệ thống
        if($congNos)
        {
          $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
          $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();
          
        }
        else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
        {
          // nợ đầu kỳ
          $noDauKi=0;
          $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

          // lấy ngày đăng ký làm ngày đầu kỳ
          $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
        }

        // lấy nợ phát sinh hoaDon
        $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.status=0 and pn.idDoiTac= :idDoiTac');
        $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $phieuNhaps=$query->getResult();

        
        $noPhatSinh=0;
        foreach ($phieuNhaps as $phieuNhap) {
          foreach ($phieuNhap->getCtPhieuNhaps() as $ctPhieuNhap) {
            $noPhatSinh+=(float)$ctPhieuNhap->getGiaNhap()*(float)$ctPhieuNhap->getSoLuong();
          }
        }

        // tính nợ cuối kỳ
        $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
        $response=array(
          'ngayDauKi'=>$ngayDauKi,
          'noDauKi'=>$noDauKi,
          'noPhatSinh'=>$noPhatSinh,
          'noCuoiKi'=>$noCuoiKi,
        );
      }
    return $response;
  } 


  public function exportCongNoKhachHangAction()
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
    $fileName='cong_no_khach_hang';
    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)
    $data=$this->dataExportCongNoKhachHang();
    

    $tieuDe='DANH SÁCH CÔNG NỢ VỚI KHÁCH HÀNG';
    $fieldName=array(0=>'Khách hàng',1=>'Ngày đầu kì',2=>'Đầu kì',3=>'Phát sinh',4=>'Cuối kì',5=>'Kênh phân phối');

    $PI_ExportExcel=$this->ExportExcel();
    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->data($objPHPExcel, $tieuDe, $fieldName, $data));
  }

  // fieldName this is array, it have 5 column
  public function data($objPHPExcel, $tieuDe, $fieldName, $data)
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('zfcuser');
    }
    $entityManager=$this->getEntityManager();

    

    $objPHPExcel->getActiveSheet()->setCellValue('A2', $tieuDe);
    $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
    $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                  
   

    $objPHPExcel->getActiveSheet()->setCellValue('A4', $fieldName[0])
                                  ->setCellValue('B4', $fieldName[1])
                                  ->setCellValue('C4', $fieldName[2])
                                  ->setCellValue('D4', $fieldName[3])
                                  ->setCellValue('E4', $fieldName[4])
                                  ->setCellValue('F4', $fieldName[5])
                                  ->getStyle('A4:F4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $taxonomyKenhPhanPhoi=$this->TaxonomyFunction();
    $kenhPhanPhois=$taxonomyKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug
      
    
    foreach ($data['response'] as $index => $response) {
      $dong=$index+5;

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$dong, $response['hoTenDoiTac']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$dong, $response['ngayDauKi']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$dong, $response['noDauKi']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$dong, $response['noPhatSinh']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$dong, $response['noCuoiKi']);
      foreach ($kenhPhanPhois as $kenhPhanPhoi) {
        if($kenhPhanPhoi['termTaxonomyId']==$response['idKenhPhanPhoi'])
        {
          $objPHPExcel->getActiveSheet()->setCellValue('F'.$dong, $kenhPhanPhoi['termId']['name']);
        }
      }
      
    }
  }

  public function dataExportCongNoKhachHang()
  {

    // kiểm tra đăng nhập==================================================================
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }
    //====================================================================================

    // kiểm tra thuộc kho nào
     $idKho=1;
     if($this->zfcUserAuthentication()->hasIdentity())
     { 
       $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
     }

      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      
      /*$query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=45');
      $doiTacs=$query->getResult();*/
      $query=$entityManager->createQuery('SELECT distinct dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=45');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      foreach ($doiTacs as $doiTac) 
      {   
        //$idDoiTac=$doiTac['idDoiTac'];  
        $idDoiTac=$doiTac->getIdDoiTac();
        if($idDoiTac)
        {

          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          

          $entityManager=$this->getEntityManager();
          $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.kho='.$idKho.' and kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and pt.kho='.$idKho.' and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $congNos = $query->getResult(); // array of CmsArticle objects 

          // nếu đã có công nợ trước với hệ thống
          if($congNos)
          {
            $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
            $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();
            
          }
          else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
          {
            // nợ đầu kỳ
            $noDauKi=0;
            $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

            // lấy ngày đăng ký làm ngày đầu kỳ
            $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
          }

          // lấy nợ phát sinh hoaDon
          $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.status=0 and hd.idDoiTac= :idDoiTac');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $hoaDons=$query->getResult();

          
          $noPhatSinh=0;
          foreach ($hoaDons as $hoaDon) {
            foreach ($hoaDon->getCtHoaDons() as $ctHoaDon) {
              $noPhatSinh+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
            }
          }

          // tính nợ cuối kỳ
          $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
          $response[]=array(
            'idKenhPhanPhoi'=>$thongTinDoiTac->getIdKenhPhanPhoi()->getTermTaxonomyId(),
            'idDoiTac'=>$idDoiTac,
            'hoTenDoiTac'=>$thongTinDoiTac->getHoTen(),
            'ngayDauKi'=>$ngayDauKi,
            'noDauKi'=>$noDauKi,
            'noPhatSinh'=>$noPhatSinh,
            'noCuoiKi'=>$noCuoiKi,
          );
        }
      }

      return array('response'=>$response);
  }

  public function exportCongNoNhaCungCapAction()
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
    $fileName='cong_no_nha_cung_cap';
    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)
    $data=$this->dataExportCongNoNhaCungCap();
    

    $tieuDe='DANH SÁCH CÔNG NỢ VỚI NHÀ CUNG CẤP';
    $fieldName=array(0=>'Tên nhà cung cấp',1=>'Ngày đầu kì',2=>'Đầu kì',3=>'Phát sinh',4=>'Cuối kì');

    $PI_ExportExcel=$this->ExportExcel();
    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->dataNhaCungCap($objPHPExcel, $tieuDe, $fieldName, $data));
  }

  public function dataNhaCungCap($objPHPExcel, $tieuDe, $fieldName, $data)
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('zfcuser');
    }
    $entityManager=$this->getEntityManager();

    $objPHPExcel->getActiveSheet()->setCellValue('A2', $tieuDe);
    $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
    $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                  
   

    $objPHPExcel->getActiveSheet()->setCellValue('A4', $fieldName[0])
                                  ->setCellValue('B4', $fieldName[1])
                                  ->setCellValue('C4', $fieldName[2])
                                  ->setCellValue('D4', $fieldName[3])
                                  ->setCellValue('E4', $fieldName[4])
                                  ->getStyle('A4:E4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    
    foreach ($data['response'] as $index => $response) {
      $dong=$index+5;

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$dong, $response['hoTenDoiTac']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$dong, $response['ngayDauKi']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$dong, $response['noDauKi']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$dong, $response['noPhatSinh']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$dong, $response['noCuoiKi']);
      
    }
  }

  public function dataExportCongNoNhaCungCap()
  {

    // kiểm tra đăng nhập
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
    
    // kiểm tra thuộc kho nào
     $idKho=1;
     if($this->zfcUserAuthentication()->hasIdentity())
     { 
       $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
     }

      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      /*$query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=46');
      $doiTacs=$query->getResult();*/

      $query=$entityManager->createQuery('SELECT distinct dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=46');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      foreach ($doiTacs as $doiTac) 
      {   
        $idDoiTac=$doiTac->getIdDoiTac();  
        if($idDoiTac)
        {
          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          
          $entityManager=$this->getEntityManager();
          $query = $entityManager->createQuery('SELECT pc FROM HangHoa\Entity\DoiTac ncc, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc  WHERE ncc.kho='.$idKho.' and ncc.idDoiTac=cn.idDoiTac and cn.idCongNo=pc.idCongNo and pc.kho='.$idKho.' and ncc.idDoiTac= :idDoiTac ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $congNos = $query->getResult(); // array of CmsArticle objects 

          // nếu đã có công nợ trước với hệ thống
          if($congNos)
          {
            $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
            $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();
            
          }
          else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
          {
            // nợ đầu kỳ
            $noDauKi=0;
            $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

            // lấy ngày đăng ký làm ngày đầu kỳ
            $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
          }

          // lấy nợ phát sinh hoaDon
          $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.status=0 and pn.idDoiTac= :idDoiTac');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $phieuNhaps=$query->getResult();

          
          $noPhatSinh=0;
          foreach ($phieuNhaps as $phieuNhap) {
            foreach ($phieuNhap->getCtPhieuNhaps() as $ctPhieuNhap) {
              $noPhatSinh+=(float)$ctPhieuNhap->getGiaNhap()*(float)$ctPhieuNhap->getSoLuong();
            }
          }

          // tính nợ cuối kỳ
          $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
          $response[]=array(
            'idDoiTac'=>$idDoiTac,
            'hoTenDoiTac'=>$thongTinDoiTac->getHoTen(),
            'ngayDauKi'=>$ngayDauKi,
            'noDauKi'=>$noDauKi,
            'noPhatSinh'=>$noPhatSinh,
            'noCuoiKi'=>$noCuoiKi,
          );
        }
      }
      return array('response'=>$response);
  }

  public function chiTietCongNoKhachHangAction()
  {
    

    // kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }

      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('cong_no/crud');
      }  


    // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }
     
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      $doiTacs=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if($doiTacs->getKho()!=$idKho)
      {
        return $this->redirect()->toRoute('cong_no/crud');
      }


      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      
        //$idDoiTac=$doiTac['idDoiTac'];  
        $idDoiTac=$id;
        if($idDoiTac)
        {
          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          
          $entityManager=$this->getEntityManager();

          $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.kho='.$idKho.' and kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and pt.kho='.$idKho.' and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');        

          
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $congNos = $query->getResult(); // array of CmsArticle objects 

          // nếu đã có công nợ trước với hệ thống
          if($congNos)
          {
            $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
            $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();

            // lấy nợ phát sinh hoaDon
            $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.status=0 and hd.idDoiTac= :idDoiTac');
            $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
            $hoaDons=$query->getResult();            
            $noPhatSinh=0;
            foreach ($hoaDons as $hoaDon) {
              foreach ($hoaDon->getCtHoaDons() as $ctHoaDon) {
                $noPhatSinh+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
              }
            }
            // tính nợ cuối kỳ
            $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
            $thanhToan='Chưa Thanh Toán';
            $duNo='';
            $ngayThanhToan='';

            $response[]=array(
                'ngayDauKi'=>$ngayDauKi,
                'noDauKi'=>$noDauKi,
                'noPhatSinh'=>$noPhatSinh,
                'noCuoiKi'=>$noCuoiKi,
                'thanhToan'=>$thanhToan,
                'duNo'=>$duNo,
                'ngayThanhToan'=>$ngayThanhToan,
                'idPhieuThu'=>'',   
                'phieuChi'=>'',
              );

            foreach ($congNos as $congNo) 
            {
              $ngayDauKi=$congNo->getIdCongNo()->getKi()->format('Y-m-d');
              $noDauKi=$congNo->getIdCongNo()->getNoDauKi();
              $noPhatSinh=$congNo->getIdCongNo()->getNoPhatSinh();
              $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
              $duNo=$congNo->getIdCongNo()->getDuNo();
              $thanhToan=(float)$noCuoiKi-(float)$duNo;
              $ngayThanhToan=$congNo->getNgayThanhToan();
              $idPT=$congNo->getIdPhieuThu();
              $idCongNo=$congNo->getIdCongNo()->getIdCongNo();
              $qr=$entityManager->createQuery('SELECT pc FROM CongNo\Entity\PhieuChi pc WHERE pc.idCongNo='.$idCongNo.' and pc.kho='.$idKho);
              $phieuChi=$qr->getResult();

              $response[]=array(
                'ngayDauKi'=>$ngayDauKi,
                'noDauKi'=>$noDauKi,
                'noPhatSinh'=>$noPhatSinh,
                'noCuoiKi'=>$noCuoiKi,
                'thanhToan'=>$thanhToan,
                'duNo'=>$duNo,
                'ngayThanhToan'=>$ngayThanhToan,
                'idPhieuThu'=>$idPT,  
                'phieuChi'=>$phieuChi,

              );
            }
           
          }
          else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
          {
            // nợ đầu kỳ
            $noDauKi=0;
            $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

            // kiểm tra đối tác này có từng mua hàng ngày nào chưa
            $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.idDoiTac='.$idDoiTac.' and hd.status=0 ORDER BY hd.idHoaDon');
            $hoaDonDauTienCuaDoiTac=$query->getResult();
            if($hoaDonDauTienCuaDoiTac)
            {
                // lấy ngày mua hàng đầu tiên
                $ngayDauKi=$hoaDonDauTienCuaDoiTac[0]->getNgayXuat()->format('Y-m-d');
            }
            else
            {
                // lấy ngày đăng ký làm ngày đầu kỳ
                $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
            }

            // lấy nợ phát sinh hoaDon
            $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.status=0 and hd.idDoiTac= :idDoiTac');
            $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
            $hoaDons=$query->getResult();            
            $noPhatSinh=0;
            foreach ($hoaDons as $hoaDon) {
              foreach ($hoaDon->getCtHoaDons() as $ctHoaDon) {
                $noPhatSinh+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
              }
            }
            // tính nợ cuối kỳ
            $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
            $thanhToan='Chưa Thanh Toán';
            $duNo='';
            $ngayThanhToan='';
            $idPT='';

            $response[]=array(
                'ngayDauKi'=>$ngayDauKi,
                'noDauKi'=>$noDauKi,
                'noPhatSinh'=>$noPhatSinh,
                'noCuoiKi'=>$noCuoiKi,
                'thanhToan'=>$thanhToan,
                'duNo'=>$duNo,
                'ngayThanhToan'=>$ngayThanhToan,
                'idPhieuThu'=>$idPT,
                'phieuChi'=>'',

              );
          }
        }

      return array('response'=>$response,'doiTac'=>$doiTacs,'hoaDons'=>$hoaDons);
  }

   public function chiTietCongNoNhaCungCapAction()
  {
    

    // kiểm tra đăng nhập
      if(!$this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('application');
      }

      // id đối tác
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('cong_no/crud');
      }  


    // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }
     
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      $doiTacs=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      if($doiTacs->getKho()!=$idKho)
      {
        return $this->redirect()->toRoute('cong_no/crud');
      }


      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      
        //$idDoiTac=$doiTac['idDoiTac'];  
        $idDoiTac=$id;
        if($idDoiTac)
        {
          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          
          $entityManager=$this->getEntityManager();

          $query = $entityManager->createQuery('SELECT pc FROM HangHoa\Entity\DoiTac ncc, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc  WHERE ncc.kho='.$idKho.' and ncc.idDoiTac=cn.idDoiTac and cn.idCongNo=pc.idCongNo and pc.kho='.$idKho.' and ncc.idDoiTac= :idDoiTac ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC');        

          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $congNos = $query->getResult(); // array of CmsArticle objects 

          // nếu đã có công nợ trước với hệ thống
          if($congNos)
          {
            $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
            $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();

            // lấy nợ phát sinh hoaDon
            $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.status=0 and pn.idDoiTac= :idDoiTac');
            $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
            $phieuNhaps=$query->getResult();            
            $noPhatSinh=0;
            foreach ($phieuNhaps as $phieuNhap) {
              foreach ($phieuNhap->getCtPhieuNhaps() as $ctPhieuNhap) {
                $noPhatSinh+=(float)$ctPhieuNhap->getGiaNhap()*(float)$ctPhieuNhap->getSoLuong();
              }
            }
            // tính nợ cuối kỳ
            $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
            $thanhToan='Chưa Thanh Toán';
            $duNo='';
            $ngayThanhToan='';

            $response[]=array(
                'ngayDauKi'=>$ngayDauKi,
                'noDauKi'=>$noDauKi,
                'noPhatSinh'=>$noPhatSinh,
                'noCuoiKi'=>$noCuoiKi,
                'thanhToan'=>$thanhToan,
                'duNo'=>$duNo,
                'ngayThanhToan'=>$ngayThanhToan,                
              );

            foreach ($congNos as $congNo) 
            {
              $ngayDauKi=$congNo->getIdCongNo()->getKi()->format('Y-m-d');
              $noDauKi=$congNo->getIdCongNo()->getNoDauKi();
              $noPhatSinh=$congNo->getIdCongNo()->getNoPhatSinh();
              $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
              $duNo=$congNo->getIdCongNo()->getDuNo();
              $thanhToan=(float)$noCuoiKi-(float)$duNo;
              $ngayThanhToan=$congNo->getNgayThanhToan();

              $response[]=array(
                'ngayDauKi'=>$ngayDauKi,
                'noDauKi'=>$noDauKi,
                'noPhatSinh'=>$noPhatSinh,
                'noCuoiKi'=>$noCuoiKi,
                'thanhToan'=>$thanhToan,
                'duNo'=>$duNo,
                'ngayThanhToan'=>$ngayThanhToan,

              );
            }
           
          }
          else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
          {
            // nợ đầu kỳ
            $noDauKi=0;
            $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

            // kiểm tra đối tác này có từng mua hàng ngày nào chưa
            $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.idDoiTac='.$idDoiTac.' and pn.status=0 ORDER BY pn.idPhieuNhap');
            $phieuNhapDauTienCuaDoiTac=$query->getResult();
            if($phieuNhapDauTienCuaDoiTac)
            {
                // lấy ngày mua hàng đầu tiên
                $ngayDauKi=$phieuNhapDauTienCuaDoiTac[0]->getNgayNhap()->format('Y-m-d');
            }
            else
            {
                // lấy ngày đăng ký làm ngày đầu kỳ
                $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
            }

            // lấy nợ phát sinh hoaDon
            $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.kho='.$idKho.' and pn.status=0 and pn.idDoiTac= :idDoiTac');
            $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
            $phieuNhaps=$query->getResult();            
            $noPhatSinh=0;
            foreach ($phieuNhaps as $phieuNhap) {
              foreach ($phieuNhap->getCtPhieuNhaps() as $ctPhieuNhap) {
                $noPhatSinh+=(float)$ctPhieuNhap->getGiaNhap()*(float)$ctPhieuNhap->getSoLuong();
              }
            }
            // tính nợ cuối kỳ
            $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;
            $thanhToan='Chưa Thanh Toán';
            $duNo='';
            $ngayThanhToan='';

            $response[]=array(
                'ngayDauKi'=>$ngayDauKi,
                'noDauKi'=>$noDauKi,
                'noPhatSinh'=>$noPhatSinh,
                'noCuoiKi'=>$noCuoiKi,
                'thanhToan'=>$thanhToan,
                'duNo'=>$duNo,
                'ngayThanhToan'=>$ngayThanhToan,

              );
          }
        }

      return array('response'=>$response,'doiTac'=>$doiTacs,'phieuNhaps'=>$phieuNhaps);
  }

  public function xuatPhieuChiAction()
  {
    // id công nợ
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('cong_no/crud');
      } 

      // kiểm tra thuộc kho nào
      $idKho=1;
      if($this->zfcUserAuthentication()->hasIdentity())
      { 
        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
      }
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

      $phieuThu=$entityManager->getRepository('CongNo\Entity\PhieuThu')->find($id);
      $idDoiTac=$phieuThu->getIdCongNo()->getIdDoiTac()->getIdDoiTac();
      $form=new PhieuChiForm($entityManager);

      $phieuChi=new PhieuChi();

      $form->bind($phieuChi);

      $request=$this->getRequest();
      if($request->isPost())
      {        
        $form->setData($request->getPost());
        if($form->isValid())
        {
          $phieuChi->setKho($idKho);
          $entityManager->persist($phieuChi);
          $entityManager->flush();
          $this->flashMessenger()->addSuccessMessage('Xuất phiếu chi thành công');
          return $this->redirect()->toRoute('cong_no/crud',array('action'=>'chiTietCongNoKhachHang','id'=>$idDoiTac));
        }
        else
        {
          $this->flashMessenger()->addErrorMessage('Xuất phiếu chi thất bại');
          return $this->redirect()->toRoute('cong_no/crud',array('action'=>'chiTietCongNoKhachHang','id'=>$idDoiTac));
        }

      }


      return array(
        'form'=>$form,
        'phieuThu'=>$phieuThu,
      );

  }
 }
?>
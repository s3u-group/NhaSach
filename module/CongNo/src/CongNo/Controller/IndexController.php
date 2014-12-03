<?php 
namespace CongNo\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 use CongNo\Form\ThanhToanForm;
 use CongNo\Entity\PhieuThu;
 use CongNo\Form\ThanhToanNhaCungCapForm;
 use CongNo\Form\PhieuChiFieldset;
 use CongNo\Entity\PhieuChi;

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


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      $query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=45');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      foreach ($doiTacs as $doiTac) 
      {   
        $idDoiTac=$doiTac['idDoiTac'];  
        if($idDoiTac)
        {

          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          

          $entityManager=$this->getEntityManager();
          $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');
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
          $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.status=0 and hd.idDoiTac= :idDoiTac');
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
          $user=$entityManager->getRepository('Application\Entity\SystemUser')->find(1);
          $phieuThu->setIdUserNv($user);
          $idDoiTac=$phieuThu->getIdCongNo()->getIdDoiTac()->getIdDoiTac();

          $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.status=0 and hd.idDoiTac='.$idDoiTac);
          $hoaDons=$query->getResult();

          foreach ($hoaDons as $hoaDon) {
            $hoaDon->setStatus(1);
            $entityManager->flush();
          }

          $entityManager->persist($phieuThu);
          $entityManager->flush();
          return $this->redirect()->toRoute('cong_no/crud',array('action','index'));
        }        
      }    

       $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('cong_no/crud', array(
              'action' => 'index',
          ));
      }  

      $response=$this->searchCongNoKhachHang($id);
      

      $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);
      // die(var_dump($response));
      return array(
        'form'=>$form,
        'thongTinDoiTac'=>$thongTinDoiTac,
        'response'=>$response,
      );
  }


  

  public function searchCongNoKhachHang($idDoiTac)
  {
      $response=array();
   
      if($idDoiTac)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');
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
        $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.status=0 and hd.idDoiTac= :idDoiTac');
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
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      $query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=46');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      foreach ($doiTacs as $doiTac) 
      {   
        $idDoiTac=$doiTac['idDoiTac'];  
        if($idDoiTac)
        {

          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          

          $entityManager=$this->getEntityManager();
          $query = $entityManager->createQuery('SELECT pc FROM HangHoa\Entity\DoiTac ncc, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc  WHERE ncc.idDoiTac=cn.idDoiTac and cn.idCongNo=pc.idCongNo and ncc.idDoiTac= :idDoiTac ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC');
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
          $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.status=0 and pn.idDoiTac= :idDoiTac');
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
          $user=$entityManager->getRepository('Application\Entity\SystemUser')->find(1);

          $phieuChi->setIdUserNv($user);
          $idDoiTac=$phieuChi->getIdCongNo()->getIdDoiTac()->getIdDoiTac();
          

          $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.status=0 and pn.idDoiTac='.$idDoiTac);
          $phieuNhaps=$query->getResult();

          foreach ($phieuNhaps as $phieuNhap) {
            $phieuNhap->setStatus(1);
            $entityManager->flush();
          }

          $entityManager->persist($phieuChi);

          $entityManager->flush();
          return $this->redirect()->toRoute('cong_no/crud', array(
             'action' => 'congNoNhaCungCap',
          ));
          
        }
        else
          die(var_dump($form->getMessages()));
      }

      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('cong_no/crud', array(
              'action' => 'congNoNhaCungCap',
          ));
      }  

      $response=$this->searchCongNoNhaCungCap($id);
      //die(var_dump($response));

      $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($id);

      return array(
        'form'=>$form,
        'thongTinDoiTac'=>$thongTinDoiTac,
        'response'=>$response,
      );
  }


  public function searchCongNoNhaCungCap($idDoiTac)
  {
      if($idDoiTac)
      {
        $entityManager=$this->getEntityManager();
        $query = $entityManager->createQuery('SELECT pc FROM HangHoa\Entity\DoiTac ncc, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc  WHERE ncc.idDoiTac=cn.idDoiTac and cn.idCongNo=pc.idCongNo and ncc.idDoiTac= :idDoiTac ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC');
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
        $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.status=0 and pn.idDoiTac= :idDoiTac');
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
 }
?>
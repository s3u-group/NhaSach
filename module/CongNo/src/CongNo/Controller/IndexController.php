<?php 
namespace CongNo\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 use CongNo\Form\ThanhToanForm;

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
      $congNos=array();
      foreach ($doiTacs as $doiTac) {
        $query=$entityManager->createQuery('SELECT pt FROM CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt WHERE cn.idCongNo=pt.idCongNo and cn.idDoiTac='.$doiTac['idDoiTac'].' ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC' );
        $congNos[]=$query->getResult();
      }

      $taxonomyKenhPhanPhoi=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

      
      return array(
        'congNos'=>$congNos,
        'kenhPhanPhois'=>$kenhPhanPhois,
      );
 	} 	


  public function congNoNhaCungCapAction()
  {
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      $query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=46');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $congNos=array();
      foreach ($doiTacs as $doiTac) {
        $query=$entityManager->createQuery('SELECT pc FROM CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc WHERE cn.idCongNo=pc.idCongNo and cn.idDoiTac='.$doiTac['idDoiTac'].' ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC' );
        $congNos[]=$query->getResult();
      }

      return array(
        'congNos'=>$congNos,
      );
  }


 	public function thanhToanAction()
 	{
    	$this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();     
      $form= new ThanhToanForm($entityManager); 
      return array(
        'form'=>$form,
      );
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
        $query = $entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.loaiDoiTac=45 and kh.hoTen LIKE :ten');
        $query->setParameter('ten','%'.$tenKhachHang.'%');// % đặt ở dưới này thì được đặt ở trên bị lỗi
        $khachHangs = $query->getResult(); // array of CmsArticle objects 
        foreach ($khachHangs as $khachHang) {
          $response[]=array(
            'idKhachHang'=>$khachHang->getIdDoiTac(),
            'tenKhachHang'=>$khachHang->getHoTen(),            
          );
        }
      }
    }

    $json = new JsonModel($response);
    return $json;
  }

  public function searchCongNoKhachHangAction()
  {
    $response=array();

    $request=$this->getRequest();
    if($request->isXmlHttpRequest())
    {
      $data=$request->getPost();
      $idDoiTac=$data['idDoiTac'];
      if($idDoiTac)
      {
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
        $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\hoaDon hd WHERE hd.status=0 and hd.idDoiTac= :idDoiTac');
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
    }

    $json = new JsonModel($response);
    return $json;
  }

  public function thanhToanNhaCungCapAction()
  {
      $this->layout('layout/giaodien');
      die(var_dump('Form thanh toan cong no voi nha cung cap'));
  } 
 }
?>
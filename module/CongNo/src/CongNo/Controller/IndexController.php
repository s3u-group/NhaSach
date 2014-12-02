<?php namespace CongNo\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 
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
 	} 

  public function thanhToanNhaCungCapAction()
  {
      $this->layout('layout/giaodien');
      die(var_dump('Form thanh toan cong no voi nha cung cap'));
  } 
 }
?>
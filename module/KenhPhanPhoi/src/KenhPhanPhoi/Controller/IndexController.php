<?php namespace KenhPhanPhoi\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 use HangHoa\Entity\DoiTac;
 use KenhPhanPhoi\Form\ThemKhachHangForm;
 use KenhPhanPhoi\Form\KhachHangFieldset;
 use HangHoa\Entity\CTHoaDon;
 
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

 	public function donHangAction()
 	{
    	$this->layout('layout/giaodien');
      

 	}

 	public function doanhThuAction()
 	{
    	$this->layout('layout/giaodien');
 	}

  public function chiTietDonHangAction()
  {
      $this->layout('layout/giaodien');
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
           return $this->redirect()->toRoute('kenh_phan_phoi/crud');
      }  
      $entityManager=$this->getEntityManager();
      //die(var_dump($id));
      $hoaDon=$entityManager->getRepository('HangHoa\Entity\HoaDon')->find($id);
      
      $query=$entityManager->createQuery('SELECT cthd FROM HangHoa\Entity\CTHoaDon cthd WHERE cthd.idHoaDon='.$id);
      $chiTietHoaDons=$query->getResult();
      //die(var_dump($chiTietHoaDon));

      
      return array(
        'chiTietHoaDons'=>$chiTietHoaDons,
        'hoaDon'=>$hoaDon,
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
          $query = $entityManager->createQuery('SELECT kh FROM HangHoa\Entity\DoiTac kh WHERE kh.hoTen=\''.$doiTac->getHoTen().'\' and kh.diaChi=\''.$doiTac->getDiaChi().'\'');
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
            
          }          
        }
      }
      return array(
        'form' => $form, 
        'kenhPhanPhois'=>$kenhPhanPhois,
        'ktTonTaiKhachHang'=>0,
      ); 

 	}

  public function khachHangAction()
  {
      $this->layout('layout/giaodien');
  }
 }
?>
<?php namespace HangHoa\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 use HangHoa\Entity\SanPham;
 use HangHoa\Form\CreateSanPhamForm;

 use Zend\Validator\File\Size;

 use Zend\Stdlib\AbstractOptions;
 
 use S3UTaxonomy\Form\CreateTermTaxonomyForm;
 
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
    $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->findAll(); 
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
     $sanPhams=new SanPhamFieldset();
     $form= new CreateSanPhamForm($entityManager);
     $form->bind($bangTin);
     $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($id); 
     return array(
       'sanPhams'=>$sanPhams,
       'form' =>$form,
     );

  }

  public function bangGiaAction()
  {
    $this->layout('layout/giaodien');  
  }

  public function nhapHangAction()
  {
    $this->layout('layout/giaodien');  
  }

  public function xuatHangAction()
  {
    $this->layout('layout/giaodien');  
  }

  

  public function themSanPhamAction()
  {

    $this->layout('layout/giaodien'); 

    $entityManager=$this->getEntityManager();
    $sanPham=new sanPham();
    $form= new CreateSanPhamForm($entityManager);
    $form->bind($sanPham);

    $taxonomyLoai=$this->TaxonomyFunction();
    $loais=$taxonomyLoai->getListChildTaxonomy('danh-muc-hang-hoa');// đưa vào taxonomy dạng slug
    
    $taxonomyDonViTinh=$this->TaxonomyFunction();
    $donViTinhs=$taxonomyDonViTinh->getListChildTaxonomy('don-vi-tinh');// đưa vào taxonomy dạng slug

    $request = $this->getRequest();

    if($request->isPost())
    {      
      $form->setData($request->getPost());      
      if ($form->isValid()){
        die(var_dump($sanPham->getMaSanPham()));
        $repository = $entityManager->getRepository('HangHoa\Entity\SanPham');
        $queryBuilder = $repository->createQueryBuilder('sp');
        //$queryBuilder->add('where','sp.maSanPham=\''..'\'');
        //$query = $queryBuilder->getQuery(); 
        //$sanPham = $query->execute();
      }      
    }

    return array(
      'form' => $form, 
      'loais'=>$loais,
      'donViTinhs'=>$donViTinhs,      
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

 }
?>
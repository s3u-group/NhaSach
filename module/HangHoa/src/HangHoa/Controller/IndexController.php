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
     $form= new CreateSanPhamForm($entityManager);     
     $sanPhams=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($id); 
     $form->bind($sanPhams);

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
      $post = array_merge_recursive(
              $request->getPost()->toArray(),
              $request->getFiles()->toArray()
          );

      $form->setData($request->getPost());      
      if ($form->isValid()){
        //die(var_dump($sanPham->getMaSanPham()));
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

 }
?>
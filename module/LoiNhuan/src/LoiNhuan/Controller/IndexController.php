<?php namespace LoiNhuan\Controller;

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
  
 	

	public function donHangAction()
	{
	    $this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $donHangs=$entityManager->getRepository('HangHoa\Entity\HoaDon')->findAll();
	    
	    $taxonomyFunction=$this->TaxonomyFunction();
    	$kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

	    return array(
	    	'donHangs'=>$donHangs,
	    	'kenhPhanPhois'=>$kenhPhanPhois,
	    );
	      

	}


	// index là doanh thu
	public function indexAction()
	{
	    $this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $donHangs=$entityManager->getRepository('HangHoa\Entity\HoaDon')->findAll();

	    
	    foreach ($donHangs as $donHang) {
	    	$donHang->getNgayXuat();
	    	$yMs[]=$donHang->getNgayXuat()->format('Y-m');	    	
	    }

	    $functionDistinct=$this->DistinctPlugin();
	    $namThangs=$functionDistinct->DistinctFunction($yMs);
	    foreach ($namThangs as $namThang) {

	    	$strStart=$namThang.'-01';
	    	$strEnd=$namThang.'-31';
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($strStart).'\''.' and hd.ngayXuat<= \''.trim($strEnd).'\'');
	    	$hD=$query->getResult();

	    	$doanhThus[]=array(
	    		'thoiGian'=>$namThang,
	    		'chiTietDoanhThus'=>$hD,
	    	);
	    	
	    }

	    
	    
	    $taxonomyFunction=$this->TaxonomyFunction();
    	$kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

    	//die(var_dump($doanhThus));
    	//die(var_dump($doanhThus[0]['chiTietDoanhThus'][0]->getIdDoiTac()->getIdKenhPhanPhoi()));
	    return array(
	    	'kenhPhanPhois'=>$kenhPhanPhois,
	    	'doanhThus'=>$doanhThus,
	    );
	}

  
 }
?>
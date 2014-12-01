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



	// index là doanh thu theo ngày
	public function indexAction()
	{
	    $this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $donHangs=$entityManager->getRepository('HangHoa\Entity\HoaDon')->findAll();


	    $yMs=array();
	    $doanhThus=array();
	    foreach ($donHangs as $donHang) {
	    	$donHang->getNgayXuat();
	    	$yMs[]=$donHang->getNgayXuat()->format('Y-m-d');	    	
	    }

	    $functionDistinct=$this->DistinctPlugin();
	    $namThangs=$functionDistinct->DistinctFunction($yMs);
	    foreach ($namThangs as $namThang) {

	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat = \''.trim($namThang).'\'');
	    	$hD=$query->getResult();

	    	$thoiGianTam=explode('-', $namThang);
	    	$thoiGian=$thoiGianTam[2].'-'.$thoiGianTam[1].'-'.$thoiGianTam[0];


	    	$doanhThus[]=array(
	    		'thoiGian'=>$thoiGian,
	    		'chiTietDoanhThus'=>$hD,
	    	);
	    	
	    }

	    //die(var_dump($doanhThus));

	    $taxonomyFunction=$this->TaxonomyFunction();
    	$kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

	    return array(
	    	'kenhPhanPhois'=>$kenhPhanPhois,
	    	'doanhThus'=>$doanhThus,
	    );
	}



	public function doanhThuTheoThangAction()
	{
	    $this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $donHangs=$entityManager->getRepository('HangHoa\Entity\HoaDon')->findAll();

	    $yMs=array();
	    $doanhThus=array();
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

	    	$thoiGianTam=explode('-', $namThang);
	    	$thoiGian=$thoiGianTam[1].'-'.$thoiGianTam[0];


	    	$doanhThus[]=array(
	    		'thoiGian'=>$thoiGian,
	    		'chiTietDoanhThus'=>$hD,
	    	);
	    	
	    }

	    //die(var_dump($doanhThus));

	    $taxonomyFunction=$this->TaxonomyFunction();
    	$kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

	    return array(
	    	'kenhPhanPhois'=>$kenhPhanPhois,
	    	'doanhThus'=>$doanhThus,
	    );
	}


	public function doanhThuTheoQuyAction()
	{
	    $this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $donHangs=$entityManager->getRepository('HangHoa\Entity\HoaDon')->findAll();

	    $yMs=array();
	    $doanhThus=array();
	    foreach ($donHangs as $donHang) {
	    	$donHang->getNgayXuat();
	    	$yMs[]=$donHang->getNgayXuat()->format('Y');	    	
	    }

	    $functionDistinct=$this->DistinctPlugin();
	    $namThangs=$functionDistinct->DistinctFunction($yMs);
	    foreach ($namThangs as $namThang) {

	    	$batDauQuy1=$namThang.'-01-01';
	    	$ketThucQuy1=$namThang.'-3-31';


	    	$batDauQuy2=$namThang.'-4-01';
	    	$ketThucQuy2=$namThang.'-6-30';

	    	$batDauQuy3=$namThang.'-7-01';
	    	$ketThucQuy3=$namThang.'-9-30';

	    	$batDauQuy4=$namThang.'-10-01';
	    	$ketThucQuy4=$namThang.'-12-31';


	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy1).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy1).'\'');
	    	$hDQuy1=$query->getResult();

	    	if($hDQuy1)
	    	{
	    		$thoiGian='Quý 1 - '.$namThang;

		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy1,
		    	);
	    	}

	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy2).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy2).'\'');
	    	$hDQuy2=$query->getResult();

	    	if($hDQuy2)
	    	{
	    		$thoiGian='Quý 2 - '.$namThang;
	    		
		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy2,
		    	);
	    	}


	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy3).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy3).'\'');
	    	$hDQuy3=$query->getResult();

	    	if($hDQuy3)
	    	{
	    		$thoiGian='Quý 3 - '.$namThang;
	    		
		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy3,
		    	);
	    	}

	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy4).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy4).'\'');
	    	$hDQuy4=$query->getResult();

	    	if($hDQuy4)
	    	{
	    		$thoiGian='Quý 4 - '.$namThang;
	    		
		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy4,
		    	);
	    	}	    	
	    	
	    }

	    //die(var_dump($doanhThus));

	    $taxonomyFunction=$this->TaxonomyFunction();
    	$kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

	    return array(
	    	'kenhPhanPhois'=>$kenhPhanPhois,
	    	'doanhThus'=>$doanhThus,
	    );
	}


	public function doanhThuTheoNamAction()
	{
	    $this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $donHangs=$entityManager->getRepository('HangHoa\Entity\HoaDon')->findAll();

	    $yMs=array();
	    $doanhThus=array();
	    foreach ($donHangs as $donHang) {
	    	$donHang->getNgayXuat();
	    	$yMs[]=$donHang->getNgayXuat()->format('Y');	    	
	    }

	    $functionDistinct=$this->DistinctPlugin();
	    $namThangs=$functionDistinct->DistinctFunction($yMs);
	    foreach ($namThangs as $namThang) {

	    	$strStart=$namThang.'-01-01';
	    	$strEnd=$namThang.'-12-31';
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($strStart).'\''.' and hd.ngayXuat<= \''.trim($strEnd).'\'');
	    	$hD=$query->getResult();

	    	$thoiGian=$namThang;


	    	$doanhThus[]=array(
	    		'thoiGian'=>$thoiGian,
	    		'chiTietDoanhThus'=>$hD,
	    	);
	    	
	    }

	    //die(var_dump($doanhThus));

	    $taxonomyFunction=$this->TaxonomyFunction();
    	$kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

	    return array(
	    	'kenhPhanPhois'=>$kenhPhanPhois,
	    	'doanhThus'=>$doanhThus,
	    );
	}

  
 }
?>
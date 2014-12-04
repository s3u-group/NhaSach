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
	    	$idThoiGian=(int)$thoiGianTam[0].$thoiGianTam[1].$thoiGianTam[2];
	    	$doanhThus[]=array(
	    		'thoiGian'=>$thoiGian,
	    		'chiTietDoanhThus'=>$hD,
	    		'idThoiGian'=>$idThoiGian,
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
	    	$idThoiGian=(int)$thoiGianTam[0].$thoiGianTam[1];


	    	$doanhThus[]=array(
	    		'thoiGian'=>$thoiGian,
	    		'chiTietDoanhThus'=>$hD,
	    		'idThoiGian'=>$idThoiGian,
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
	    		$idThoiGian=$namThang.'01';

		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy1,
		    		'idThoiGian'=>$idThoiGian,
		    	);
	    	}

	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy2).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy2).'\'');
	    	$hDQuy2=$query->getResult();

	    	if($hDQuy2)
	    	{
	    		$thoiGian='Quý 2 - '.$namThang;
	    		$idThoiGian=$namThang.'02';
	    		
		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy2,
		    		'idThoiGian'=>$idThoiGian,
		    	);
	    	}


	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy3).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy3).'\'');
	    	$hDQuy3=$query->getResult();

	    	if($hDQuy3)
	    	{
	    		$thoiGian='Quý 3 - '.$namThang;
	    		$idThoiGian=$namThang.'03';
	    		
		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy3,
		    		'idThoiGian'=>$idThoiGian,
		    	);
	    	}

	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy4).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy4).'\'');
	    	$hDQuy4=$query->getResult();

	    	if($hDQuy4)
	    	{
	    		$thoiGian='Quý 4 - '.$namThang;
	    		$idThoiGian=$namThang.'04';
	    		
		    	$doanhThus[]=array(
		    		'thoiGian'=>$thoiGian,
		    		'chiTietDoanhThus'=>$hDQuy4,
		    		'idThoiGian'=>$idThoiGian,
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
	    	$idThoiGian=(int)$namThang;

	    	$doanhThus[]=array(
	    		'thoiGian'=>$thoiGian,
	    		'chiTietDoanhThus'=>$hD,
	    		'idThoiGian'=>$idThoiGian,
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

	public function chiTietDoanhThuNgayAction()
	{
		$this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $id = (int) $this->params()->fromRoute('id', 0);
	    if (!$id) {
	        return $this->redirect()->toRoute('loi_nhuan/crud', array(
	            'action' => 'index',
	        ));
	    }  

	    $thoiGian=(string)$id;
	    $y=$thoiGian[0].$thoiGian[1].$thoiGian[2].$thoiGian[3];
	    $m=$thoiGian[4].$thoiGian[5];
	    $d=$thoiGian[6].$thoiGian[7];
	    $idKenhPhanPhoi=(int)$thoiGian[8].$thoiGian[9];
	    $thoiGian=$y.'-'.$m.'-'.$d;

	    // kiểm tra nếu dữ liệu gửi qua thuộc kênh nào thì mình chỉ select theo kênh đó. còn nếu idkenhphanphoi=00 thì select tất cả
	    if((int)$idKenhPhanPhoi!=00)
	    {
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd, HangHoa\Entity\DoiTac dt WHERE hd.idDoiTac=dt.idDoiTac and dt.idKenhPhanPhoi='.$idKenhPhanPhoi.' and hd.ngayXuat = \''.trim($thoiGian).'\'');
	    }
	    else
	    {
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat = \''.trim($thoiGian).'\'');
	    }
	    
	    $hoaDons=$query->getResult();
	   	return array('hoaDons'=>$hoaDons);

	}

	public function chiTietDoanhThuThangAction()
	{
		$this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $id = (int) $this->params()->fromRoute('id', 0);
	    if (!$id) {
	        return $this->redirect()->toRoute('loi_nhuan/crud', array(
	            'action' => 'index',
	        ));
	    }  

	    $thoiGian=(string)$id;
	    $y=$thoiGian[0].$thoiGian[1].$thoiGian[2].$thoiGian[3];
	    $m=$thoiGian[4].$thoiGian[5];
	    $idKenhPhanPhoi=(int)$thoiGian[6].$thoiGian[7];	    
	    $thoiGian=$y.'-'.$m;

	    $strStart=$thoiGian.'-01';
    	$strEnd=$thoiGian.'-31';

    	if((int)$idKenhPhanPhoi!=00)
	    {
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd, HangHoa\Entity\DoiTac dt WHERE hd.idDoiTac=dt.idDoiTac and dt.idKenhPhanPhoi='.$idKenhPhanPhoi.' and hd.ngayXuat >= \''.trim($strStart).'\''.' and hd.ngayXuat<= \''.trim($strEnd).'\'');
	    }
	    else
	    {
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($strStart).'\''.' and hd.ngayXuat<= \''.trim($strEnd).'\'');
	    }

    	
    	$hoaDons=$query->getResult();

	   	return array('hoaDons'=>$hoaDons);

	}  

	public function chiTietDoanhThuQuyAction()
	{
		$this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $id = (int) $this->params()->fromRoute('id', 0);
	    if (!$id) {
	        return $this->redirect()->toRoute('loi_nhuan/crud', array(
	            'action' => 'index',
	        ));
	    }  

	    $thoiGian=(string)$id;
	    $y=$thoiGian[0].$thoiGian[1].$thoiGian[2].$thoiGian[3];
	    $quy=(int)$thoiGian[4].$thoiGian[5];
	    $idKenhPhanPhoi=(int)$thoiGian[6].$thoiGian[7];
	    $thoiGian=$y;
	    if($quy==01)
	    {
	    	$batDauQuy1=$thoiGian.'-01-01';
    		$ketThucQuy1=$thoiGian.'-3-31';
    		// kiểm tra nếu dữ liệu gửi qua thuộc kênh nào thì mình chỉ select theo kênh đó. còn nếu idkenhphanphoi=00 thì select tất cả
		    if((int)$idKenhPhanPhoi!=00)
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd, HangHoa\Entity\DoiTac dt WHERE hd.idDoiTac=dt.idDoiTac and dt.idKenhPhanPhoi='.$idKenhPhanPhoi.' and hd.ngayXuat >= \''.trim($batDauQuy1).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy1).'\'');
		    }
		    else
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy1).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy1).'\'');
		    }
	    }
	    if($quy==02)
	    {
	    	$batDauQuy2=$thoiGian.'-4-01';
    		$ketThucQuy2=$thoiGian.'-6-30';
    		if((int)$idKenhPhanPhoi!=00)
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd, HangHoa\Entity\DoiTac dt WHERE hd.idDoiTac=dt.idDoiTac and dt.idKenhPhanPhoi='.$idKenhPhanPhoi.' and hd.ngayXuat >= \''.trim($batDauQuy2).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy2).'\'');
		    }
		    else
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy2).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy2).'\'');
		    }
	    }
	    if($quy==03)
	    {
	    	$batDauQuy3=$thoiGian.'-7-01';
    		$ketThucQuy3=$thoiGian.'-9-30';
    		if((int)$idKenhPhanPhoi!=00)
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd, HangHoa\Entity\DoiTac dt WHERE hd.idDoiTac=dt.idDoiTac and dt.idKenhPhanPhoi='.$idKenhPhanPhoi.' and hd.ngayXuat >= \''.trim($batDauQuy3).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy3).'\'');
		    }
		    else
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy3).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy3).'\'');
		    }
	    }
	    if($quy==04)
	    {
	    	$batDauQuy4=$thoiGian.'-10-01';
    		$ketThucQuy4=$thoiGian.'-12-31';
    		if((int)$idKenhPhanPhoi!=00)
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd, HangHoa\Entity\DoiTac dt WHERE hd.idDoiTac=dt.idDoiTac and dt.idKenhPhanPhoi='.$idKenhPhanPhoi.' and hd.ngayXuat >= \''.trim($batDauQuy4).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy4).'\'');
		    }
		    else
		    {
		    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($batDauQuy4).'\''.' and hd.ngayXuat<= \''.trim($ketThucQuy4).'\'');
		    }
	    }
	    
	    $hoaDons=$query->getResult();
	   	return array('hoaDons'=>$hoaDons);

	}

	public function chiTietDoanhThuNamAction()
	{
		$this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $id = (int) $this->params()->fromRoute('id', 0);
	    if (!$id) {
	        return $this->redirect()->toRoute('loi_nhuan/crud', array(
	            'action' => 'index',
	        ));
	    }  

	    $thoiGian=(string)$id;
	    $y=$thoiGian[0].$thoiGian[1].$thoiGian[2].$thoiGian[3];
	    $idKenhPhanPhoi=(int)$thoiGian[4].$thoiGian[5];
	    $thoiGian=$y;
	    $strStart=$thoiGian.'-01-01';
	    $strEnd=$thoiGian.'-12-31';

	    // kiểm tra nếu dữ liệu gửi qua thuộc kênh nào thì mình chỉ select theo kênh đó. còn nếu idkenhphanphoi=00 thì select tất cả
	    if((int)$idKenhPhanPhoi!=00)
	    {
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd, HangHoa\Entity\DoiTac dt WHERE hd.idDoiTac=dt.idDoiTac and dt.idKenhPhanPhoi='.$idKenhPhanPhoi.' and hd.ngayXuat >= \''.trim($strStart).'\''.' and hd.ngayXuat<= \''.trim($strEnd).'\'');
	    }
	    else
	    {
	    	$query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.ngayXuat >= \''.trim($strStart).'\''.' and hd.ngayXuat<= \''.trim($strEnd).'\'');
	    }
	    
	    $hoaDons=$query->getResult();
	   	return array('hoaDons'=>$hoaDons);

	}
 }
?>
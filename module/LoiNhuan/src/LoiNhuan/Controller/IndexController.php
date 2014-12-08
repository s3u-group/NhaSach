<?php namespace LoiNhuan\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;

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
    	// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================
      if(!$this->entityManager)
      {
       $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
      }
      return $this->entityManager;
    }
  
 	

	public function donHangAction()
	{

		// kiểm tra đăng nhập==================================================================
	     if(!$this->zfcUserAuthentication()->hasIdentity())
	     {
	       return $this->redirect()->toRoute('application');
	     }
	     //====================================================================================

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

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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
		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

	public function exportDoanhThuTheoNgayAction()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

		$entityManager=$this->getEntityManager();
	    // tham số thức nhất cho hàm exportExcel
	    $objPHPExcel = new PHPExcel();
	    // tham số thức 2 cho hàm exportExcel
	    $fileName='doanh_thu_theo_ngay';
	    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)

	    $tieuDe='THỐNG KÊ DOANH THU THEO NGÀY';
	    $fieldName=array(0=>'Thời gian',1=>'Số đơn hàng',2=>'Doanh thu',3=>'Lợi nhuận');

	    $doanhThus=$this->dataDoanhThuTheoNgay();

	    $PI_ExportExcel=$this->ExportExcel();
	    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->data($objPHPExcel, $tieuDe, $fieldName, $doanhThus));

	    return $this->redirect()->toRoute('loi_nhuan/crud',array('action'=>'index'));   
	}

	public function dataDoanhThuTheoNgay()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================
	    

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
	    return $doanhThus;	   
	   
	}


	public function exportDoanhThuTheoThangAction()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

		$entityManager=$this->getEntityManager();
	    // tham số thức nhất cho hàm exportExcel
	    $objPHPExcel = new PHPExcel();
	    // tham số thức 2 cho hàm exportExcel
	    $fileName='doanh_thu_theo_thang';
	    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)

	    $tieuDe='THỐNG KÊ DOANH THU THEO THÁNG';
	    $fieldName=array(0=>'Thời gian',1=>'Số đơn hàng',2=>'Doanh thu',3=>'Lợi nhuận');

	    $doanhThus=$this->dataDoanhThuTheoThang();

	    $PI_ExportExcel=$this->ExportExcel();
	    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->data($objPHPExcel, $tieuDe, $fieldName, $doanhThus));

	    return $this->redirect()->toRoute('loi_nhuan/crud',array('action'=>'doanhThuTheoThang'));   
	}

	public function dataDoanhThuTheoThang()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

	    return $doanhThus;
	}


	public function exportDoanhThuTheoQuyAction()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

		$entityManager=$this->getEntityManager();
	    // tham số thức nhất cho hàm exportExcel
	    $objPHPExcel = new PHPExcel();
	    // tham số thức 2 cho hàm exportExcel
	    $fileName='doanh_thu_theo_quy';
	    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)

	    $tieuDe='THỐNG KÊ DOANH THU THEO QUÝ';
	    $fieldName=array(0=>'Thời gian',1=>'Số đơn hàng',2=>'Doanh thu',3=>'Lợi nhuận');

	    $doanhThus=$this->dataDoanhThuTheoQuy();

	    $PI_ExportExcel=$this->ExportExcel();
	    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->data($objPHPExcel, $tieuDe, $fieldName, $doanhThus));

	    return $this->redirect()->toRoute('loi_nhuan/crud',array('action'=>'doanhThuTheoQuy'));   
	}

	public function dataDoanhThuTheoQuy()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

	    return $doanhThus;
	}


	public function exportDoanhThuTheoNamAction()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

		$entityManager=$this->getEntityManager();
	    // tham số thức nhất cho hàm exportExcel
	    $objPHPExcel = new PHPExcel();
	    // tham số thức 2 cho hàm exportExcel
	    $fileName='doanh_thu_theo_nam';
	    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)

	    $tieuDe='THỐNG KÊ DOANH THU THEO NĂM';
	    $fieldName=array(0=>'Thời gian',1=>'Số đơn hàng',2=>'Doanh thu',3=>'Lợi nhuận');

	    $doanhThus=$this->dataDoanhThuTheoNam();

	    $PI_ExportExcel=$this->ExportExcel();
	    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->data($objPHPExcel, $tieuDe, $fieldName, $doanhThus));

	    return $this->redirect()->toRoute('loi_nhuan/crud',array('action'=>'doanhThuTheoNam'));   
	}

	public function dataDoanhThuTheoNam()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

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

	    return $doanhThus;
	}

	public function data($objPHPExcel, $tieuDe, $fieldName, $doanhThus)
	{
		if(!$this->zfcUserAuthentication()->hasIdentity())
	    {
	      return $this->redirect()->toRoute('zfcuser');
	    }

	    $objPHPExcel->getActiveSheet()->setCellValue('A2', $tieuDe);
	    $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
	    $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true);
	    $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	                                  
	   

	    $objPHPExcel->getActiveSheet()->setCellValue('A4', $fieldName[0])
	                                  ->setCellValue('B4', $fieldName[1])
	                                  ->setCellValue('C4', $fieldName[2])
	                                  ->setCellValue('D4', $fieldName[3])
	                                  ->getStyle('A4:D4')->getFont()->setBold(true);
	    $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	    foreach ($doanhThus as $index => $doanhThu) {
			$dong=$index+5;
			$thoiGian=$doanhThu['thoiGian'];
			$soHoaDon=count($doanhThu['chiTietDoanhThus']);
			$tongDoanhThu=0;$tongNguonVon=0;$tongLoiNhuan=0;
			foreach ($doanhThu['chiTietDoanhThus'] as $hoaDons) {
				foreach ($hoaDons->getCtHoaDons() as $ctHoaDon) {
					$tongDoanhThu+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
					$tongNguonVon+=(float)$ctHoaDon->getIdSanPham()->getGiaNhap()*(float)$ctHoaDon->getSoLuong();
				}
			}
			$tongLoiNhuan=$tongDoanhThu-$tongNguonVon;
		    $objPHPExcel->getActiveSheet()->setCellValue('A'.$dong, $thoiGian);
		    $objPHPExcel->getActiveSheet()->setCellValue('B'.$dong, $soHoaDon);
		    $objPHPExcel->getActiveSheet()->setCellValue('C'.$dong, $tongDoanhThu);
		    $objPHPExcel->getActiveSheet()->setCellValue('D'.$dong, $tongLoiNhuan);

	      }
	}

	public function exportDonHangAction()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

		$entityManager=$this->getEntityManager();
	    // tham số thức nhất cho hàm exportExcel
	    $objPHPExcel = new PHPExcel();
	    // tham số thức 2 cho hàm exportExcel
	    $fileName='don_hang';
	    // tham số thức 3 cho hàm exportExcel là dữ liệu (data)

	    $tieuDe='THỐNG KÊ DANH SÁCH ĐƠN HÀNG';
	    $fieldName=array(0=>'Đơn hàng',1=>'Ngày',2=>'Khách hàng',3=>'Thanh toán',4=>'Tổng tiền');

	    $donHangs=$this->dataDonHang();

	    $PI_ExportExcel=$this->ExportExcel();
	    $exportExcel=$PI_ExportExcel->exportExcel($objPHPExcel, $fileName, $this->dataExportDonHang($objPHPExcel, $tieuDe, $fieldName, $donHangs));

	    return $this->redirect()->toRoute('loi_nhuan/crud',array('action'=>'donHang'));   
	}

	public function dataDonHang()
	{

		// kiểm tra đăng nhập==================================================================
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     //====================================================================================

	    $this->layout('layout/giaodien');
	    $entityManager=$this->getEntityManager();

	    $donHangs=$entityManager->getRepository('HangHoa\Entity\HoaDon')->findAll();
	    
	    $taxonomyFunction=$this->TaxonomyFunction();
    	$kenhPhanPhois=$taxonomyFunction->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

	    return $donHangs;
	}
	public function dataExportDonHang($objPHPExcel, $tieuDe, $fieldName, $donHangs)
	{
		if(!$this->zfcUserAuthentication()->hasIdentity())
	    {
	      return $this->redirect()->toRoute('zfcuser');
	    }

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

	    foreach ($donHangs as $index => $donHang) {
			$dong=$index+5;		
			$maDonHang=$donHang->getMaHoaDon();
			$ngay=$donHang->getNgayXuat()->format('d-m-Y');
			$khachHang=$donHang->getIdDoiTac()->getHoTen();
			$thanhToan='';
			$tongTien=0;
			if($donHang->getStatus()==0)
            {
                $thanhToan='Ghi nợ';
            }
            else
            {
            	$thanhToan='Đã thanh toán';
            }
            foreach ($donHang->getCtHoaDons() as $ctHoaDon) {
            	$tongTien+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
            }
			
		    $objPHPExcel->getActiveSheet()->setCellValue('A'.$dong, $maDonHang);
		    $objPHPExcel->getActiveSheet()->setCellValue('B'.$dong, $ngay);
		    $objPHPExcel->getActiveSheet()->setCellValue('C'.$dong, $khachHang);
		    $objPHPExcel->getActiveSheet()->setCellValue('D'.$dong, $thanhToan);
		    $objPHPExcel->getActiveSheet()->setCellValue('E'.$dong, $tongTien);

	      }
	}
 }
?>
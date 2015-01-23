<?php
	namespace Kho\Controller;

	 use Zend\Mvc\Controller\AbstractActionController;
	 use Zend\View\Model\ViewModel;
	 use Kho\Entity\Kho;
	 use Kho\Form\ThemKhoForm;
	 use Kho\Entity\ChietKhau;
	 use HangHoa\Entity\DoiTac;
	 use DateTime;
	 use DateTimeZone;
	
	 class IndexController extends AbstractActionController
	 {
	 	private $entityManager;

	 	public function getEntityManager()
	    {
	    /*// kiểm tra đăng nhập
	     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
	     {
	       return $this->redirect()->toRoute('application');
	     }*/

	      if(!$this->entityManager)
	      {
	       $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
	      }
	      return $this->entityManager;
	    }
	 	
	     public function indexAction()
	     {	
	     	 // kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }
 
	     	$this->layout('layout/giaodien');  
	     	$entityManager=$this->getEntityManager();

	     	$khos=$entityManager->getRepository('Kho\Entity\Kho')->findAll();
	     	return array(
	     		'khos'=>$khos,
	     	);
	     }


	     public function themKhoConAction()
	     {	 
	     	// kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }
	     	$this->layout('layout/giaodien');  
	     	$entityManager=$this->getEntityManager();
	     	$form=new ThemKhoForm($entityManager);
	     	$kho=new Kho();
	     	$form->bind($kho);

	     	$khos=$entityManager->getRepository('Kho\Entity\Kho')->findAll();

	     	$request=$this->getRequest();
	     	if($request->isPost())
	     	{
	     		$form->setData($request->getPost());
	     		if($form->isValid())
	     		{
	     			$query=$entityManager->createQuery('SELECT k FROM Kho\Entity\Kho k WHERE k.tenKho=\''.$kho->getTenKho().'\'');
	     			$kiemTraTonTai=$query->getResult();
	     			if($kiemTraTonTai)
	     			{
	     				return array(
				     		'form'=>$form,
				     		'khos'=>$khos,
				     		'ktKhoTonTai'=>1,
				     	);
	     			}
	     			$entityManager->persist($kho);
	     			$entityManager->flush();
	     			
	     			$taxonomyKenhPhanPhoi=$this->TaxonomyFunction();
    				$kenhPhanPhois=$taxonomyKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug
	     			
    				foreach ($kenhPhanPhois as $kenhPhanPhoi) {
    					if($kenhPhanPhoi['cap']>0)
    					{
    						// tạo chiết khấu cho từng kênh
    						$chietKhau=new ChietKhau();
    						$chietKhau->setIdKho($kho);
    						$kpp=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find($kenhPhanPhoi['termTaxonomyId']);
    						$chietKhau->setIdKenhPhanPhoi($kpp);
    						$chietKhau->setChietKhau(0);
    						$chietKhau->setStatus(0);
    						$entityManager->persist($chietKhau);
    						$entityManager->flush();

    						// nếu cần chiết khấu tăng thì $chietKhau->setStatus(1); tức là: status=0 thì chiết khấu giảm, status =1 chiết khấu tăng
    					}
    				}
    				
    				$dateTime=new DateTime(null,new DateTimeZone('Asia/Ho_Chi_Minh'));
    				//die(var_dump($dateTime));
    				// khi thêm chi nhánh nhớ phải thểm 1 khách hàng bán lẻ
    				$kenhPhanPhoi=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(43);
    				$loaiDoiTac=$entityManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy')->find(45);
    				$banLe=new DoiTac();
    				$banLe->setHoTen('Bán lẻ');
    				$banLe->setDiaChi($kho->getDiaChiKho());
    				$email='banLe_'.$kho->getIdKho().'@gmail.com';
    				$banLe->setEmail($email);
    				$banLe->setKho($kho->getIdKho());
    				$banLe->setIdKenhPhanPhoi($kenhPhanPhoi);
    				$banLe->setNgayDangKy($dateTime);
    				$banLe->setLoaiDoiTac($loaiDoiTac);
    				$entityManager->persist($banLe);
    				$entityManager->flush();

	     			$this->flashMessenger()->addSuccessMessage('Thêm chi nhánh mới thành công');
	     			return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     		}	     		
	     	}

	     	
	     	return array(
	     		'form'=>$form,
	     		'khos'=>$khos,
	     		'ktKhoTonTai'=>0,
	     	);
	     }


	     public function suaChiNhanhAction()
	     {	 
	     	// kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }

		     // lấy id
	         $id = (int) $this->params()->fromRoute('id', 0);
		     if (!$id) {
		           return $this->redirect()->toRoute('kho/crud');
		     } 
		     // tạo layout 
	     	$this->layout('layout/giaodien');  
	     	$entityManager=$this->getEntityManager();

	     	// tạo form
	     	$form=new ThemKhoForm($entityManager);
	     	$kho=$entityManager->getRepository('Kho\Entity\Kho')->find($id);


	     	if(!$kho)
	     	{
	     		$this->flashMessenger()->addErrorMessage('Không tìm thấy chi nhánh này! Vui lòng kiểm tra lại!');
	     		return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     	}
	     	$idKhoTruoc=$kho->getIdKho();
	     	$form->bind($kho);
	     	

	     	$request=$this->getRequest();
	     	if($request->isPost())
	     	{
	     		$form->setData($request->getPost());
	     		if($form->isValid())
	     		{
	     			$query=$entityManager->createQuery('SELECT k FROM Kho\Entity\Kho k WHERE k.tenKho=\''.$kho->getTenKho().'\'');
	     			$kiemTraTonTai=$query->getResult();	     			
	     			if(!$kiemTraTonTai||($kiemTraTonTai&&$kiemTraTonTai[0]->getIdKho()==$idKhoTruoc))
	     			{
					    $query=$entityManager->createQuery('SELECT dt FROM HangHoa\Entity\DoiTac dt WHERE dt.hoTen= :hoTen and dt.kho= :idKho and dt.email= :email');
					    $query->setParameter('hoTen','Bán lẻ');
					    $query->setParameter('idKho',$id);
					    $email='banLe_'.$id.'@gmail.com';
					    $query->setParameter('email',$email);
					    $banLe=$query->getSingleResult();
					    $banLe->setDiaChi($kho->getDiaChiKho());

					    $entityManager->flush();

		     			$this->flashMessenger()->addSuccessMessage('Cập nhật thành công');
		     			return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     			}
	     			else
	     			{
	     				return array(
				     		'form'=>$form,
				     		'kho'=>$kho,
				     		'ktKhoTonTai'=>1,
				     	);
	     			}	     			
	     		}
	     		else{     			

	     			$this->flashMessenger()->addErrorMessage('Cập nhật thất bại');
	     			return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     		}	     		
	     	}

	     	return array(
	     		'form'=>$form,
	     		'kho'=>$kho,
	     		'ktKhoTonTai'=>0,
	     	);
	     }


	     public function xemKhoAction()
	     {
	     	// kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }

		    $id = (int) $this->params()->fromRoute('id', 0);
		    if (!$id) 
		    {
		        return $this->redirect()->toRoute('hang_hoa/crud');
		    }  
	     	$this->layout('layout/giaodien');
	     	$entityManager=$this->getEntityManager();

	     	$admin=$entityManager->getRepository('Application\Entity\SystemUser')->find(1);
	     	if($admin)
	     	{
	     		$admin->setKho($id);
	     		$entityManager->flush();
	     		
	     	}
	     	return $this->redirect()->toRoute('hang_hoa/crud');


	     }

	    public function chinhSachBanHangAction()
	    {
	      // kiểm tra đăng nhập
	        if(!$this->zfcUserAuthentication()->hasIdentity())
	        {
	        return $this->redirect()->toRoute('application');
	        }
	        $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
	        $this->layout('layout/giaodien');
	        $entityManager=$this->getEntityManager();
	        

	       $taxonomyKenhPhanPhoi=$this->TaxonomyFunction();
	       $kenhPhanPhois=$taxonomyKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug

	       $entityManager=$this->getEntityManager();
	       $query=$entityManager->createQuery('SELECT ck FROM Kho\Entity\ChietKhau ck WHERE ck.idKho='.$idKho.' and ck.status=0');
	       $chietKhaus=$query->getResult();
	       
	       $request=$this->getRequest();
	       if($request->isPost())
	       {
	         $post=$request->getPost();
	         foreach ($chietKhaus as $chietKhau) {
	            
	            $this->suaChietKhau($chietKhau->getIdChietKhau(),$post[$chietKhau->getIdChietKhau()]);
	         }
	         $this->flashMessenger()->addSuccessMessage('Cập nhập chính sách bán hàng thành công');
	         return $this->redirect()->toRoute('kho/crud',array('action'=>'chinh-sach-ban-hang'));
	       }
	       return array(        
	         'kenhPhanPhois'=>$kenhPhanPhois,
	         'chietKhaus'=>$chietKhaus,
	       );
	    }

	    public function suaChietKhau($id,$value)
	    {
	        if($id&&$value)
	        {
	            // kiểm tra đăng nhập
	            if(!$this->zfcUserAuthentication()->hasIdentity())
	            {
	            return $this->redirect()->toRoute('application');
	            }
	            $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
	            $this->layout('layout/giaodien');
	            $entityManager=$this->getEntityManager();

	            $chietKhau=$entityManager->getRepository('Kho\Entity\ChietKhau')->find($id);
	            $chietKhau->setChietKhau($value);

	            $idKenhPhanPhoi=$chietKhau->getIdKenhPhanPhoi()->getTermTaxonomyId();

	            $query=$entityManager->createQuery('SELECT gx FROM HangHoa\Entity\GiaXuat gx WHERE gx.kho='.$idKho.' and gx.idKenhPhanPhoi='.$idKenhPhanPhoi);
	            $giaXuats=$query->getResult();

	            foreach ($giaXuats as $giaXuat) {
	                $idSanPham=$giaXuat->getIdSanPham();
	                $sanPham=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($idSanPham);
	                if($sanPham->getLoaiGia()==1)// là sản phẩm có giá bìa
	                {
	                    /**
	                     * @value là chiết khấu
	                     */
	                    $this->suaGiaXuatSanPhamCoGiaBia($sanPham,$giaXuat,$value);
	                }
	                else// là sản phẩm có giá nhập
	                {
	                    $this->suaGiaXuatSanPhamKhongCoGiaBia($sanPham,$giaXuat,$value);
	                }
	            }

	            $entityManager->flush();
	        }
	    }

	    public function suaGiaXuatSanPhamCoGiaBia($sanPham,$giaXuat,$chietKhau)
	    {
	        $entityManager=$this->getEntityManager();
	        if($giaXuat&&$chietKhau&&$sanPham)
	        {
	            $loiNhuan=(float)(((float)($sanPham->getGiaBia())*(float)$chietKhau)/100);
	            $gx=(float)($sanPham->getGiaBia()-$loiNhuan);
	            $giaXuat->setGiaXuat($gx);
	            $entityManager->flush();
	        }

	    }
	    public function suaGiaXuatSanPhamKhongCoGiaBia($sanPham,$giaXuat,$chietKhau)
	    {
	        $entityManager=$this->getEntityManager();
	        if($giaXuat&&$chietKhau)
	        {
	            $loiNhuan=(float)(((float)($sanPham->getGiaNhap())*(float)$chietKhau)/100);
	            $gx=(float)($sanPham->getGiaNhap()+$loiNhuan);
	            $giaXuat->setGiaXuat($gx);
	            $entityManager->flush();
	        }

	    }

	        
}
?>
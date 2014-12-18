<?php namespace S3UTaxonomy\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use S3UTaxonomy\Entity\ZfTerm;
 use S3UTaxonomy\Entity\ZfTermTaxonomy;
 use Zend\ServiceManager\ServiceManager;
 use S3UTaxonomy\Form\ZfTermTaxonomyFieldset;
 use S3UTaxonomy\Form\ZfTermFieldset;
 use S3UTaxonomy\Form\CreateTaxonomyForm;
 use S3UTaxonomy\Form\CreateTermTaxonomyForm;
 use S3UTaxonomy\Form\UpdateTermForm; 
 use S3UTaxonomy\Form\UploadForm;
 use Zend\File\Transfer\Adapter\Http;
 use Zend\Http\PhpEnvironment\Request;

 use DateTime;



 use BaconStringUtils\Slugifier;
 use BaconStringUtils\UniDecoder;
 
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
        $entityManager=$this->getEntityManager();
    
        $objectManager= $this->getEntityManager();
        $repository = $objectManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy');
        $queryBuilder = $repository->createQueryBuilder('tt');
        $queryBuilder->add('where','tt.parent is NULL');
        $query = $queryBuilder->getQuery();
        $termTaxonomys = $query->execute();

        // kiểm thử các hàm trong Plugin/TaxonomyFunction
 		$taxonomyFunction=$this->TaxonomyFunction();
        // $list=$taxonomyFunction->getListTaxonomy();
        //die(var_dump($list));

        //$idTermTaxonomy=$taxonomyFunction->getIdTermTaxonomy('dm1', 'a a', 'a-a');
        //die(var_dump($idTermTaxonomy));

        //$listChildTermTaxonomysDanhMuc=$taxonomyFunction->getListChildTaxonomy('danh-muc');// đưa vào taxonomy dạng slug
        //var_dump($listChildTermTaxonomysDanhMuc);

         //$listChildTermTaxonomys=$taxonomyFunction->getListChildTaxonomy('khu-vuc');// đưa vào taxonomy dạng slug
        //die(var_dump($listChildTermTaxonomys));

        // $listChildTermTaxonomyOrderById=$taxonomyFunction->getListChildTaxonomyOrderById('dm1');
        // die(var_dump($listChildTermTaxonomyOrderById));

        
        
        // $childTermTaxonomys=$taxonomyFunction->getChildTaxonomy('dm1',16);
        // die(var_dump($childTermTaxonomys));

        // $listChildTermTaxonomyCondition=$taxonomyFunction->getListChildTaxonomyCondition('dm1',array(16,15,18));
        // die(var_dump($listChildTermTaxonomyCondition));

 		return array(
            'termTaxonomys'=>$termTaxonomys,
        );
 	}

 	public function addAction()
 	{

        $objectManager=$this->getEntityManager();
        $zfTermTaxonomy=new ZfTermTaxonomy();
        $form= new CreateTermTaxonomyForm($objectManager);
        $form->bind($zfTermTaxonomy);
        //die(var_dump($form));
        
        $request = $this->getRequest();
        if ($request->isPost())
        { 
            $form->setData($request->getPost());
            //var_dump($zfTermTaxonomy);
                        
            if ($form->isValid()) {

                 $term=$objectManager->getRepository('S3UTaxonomy\Entity\ZfTerm'); 
                 $queryBuilder = $term->createQueryBuilder('t');             
                 $queryBuilder->add('where','t.name=\''.$zfTermTaxonomy->getTermId()->getName().'\'');       
                 $query = $queryBuilder->getQuery();        
                 $terms = $query->execute(); 
                 if(!$terms)
                 {
                    $zfTermTaxonomy->setTaxonomy($zfTermTaxonomy->getTermId()->getSlug());
                    $zfTermTaxonomy->setDescription('Taxonomy');
                    $zfTermTaxonomy->setParent(NULL);
                    $zfTermTaxonomy->getTermId()->setTermGroup(0);
                    //die(var_dump($zfTermTaxonomy));                
                    $objectManager->persist($zfTermTaxonomy);
                    $objectManager->flush();
                    return $this->redirect()->toRoute('s3u_taxonomy');
                 } 
                 else
                 {
                    return array(
                      'form' => $form,
                      'coKiemTraTonTai'=>1,
                    );
                 }           

            }
            
        }
        
        return array(
          'form' => $form,  
          'coKiemTraTonTai'=>0,         
        );
        
 	}

 	public function editAction()
 	{        
        $entityManager=$this->getEntityManager();
        $objectManager=$this->getEntityManager();
        $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('s3u_taxonomy', array(
                 'action' => 'add'
             ));
         }
         //die(var_dump($id));
         $form= new UpdateTermForm($objectManager);         
         $repository = $objectManager->getRepository('S3UTaxonomy\Entity\ZfTerm')->find($id);
         // lấy slug cũ.        
         $slug=$repository->getSlug();
         $oldName=$repository->getName();

         $form->bind($repository);
         if ($this->request->isPost()) {
             $form->setData($this->request->getPost());

             if ($form->isValid()) {
                 $name=$repository->getName();
                 $newSlug=$repository->getSlug();
                 
                 if($name==$oldName)
                 {
                    return $this->redirect()->toRoute('s3u_taxonomy', array(
                     'action' => 'index'
                     ));
                 } 
                 /*var_dump($name);
                 die(var_dump($oldName));*/
                 
                 $term=$objectManager->getRepository('S3UTaxonomy\Entity\ZfTerm'); 
                 $queryBuilder = $term->createQueryBuilder('t');             
                 $queryBuilder->add('where','t.name=\''.$name.'\'');       
                 $query = $queryBuilder->getQuery();        
                 $terms = $query->execute();  
                 if(!$terms)
                 {
                    // Save the changes
                     $objectManager->flush();
                    // lấy trong bảng termTaxonomy những thằng có taxonomy=slug
                     $termTaxonomysys=$objectManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy'); 
                     $queryBuilder = $termTaxonomysys->createQueryBuilder('tt');             
                     $queryBuilder->add('where','tt.taxonomy=\''.$slug.'\'');       
                     $query = $queryBuilder->getQuery();        
                     $termTaxonomys = $query->execute();  
                     foreach ($termTaxonomys as $termtaxonomy) {
                         $termtaxonomy->setTaxonomy($newSlug);
                         $objectManager->flush();

                     }
                      return $this->redirect()->toRoute('s3u_taxonomy', array(
                     'action' => 'index'
                     ));
                 }
                 else
                 {
                    return array(
                        'form' => $form,
                        'id'=>$id,
                        'coKiemTraTonTai'=>1,
                     );

                 }
                
            }
         }

         return array(
            'form' => $form,
            'id'=>$id,
            'coKiemTraTonTai'=>0,
         );
 	}

 	public function deleteAction()
 	{        
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('s3u_taxonomy');
        }

        $objectManager= $this->getEntityManager();        
        
        $term = $objectManager->getRepository('S3UTaxonomy\Entity\ZfTerm')->find($id);
        $taxonomy=$term->getSlug();
        //$form = new ZfTermTaxonomyForm($objectManager);

        $repository = $objectManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy');
        $queryBuilder = $repository->createQueryBuilder('tt');
        $queryBuilder->add('where','tt.taxonomy =\''.$taxonomy.'\'');
        $query = $queryBuilder->getQuery(); 
        $termTaxonomys = $query->execute();        
        
        if($termTaxonomys)
        {
            foreach ($termTaxonomys as $termTaxonomy) {
                //$termTaxonomy = new ZfTermTaxonomyForm();
                $entityManager=$this->getEntityManager();
                $termTaxonomy->setParent(NULL);
                $entityManager->merge($termTaxonomy);
                $entityManager->flush();
            }
            foreach ($termTaxonomys as $termTaxonomy) {

                $termId=$termTaxonomy->getTermId();
                $objectManager->remove($termTaxonomy);
                $objectManager->flush();
                $termId = $objectManager->getRepository('S3UTaxonomy\Entity\ZfTerm')->find($termId);                
                // kiểm tra có ai xài chung term_id của thằng này nữa không nếu không thì xóa ở bảng term luôn
                //1. kiểm tra xem có ai trong bảng termtaxonomy xài thằng này ko
                $repository = $objectManager->getRepository('S3UTaxonomy\Entity\ZfTermTaxonomy');
                $queryBuilder = $repository->createQueryBuilder('tt');
                $queryBuilder->add('where','tt.termId ='.$termId->getTermId());
                $query = $queryBuilder->getQuery(); 
                $kiemTraTermTaxonomy = $query->execute();                
                if(!$kiemTraTermTaxonomy)
                {
                    //2. lệnh xóa bỏ trong bảng term
                    //$deleteTerm = $objectManager->getRepository('S3UTaxonomy\Entity\ZfTerm')->find($termId);                    
                    $objectManager->remove($termId);
                    $objectManager->flush();                     
                }                
            } 
        }                       
        return $this->redirect()->toRoute('s3u_taxonomy');        
 	}

    public function chietKhauAction()
    {
       $this->layout('layout/giaodien');

        $idKho=1;
        if($this->zfcUserAuthentication()->hasIdentity())
        { 
          $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
        }
        else
        {
            return $this->redirect()->toRoute('hang_hoa/crud');
        }

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
         $this->flashMessenger()->addSuccessMessage('Cập nhập chiết khấu thành công');
         return $this->redirect()->toRoute('s3u_taxonomy/taxonomys',array('action'=>'chietKhau'));
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
            $idKho=1;
            if($this->zfcUserAuthentication()->hasIdentity())
            { 
              $idKho=$this->zfcUserAuthentication()->getIdentity()->getKho();
            }
            else
            {
                return $this->redirect()->toRoute('hang_hoa/crud');
            }
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
<?php namespace Barcode\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager; 
 use Barcode\Entity\Barcode; 
 
 use Barcode\Form\CreateSanPhamForm;
  
 use Barcode\Form\FileForm;
 use Barcode\Form\BarcodeForm;
 use Zend\Validator\File\Size;

 use Zend\Stdlib\AbstractOptions;
 
 use S3UTaxonomy\Form\CreateTermTaxonomyForm; 
  
 use Zend\Barcode\Renderer;
 use Zend\Barcode\Barcode as Bc; 
 
 class IndexController extends AbstractActionController
 {
 	private $entityManager;
  
  public function getEntityManager()
  {
     if(!$this->zfcUserAuthentication()->hasIdentity())
     {
       return $this->redirect()->toRoute('application');
     }
     if(!$this->entityManager)
     {
      $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
     }
     return $this->entityManager;
  }  

  public function indexAction()
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();
        
    $repository = $entityManager->getRepository('Barcode\Entity\Barcode');     
    $queryBuilder = $repository->createQueryBuilder('bc');
    $queryBuilder->add('where','bc.idBarcode NOT IN (0)');
    $query = $queryBuilder->getQuery(); 
    $barcode = $query->execute();
    return array(
      'barcodes'=>$barcode,
    );
  }

  public function suaBarcodeAction()
  {
    if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
    {
      return $this->redirect()->toRoute('application');
    }
     // lấy id
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) 
    {
      return $this->redirect()->toRoute('barcode/crud');
    }      
    $this->layout('layout/giaodien');  
    $entityManager=$this->getEntityManager();

    $form=new BarcodeForm($entityManager);
    $barcode=$entityManager->getRepository('Barcode\Entity\Barcode')->find($id);
    if(!$barcode)
    {
      $this->flashMessenger()->addErrorMessage('Không tìm thấy! Vui lòng kiểm tra lại!');
      return $this->redirect()->toRoute('barcode/crud',array('action'=>'index'));
    }
    $idBarcodeTruoc=$barcode->getIdBarcode();
    $form->bind($barcode);
    $request=$this->getRequest();
    if($request->isPost())
    {
      $form->setData($request->getPost());      
      if($form->isValid())
      {        
        $length=$request->getPost()->get('length');
        $query=$entityManager->createQuery('SELECT b FROM Barcode\Entity\Barcode b WHERE b.tenBarcode=\''.trim($barcode->getTenBarcode()).'\'');
        $kiemTraTonTai=$query->getResult();
        if(!$kiemTraTonTai||($kiemTraTonTai&&$kiemTraTonTai[0]->getIdBarcode()==$idBarcodeTruoc))
        {
          $barcode->setLength($length);
          $entityManager->flush();
          $this->flashMessenger()->addSuccessMessage('Cập nhật thành công');
          return $this->redirect()->toRoute('barcode/crud',array('action'=>'index'));
        }
        else
        {
          return array(
            'form'=>$form,
            'barcode'=>$barcode,
            'ktTonTai'=>1,
          );
        }
      }
      else
      {
        $this->flashMessenger()->addErrorMessage('Cập nhật thất bại');
        return $this->redirect()->toRoute('barcode/crud',array('action'=>'index'));
      } 
    }
    return array(
      'form'=>$form,
      'barcode'=>$barcode,
      'ktTonTai'=>0,
    );
  }

  public function chonSuDungBarcodeAction()
  {
    if(!$this->zfcUserAuthentication()->hasIdentity())
    {
      return $this->redirect()->toRoute('application');
    }

    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) 
    {
      return $this->redirect()->toRoute('barcode/crud');
    }
    $this->layout('layout/giaodien');
    $entityManager=$this->getEntityManager();
        
    $barcodes = $entityManager->getRepository('Barcode\Entity\Barcode')->findAll();
    foreach ($barcodes as $barcode) {      
      if($barcode->getIdBarcode()==$id)
      {        
        $barcode->setState(1);        
      }
      else
      {
        $barcode->setState(0);        
      }
      $entityManager->flush();
    }
    $this->flashMessenger()->addSuccessMessage('Thay đổi lựa chọn thành công');
    return $this->redirect()->toRoute('barcode/crud',array('action'=>'index'));
  }

  public function inBarcodeAction()
  {
    if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
    {
      return $this->redirect()->toRoute('application');
    }
     // lấy id
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) 
    {
      return $this->redirect()->toRoute('barcode/crud');
    }      
    $this->layout('layout/giaodien');  
    $entityManager=$this->getEntityManager();

    $sanPham=$entityManager->getRepository('HangHoa\Entity\SanPham')->find($id);
    if(!$sanPham)
    {
      $this->flashMessenger()->addErrorMessage('Không tìm thấy sản phẩm này! Vui lòng kiểm tra lại!');
      return $this->redirect()->toRoute('hang_hoa/crud',array('action'=>'hang-hoa'));
    }
    $request=$this->getRequest();
    if($request->isPost())
    {      
      $soLuong=$request->getPost()->get('soLuong');
      $maVach=$sanPham->getMaVach();
      $loaiMaVach=$sanPham->getIdBarcode()->getTenBarcode();

      $barcodeOptions = array(          
          'text' =>'12345678', 
          'barHeight'=> 25,          
          //'withBorder'=>true,
          'stretchText'=>true,
          'barThickWidth'=>3,
          'withQuietZones'=>false,
      );

      $rendererOptions = array();      
      $imageResource = Bc::draw('code39', 'image', $barcodeOptions, $rendererOptions);
      imagejpeg($imageResource, './public/img/barcode.png', 100);
      imagedestroy($imageResource);

      return array(
        'soLuong'=>$soLuong,
        'sanPham'=>$sanPham,        
        'ktIn'=>1,
      );
    }
    return array(
      'sanPham'=>$sanPham,
      'ktIn'=>0,
    );
  }
}
?>
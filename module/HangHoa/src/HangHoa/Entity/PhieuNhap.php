<?php
	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Datetime;

	/**
	* @ORM\Entity
	* @ORM\Table(name="phieu_nhap")
	* @ORM\HasLifecycleCallbacks
	*/
	class PhieuNhap {
		

		/**
		* @ORM\Column(name="id_phieu_nhap",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idPhieuNhap;

		/**
		* @ORM\Column(name="ma_phieu_nhap", type="text")
		*/
		private $maPhieuNhap;


		/**
		* @ORM\Column(name="ngay_nhap", type="date")
		*/
		private $ngayNhap;

		/**
		 * @ORM\Column(type="integer")
		 */

		// status =0: là chưa thanh toán
		// status !=0: là đã thanh toán

		private $status; 


		/**
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\DoiTac", cascade={"persist"})
		* @ORM\JoinColumn(name="id_doi_tac", referencedColumnName="id_doi_tac")
		*/
		private $idDoiTac;

		/**
		 * @ORM\OneToMany(targetEntity="HangHoa\Entity\CTPhieuNhap", mappedBy="id_phieu_nhap", cascade={"persist"})
		 */
		private $ctPhieuNhaps;

		/**
		* @ORM\ManyToOne(targetEntity="Application\Entity\SystemUser", cascade={"persist"})
		* @ORM\JoinColumn(name="id_user_nv", referencedColumnName="user_id")
		*/
		private $idUserNv;

		/**
		 * @ORM\PrePersist 
		 */
		public function onPrePersist(){
	    	$this->ngayNhap = new DateTime('now');
	    	$this->maPhieuNhap = $this->ngayNhap->format('His');    	
		}

		public function __construct(){
			$this->ctPhieuNhaps = new ArrayCollection();
		}

		public function getCtPhieuNhaps(){
			return $this->ctPhieuNhaps->toArray();
		}

		public function addCtPhieuNhaps($ctPhieuNhaps){
			foreach($ctPhieuNhaps as $ctPhieuNhap){
				$ctPhieuNhap->setIdPhieuNhap($this);
				$this->ctPhieuNhaps->add($ctPhieuNhap);
			}
		}

		public function removeCtPhieuNhaps($ctPhieuNhaps){
			foreach($ctPhieuNhaps as $ctPhieuNhap){
				$ctPhieuNhap->setIdPhieuNhap(null);
				$this->ctPhieuNhaps->removeElement($ctPhieuNhap);
			}
		}

		public function setIdPhieuNhap($idPhieuNhap)
		{
			$this->idPhieuNhap=$idPhieuNhap;
		}
		public function getIdPhieuNhap()
		{
			return $this->idPhieuNhap;
		}


		public function setMaPhieuNhap($maPhieuNhap)
		{
			$this->maPhieuNhap=$maPhieuNhap;
		}
		public function getMaPhieuNhap()
		{
			return $this->maPhieuNhap;
		}


		public function setNgayNhap($ngayNhap)
		{
			$this->ngayNhap=$ngayNhap;
		}
		public function getNgayNhap()
		{
			return $this->ngayNhap;
		}


		public function setIdDoiTac($idDoiTac)
		{
			$this->idDoiTac=$idDoiTac;
		}
		public function getIdDoiTac()
		{
			return $this->idDoiTac;
		}


		public function setIdUserNv($idUserNv)
		{
			$this->idUserNv=$idUserNv;
		}
		public function getIdUserNv()
		{
			return $this->idUserNv;
		}	

		// mặt đinh status=0;
		// nếu setStatus();// có truyền tham số thì status bằng tham số đã truyền

		public function setStatus($status=null)
		{
			if($status==null)
				$status=0;
			$this->status=$status;
		}	

		public function getStatus()
		{
			return $this->status;
		}	
	}
	

?>
<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Datetime;


	/**
	* @ORM\Entity
	* @ORM\Table(name="hoa_don")
	* @ORM\HasLifecycleCallbacks
	*/
	class HoaDon {
		

		/**
		* @ORM\Column(name="id_hoa_don",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idHoaDon;


		/**
		* @ORM\Column(name="ma_hoa_don", type="text")
		*/
		private $maHoaDon;


		/**
		* @ORM\Column(name="ngay_xuat", type="date")
		*/
		private $ngayXuat;


		/**
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\DoiTac", cascade={"persist"})
		* @ORM\JoinColumn(name="id_doi_tac", referencedColumnName="id_doi_tac")
		*/
		private $idDoiTac;



		/**
		* @ORM\ManyToOne(targetEntity="Application\Entity\SystemUser", cascade={"persist"})
		* @ORM\JoinColumn(name="id_user_nv", referencedColumnName="user_id")
		*/
		private $idUserNv;

		/**
		 * @ORM\OneToMany(targetEntity="HangHoa\Entity\CTHoaDon", mappedBy="id_hoa_don", cascade={"persist"})
		 */
		private $ctHoaDons;

		/**
		 * @ORM\PrePersist 
		 */
		public function onPrePersist(){
	    	$this->ngayXuat = new DateTime('now');
	    //	$this->idUserNv = 1; //tam thoi
	    	$this->maHoaDon = $this->ngayXuat->format('His');
		}

		public function __construct(){
			$this->ctHoaDons = new ArrayCollection();
		}

		public function getCtHoaDons(){
			return $this->ctHoaDons->toArray();
		}

		public function addCtHoaDons($ctHoaDons){
			foreach($ctHoaDons as $ctHoaDon){
				$ctHoaDon->setIdHoaDon($this);
				$this->ctHoaDons->add($ctHoaDon);
			}
		}

		public function removeCtHoaDons($ctHoaDons){
			foreach($ctHoaDons as $ctHoaDon){
				$ctHoaDon->setIdHoaDon(null);
				$this->ctHoaDons->removeElement($ctHoaDon);
			}
		}

		public function setIdHoaDon($idHoaDon)
		{
			$this->idHoaDon=$idHoaDon;
		}
		public function getIdHoaDon()
		{
			return $this->idHoaDon;
		}


		public function setMaHoaDon($maHoaDon)
		{
			$this->maHoaDon=$maHoaDon;
		}
		public function getMaHoaDon()
		{
			return $this->maHoaDon;
		}


		public function setNgayXuat($ngayXuat)
		{
			$this->ngayXuat=$ngayXuat;
		}
		public function getNgayXuat()
		{
			return $this->ngayXuat;
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
	}
	

?>
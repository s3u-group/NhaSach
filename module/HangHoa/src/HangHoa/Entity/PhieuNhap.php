<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="phieu_nhap")
	*/
	class PhieuNhap implements UserInterface, ProviderInterface {
		

		/**
		* @ORM\Column(name="id_phieu_nhap",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idPhieuNhap;


		/**
		* @ORM\Column(name="ma_phieu_nhap", type="char", length=6)
		*/
		private $maPhieuNhap;


		/**
		* @ORM\Column(name="ngay_nhap", type="date")
		*/
		private $ngayNhap;


		/**
		* @ManyToOne(targetEntity="HangHoa\Entity\DoiTac")
		* @JoinColumn(name="id_doi_tac", referencedColumnName="id_doi_tac")
		*/
		private $idDoiTac;



		/**
		* @ManyToOne(targetEntity="Application\Entity\User")
		* @JoinColumn(name="id_user_nv", referencedColumnName="user_id")
		*/
		private $idUserNv;




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
	}
	

?>
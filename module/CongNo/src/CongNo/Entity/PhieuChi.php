<?php

	namespace CongNo\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="phieu_chi")
	*/
	class PhieuChi {
		

		/**
		* @ORM\Column(name="id_phieu_chi",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idPhieuChi;


		/**
		* @ORM\OneToOne(targetEntity="CongNo\Entity\CongNo")
		* @ORM\JoinColumn(name="id_cong_no", referencedColumnName="id_cong_no")
		*/
		private $idCongNo;


		/**
		* @ORM\ManyToOne(targetEntity="Application\Entity\SystemUser")
		* @ORM\JoinColumn(name="id_user_nv", referencedColumnName="user_id")
		*/
		private $idUserNv;


		/**
		* @ORM\Column(name="ly_do", type="text")
		*/
		private $lyDo;

		/**
		* @ORM\Column(name="so_tien",type="float")
		*/
		private $soTien;

		/**
		* @ORM\Column(name="ngay_thanh_toan", type="date")
		*/
		private $ngayThanhToan;



		public function setIdPhieuChi($idPhieuChi)
		{
			$this->idPhieuChi=$idPhieuChi;
		}
		public function getIdPhieuChi()
		{
			return $this->idPhieuChi;
		}


		public function setIdCongNo($idCongNo)
		{
			$this->idCongNo=$idCongNo;
		}
		public function getIdCongNo()
		{
			return $this->idCongNo;
		}


		public function setIdUserNv($idUserNv)
		{
			$this->idUserNv=$idUserNv;
		}
		public function getIdUserNv()
		{
			return $this->idUserNv;
		}


		public function setLyDo($lyDo)
		{
			$this->lyDo=$lyDo;
		}
		public function getLyDo()
		{
			return $this->lyDo;
		}



		public function setSoTien($soTien)
		{
			$this->soTien=$soTien;
		}

		public function getSoTien()
		{
			return $this->soTien;
		}



		public function setNgayThanhToan($ngayThanhToan)
		{
			$this->ngayThanhToan=$ngayThanhToan;
		}

		public function getNgayThanhToan()
		{
			return $this->ngayThanhToan;
		}
	}
	

?>
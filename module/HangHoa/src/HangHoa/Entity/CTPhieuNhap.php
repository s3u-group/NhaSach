<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="ct_phieu_nhap")
	*/
	class CTPhieuNhap {
		

		/**
		* @ORM\Column(name="id_ct_phieu_nhap",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idCTPhieuNhap;



		/**
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\PhieuNhap", inversedBy="ctPhieuNhaps")
		* @ORM\JoinColumn(name="id_phieu_nhap", referencedColumnName="id_phieu_nhap")
		*/
		private $idPhieuNhap;



		/**
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\SanPham", inversedBy="ctPhieuNhaps")
		* @ORM\JoinColumn(name="id_san_pham", referencedColumnName="id_san_pham")
		*/
		private $idSanPham;



		/**
		* @ORM\Column(name="gia_nhap", type="float")
		*/
		private $giaNhap;


		/**
		* @ORM\Column(name="so_luong", type="integer")
		*/
		private $soLuong;


		public function setIdCTPhieuNhap($idCTPhieuNhap)
		{
			$this->idCTPhieuNhap=$idCTPhieuNhap;
		}
		public function getIdCTPhieuNhap()
		{
			return $this->idCTPhieuNhap;
		}


		public function setIdPhieuNhap($idPhieuNhap)
		{
			$this->idPhieuNhap=$idPhieuNhap;
		}
		public function getIdPhieuNhap()
		{
			return $this->idPhieuNhap;
		}


		public function setIdSanPham($idSanPham)
		{
			$this->idSanPham=$idSanPham;
		}
		public function getIdSanPham()
		{
			return $this->idSanPham;
		}


		public function setGiaNhap($giaNhap)
		{
			$this->giaNhap=$giaNhap;
		}
		public function getGiaNhap()
		{
			return $this->giaNhap;
		}


		public function setSoLuong($soLuong)
		{
			$this->soLuong=$soLuong;
		}
		public function getSoLuong()
		{
			return $this->soLuong;
		}		
	}
	

?>
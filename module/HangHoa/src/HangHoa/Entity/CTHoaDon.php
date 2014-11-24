<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="ct_hoa_don")
	*/
	class CTHoaDon {
		

		/**
		* @ORM\Column(name="id_ct_hoa_don",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idCTHoaDon;



		/**
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\HoaDon", inversedBy="ctHoaDons")
		* @ORM\JoinColumn(name="id_hoa_don", referencedColumnName="id_hoa_don")
		*/
		private $idHoaDon;



		/**
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\SanPham", inversedBy="ctHoaDons")
		* @ORM\JoinColumn(name="id_san_pham", referencedColumnName="id_san_pham")
		*/
		private $idSanPham;



		/**
		* @ORM\Column(name="gia", type="float")
		*/
		private $gia;


		/**
		* @ORM\Column(name="so_luong", type="integer")
		*/
		private $soLuong;


		public function setIdCTHoaDon($idCTHoaDon)
		{
			$this->idCTHoaDon=$idCTHoaDon;
		}
		public function getIdCTHoaDon()
		{
			return $this->idCTHoaDon;
		}


		public function setIdHoaDon($idHoaDon)
		{
			$this->idHoaDon=$idHoaDon;
		}
		public function getIdHoaDon()
		{
			return $this->idHoaDon;
		}



		public function setIdSanPham($idSanPham)
		{
			$this->idSanPham=$idSanPham;
		}
		public function getIdSanPham()
		{
			return $this->idSanPham;
		}


		public function setGia($gia)
		{
			$this->gia=$gia;
		}
		public function getGia()
		{
			return $this->gia;
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
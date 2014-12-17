<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="gia_xuat")
	*/
	class GiaXuat {
		

		/**
		* @ORM\Column(name="id_gia_xuat",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		
		private $idGiaXuat;
		/**
		* @ORM\Column(name="id_san_pham")
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\SanPham")
		* @ORM\JoinColumn(name="id_san_pham", referencedColumnName="id_san_pham")
		*/
		private $idSanPham;



		/**
		* @ORM\Column(name="gia_xuat", type="float")
		*/
		private $giaXuat;

		/**
		* @ORM\Column(type="integer")
		*/
		private $kho;

		/**
		* @ORM\Column(name="id_kenh_phan_phoi")
		* @ORM\ManyToOne(targetEntity="S3UTaxonomy\Entity\ZfTermTaxonomy")
		* @ORM\JoinColumn(name="id_kenh_phan_phoi", referencedColumnName="term_taxonomy_id")
		*/
		private $idKenhPhanPhoi;

		public function setIdGiaXuat($idGiaXuat)
		{
			$this->idGiaXuat=$idGiaXuat;
		}
		public function getIdGiaXuat()
		{
			return $this->idGiaXuat;
		}



		public function setIdSanPham($idSanPham)
		{
			$this->idSanPham=$idSanPham;
		}
		public function getIdSanPham()
		{
			return $this->idSanPham;
		}


		public function setGiaXuat($giaXuat)
		{
			$this->giaXuat=$giaXuat;
		}
		public function getGiaXuat()
		{
			return $this->giaXuat;
		}


		public function setIdKenhPhanPhoi($idKenhPhanPhoi)
		{
			$this->idKenhPhanPhoi=$idKenhPhanPhoi;
		}
		public function getIdKenhPhanPhoi()
		{
			return $this->idKenhPhanPhoi;
		}	

		public function setKho($kho)
		{
			$this->kho=$kho;
		}	
		public function getKho()
		{
			return $this->kho;
		}
	}
	

?>
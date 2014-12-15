<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="san_pham")
	*/
	class SanPham {
		

		/**
		* @ORM\Column(name="id_san_pham",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idSanPham;

		/**
		* @ORM\Column(name="ma_san_pham",type="text", length=255)
		*/
		private $maSanPham;


		/**
		* @ORM\Column(name="ten_san_pham")
		*/
		private $tenSanPham;


		/**
		* @ORM\Column(name="mo_ta")
		*/
		private $moTa;

		/**
		* @ORM\Column(name="hinh_anh")
		*/
		private $hinhAnh;


		/**
		* @ORM\Column
		*/
		private $nhan;


		/**
		* @ORM\ManyToOne(targetEntity="S3UTaxonomy\Entity\ZfTermTaxonomy")
		* @ORM\JoinColumn(name="id_don_vi_tinh", referencedColumnName="term_taxonomy_id")
		*/
		private $idDonViTinh;


		/**
		* @ORM\ManyToOne(targetEntity="S3UTaxonomy\Entity\ZfTermTaxonomy")
		* @ORM\JoinColumn(name="id_loai", referencedColumnName="term_taxonomy_id")
		*/
		private $idLoai;


		/**
		* @ORM\Column(name="ton_kho", type="float")
		*/
		private $tonKho;

		/**
		* @ORM\Column(name="gia_nhap", type="float")
		*/
		private $giaNhap;

		/**
		 * @ORM\Column(name="loai_gia", type="integer")
		 */

		private $loaiGia;

		/**
		* @ORM\Column(name="gia_bia", type="float")
		*/
		private $giaBia;

		/**
		* @ORM\Column(name="chiec_khau", type="float")
		*/
		private $chiecKhau;

		/**
		 * @ORM\OneToMany(targetEntity="HangHoa\Entity\CTHoaDon", mappedBy="id_san_pham")
		 */
		private $ctHoaDons;

		/**
		 * @ORM\OneToMany(targetEntity="HangHoa\Entity\CTPhieuNhap", mappedBy="id_san_pham")
		 */
		private $ctPhieuNhaps;


		/**
		 * @ORM\Column(type="integer")
		 */

		private $kho;

		public function setLoaiGia($loaiGia)
		{
			$this->loaiGia=$loaiGia;
		}

		public function getLoaiGia()
		{
			return $this->loaiGia;
		}

		public function setKho($kho)
	    {
	    	$this->kho=$kho;
	    }

	    public function getKho()
	    {
	    	return $this->kho;
	    }



		public function setIdSanPham($idSanPham)
		{
			$this->idSanPham=$idSanPham;
		}
		public function getIdSanPham()
		{
			return $this->idSanPham;
		}


		public function setMaSanPham($maSanPham)
		{
			$this->maSanPham=$maSanPham;
		}
		public function getMaSanPham()
		{
			return $this->maSanPham;
		}



		public function setTenSanPham($tenSanPham)
		{
			$this->tenSanPham=$tenSanPham;
		}
		public function getTenSanPham()
		{
			return $this->tenSanPham;
		}


		public function setMoTa($moTa)
		{
			$this->moTa=$moTa;
		}
		public function getMoTa()
		{
			return $this->moTa;
		}


		public function setHinhAnh($hinhAnh)
		{
			$this->hinhAnh=$hinhAnh;
		}
		public function getHinhAnh()
		{
			return $this->hinhAnh;
		}


		public function setNhan($nhan)
		{
			$this->nhan=$nhan;
		}
		public function getNhan()
		{
			return $this->nhan;
		}


		public function setIdDonViTinh($idDonViTinh)
		{
			$this->idDonViTinh=$idDonViTinh;
		}
		public function getIdDonViTinh()
		{
			return $this->idDonViTinh;
		}

		public function getDonViTinh()
		{
			return $this->getIdDonViTinh()->getTermId()->getName();
		}


		public function setIdLoai($idLoai)
		{
			$this->idLoai=$idLoai;
		}
		public function getIdLoai()
		{
			return $this->idLoai;
		}


		public function setTonKho($tonKho)
		{
			$this->tonKho=$tonKho;
		}
		public function getTonKho()
		{
			return $this->tonKho;
		}

		public function setGiaNhap($giaNhap)
		{
			$this->giaNhap=$giaNhap;
		}
		public function getGiaNhap()
		{
			return $this->giaNhap;
		}	


		public function setGiaBia($giaBia)
		{
			$this->giaBia=$giaBia;
		}	

		public function getGiaBia()
		{
			return $this->giaBia;
		}

		public function setChiecKhau($chiecKhau)
		{
			$this->chiecKhau=$chiecKhau;
		}

		public function getChiecKhau()
		{
			return $this->chiecKhau;
		}
	}
?>
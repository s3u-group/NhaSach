<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="doi_tac")
	*/
	class DoiTac implements UserInterface, ProviderInterface {
		

		/**
		* @ORM\Column(name="id_doi_tac,type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idDoiTac;

		/**
		* @ORM\Column(name="ho_ten", length=100)
		*/
		private $hoTen;


		/**
		* @ORM\Column(name="dia_chi")
		*/
		private $diaChi;


		/**
		* @ORM\Column
		*/
		private $email;


		/**
		* @ORM\Column(type="smallint",length=6)
		*/
		private $state;


		/**
		* @ORM\Column(name="mo_ta")
		*/
		private $moTa;


		/**
		* @ORM\Column(name="dien_thoai_co_dinh", type="integer")
		*/
		private $dienThoaiCoDinh;


		/**
		* @ORM\Column(name="di_dong", type="integer")
		*/
		private $diDong;


		/**
		* @ORM\Column(name="hinh_anh")
		*/
		private $hinhAnh;


		/**
		* @ORM\Column(name="website")
		*/
		private $website;


		/**
		* @ORM\Column(name="twitter")
		*/
		private $twitter;



		// sử dụng hằng cho loại đối tác,
		/**
		* @ORM\Column(name="loai_doi_tac", type="integer")
		*/
		private $loaiDoiTac;

		const KHACH_HANG=1;
		const NHA_CUNG_CAP=2;




		public function setIdDoiTac($idDoiTac)
		{
			$this->idDoiTac=$idDoiTac;
		}
		public function getIdDoiTac()
		{
			return $this->idDoiTac;
		}


		public function setHoTen($hoTen)
		{
			$this->hoTen=$hoTen;
		}
		public function getHoTen()
		{
			return $this->hoTen;
		}


		public function setDiaChi($diaChi)
		{
			$this->diaChi=$diaChi;
		}
		public function getDiaChi()
		{
			return $this->diaChi;
		}


		public function setEmail($email)
		{
			$this->email=$email;
		}
		public function getEmail()
		{
			return $this->email;
		}


		public function setState($state)
		{
			$this->state=$state;
		}
		public function getState()
		{
			return $this->state;
		}


		public function setMoTa($moTa)
		{
			$this->moTa=$moTa;
		}
		public function getMoTa()
		{
			return $this->moTa;
		}


		public function setDienThoaiCoDinh($dienThoaiCoDinh)
		{
			$this->dienThoaiCoDinh=$dienThoaiCoDinh;
		}
		public function getDienThoaiCoDinh()
		{
			return $this->dienThoaiCoDinh;
		}


		public function setDiDong($diDong)
		{
			$this->diDong=$diDong;
		}
		public function getDiDong()
		{
			return $this->diDong;
		}


		public function setHinhAnh($hinhAnh)
		{
			$this->hinhAnh=$hinhAnh;
		}
		public function getHinhAnh()
		{
			return $this->hinhAnh;
		}


		public function setWebsite($website)
		{
			$this->website=$website;
		}
		public function getWebsite()
		{
			return $this->website;
		}


		public function setTwitter($twitter)
		{
			$this->twitter=$twitter;
		}
		public function getTwitter()
		{
			return $this->twitter;
		}


		public function setLoaiDoiTac($loaiDoiTac)
		{
			$this->loaiDoiTac=$loaiDoiTac;
		}
		public function getLoaiDoiTac()
		{
			return $this->loaiDoiTac;
		}
	}
	

?>
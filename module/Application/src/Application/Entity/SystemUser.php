<?php

	namespace Application\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="user")
	*/
	class SystemUser implements UserInterface, ProviderInterface {
		

		/**
		* @ORM\Column(name="user_id",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $id;



		/**
		* @ORM\Column
		*/
		private $username;


		/**
		* @ORM\Column(name="password",length=128)
		*/
		private $password;


		/**
		* @ORM\Column(name="display_name",length=50)
		*/
		private $displayName;


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



		// sử dụng hằng cho loại tài khoản,
		/**
		* @ORM\Column(name="loai_tai_khoan", type="integer")
		*/
		private $loaiTaiKhoan;

		const NHAN_VIEN_CUA_HANG=1;
		const NHAN_VIEN_DIEM_BAN=2;


		/**
	     * @var \Doctrine\Common\Collections\Collection
	     * @ORM\ManyToMany(targetEntity="Application\Entity\Role")
	     * @ORM\JoinTable(name="user_role_linker",
	     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
	     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	     * )
	     */
		private $roles;
		
		 public function __construct()
	    {
	        $this->roles = new ArrayCollection();
	    }
		
		public function setId($id)
		{
			$this->id=$id;

		}
		public function getId()
		{
			return $this->id;			
		}


		public function setUsername($username)
		{
			$this->username=$username;
		}

		public function getUsername()
		{
			return $this->username;
		}



		public function setPassword($password)
		{
			return $this->password=$password;
		}


		public function getPassword()
		{
			return $this->password;
		}



		public function setDisplayName($displayName)
		{
			$this->displayName=$displayName;
		}

		public function getDisplayName()
		{
			return $this->displayName;
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


		public function setState($state)
		{
			$this->state=$state;
		}
		public function getState()
		{
			return $this->state;
		}


		public function setEmail($email)
		{
			$this->email=$email;
		}
		public function getEmail()
		{
			return $this->email;
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


		public function setLoaiTaiKhoan($loaiTaiKhoan)
		{
			$this->loaiTaiKhoan=$loaiTaiKhoan;
		}
		public function getLoaiTaiKhoan()
		{
			return $this->loaiTaiKhoan;
		}


		public function getRoles()
	    {
	        return $this->roles->getValues();
	    }


		public function addRole($role)
	    {
	        $this->roles[] = $role;
	    }

		
	}
	

?>
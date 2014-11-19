<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="hoa_don")
	*/
	class HoaDon implements UserInterface, ProviderInterface {
		

		/**
		* @ORM\Column(name="id_hoa_don",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idHoaDon;


		/**
		* @ORM\Column(name="ma_hoa_don", type="char", length=6)
		*/
		private $maHoaDon;


		/**
		* @ORM\Column(name="ngay_xuat", type="date")
		*/
		private $ngayXuat;


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
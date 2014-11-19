<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="phieu_thu")
	*/
	class PhieuThu implements UserInterface, ProviderInterface {
		

		/**
		* @ORM\Column(name="id_phieu_thu",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idPhieuThu;


		/**
		* @OneToOne(targetEntity="HangHoa\Entity\CongNo")
		* @JoinColumn(name="id_cong_no", referencedColumnName="id_cong_no")
		*/
		private $idCongNo;


		/**
		* @ManyToOne(targetEntity="Application\Entity\User")
		* @JoinColumn(name="id_user_nv", referencedColumnName="user_id")
		*/
		private $idUserNv;


		/**
		* @ORM\Column(name="ly_do")
		*/
		private $lyDo;



		public function setIdPhieuThu($idPhieuThu)
		{
			$this->idPhieuThu=$idPhieuThu;
		}
		public function getIdPhieuThu()
		{
			return $this->idPhieuThu;
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
	}
	

?>
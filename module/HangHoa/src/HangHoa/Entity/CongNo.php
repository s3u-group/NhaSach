<?php

	namespace HangHoa\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="cong_no")
	*/
	class CongNo implements UserInterface, ProviderInterface {
		

		/**
		* @ORM\Column(name="id_cong_no",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idCongNo;


		/**
		* @ManyToOne(targetEntity="HangHoa\Entity\DoiTac")
		* @JoinColumn(name="id_doi_tac", referencedColumnName="id_doi_tac")
		*/
		private $idDoiTac;


		/**
		* @ORM\Column(name="ki",type="date")
		*/
		private $ki;


		/**
		* @ORM\Column(name="no_dau_ki",type="float")
		*/
		private $noDauKi;


		/**
		* @ORM\Column(name="no_phat_sinh",type="float")
		*/
		private $noPhatSinh;


		/**
		* @ORM\Column(name="cong_no_moi",type="float")
		*/
		private $congNoMoi;


		public function setIdCongNo($idCongNo)
		{
			$this->idCongNo=$idCongNo;
		}
		public function getIdCongNo()
		{
			return $this->idCongNo;
		}


		public function setIdDoiTac($idDoiTac)
		{
			$this->idDoiTac=$idDoiTac;
		}
		public function getIdDoiTac()
		{
			return $this->idDoiTac;
		}


		public function setKi($ki)
		{
			$this->ki=$ki;
		}
		public function getKi()
		{
			return $this->ki;
		}


		public function setNoDauKi($noDauKi)
		{
			$this->noDauKi=$noDauKi;
		}
		public function getNoDauKi()
		{
			return $this->noDauKi;
		}


		public function setNoPhatSinh($noPhatSinh)
		{
			$this->noPhatSinh=$noPhatSinh;
		}
		public function getNoPhatSinh()
		{
			return $this->noPhatSinh;
		}


		public function setCongNoMoi($congNoMoi)
		{
			$this->congNoMoi=$congNoMoi;
		}
		public function getCongNoMoi()
		{
			return $this->congNoMoi;
		}
	}
	

?>
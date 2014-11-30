<?php

	namespace CongNo\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="cong_no")
	*/
	class CongNo {
		

		/**
		* @ORM\Column(name="id_cong_no",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idCongNo;


		/**
		* @ORM\ManyToOne(targetEntity="HangHoa\Entity\DoiTac", cascade={"persist"})
		* @ORM\JoinColumn(name="id_doi_tac", referencedColumnName="id_doi_tac")
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
		* @ORM\Column(name="du_no",type="float")
		*/
		private $duNo;

		

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


		public function setDuNo($duNo)
		{
			$this->duNo=$duNo;
		}
		public function getDuNo()
		{
			return $this->duNo;
		}
	}
	

?>
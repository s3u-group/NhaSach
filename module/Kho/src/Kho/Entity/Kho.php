<?php

	namespace Kho\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="kho")
	*/
	class Kho {
		

		/**
		* @ORM\Column(name="id_kho",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idKho;

		

		/**
		* @ORM\Column(name="ten_kho")
		*/
		private $tenKho;


		/**
		* @ORM\Column(name="dia_chi_kho")
		*/
		private $diaChiKho;
		

		public function setIdKho($idKho)
		{
			$this->idKho=$idKho;
		}
		public function getIdKho()
		{
			return $this->idKho;
		}



		public function setTenKho($tenKho)
		{
			$this->tenKho=$tenKho;
		}

		public function getTenKho()
		{
			return $this->tenKho;
		}

		public function setDiaChiKho($diaChiKho)
		{
			$this->diaChiKho=$diaChiKho;
		}

		public function getDiaChiKho()
		{
			return $this->diaChiKho;
		}

	}
	

?>